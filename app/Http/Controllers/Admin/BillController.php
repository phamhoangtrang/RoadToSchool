<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillCourse;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BillController extends Controller
{
    /**
     * The user model instance.
     */
    protected $modelBill;

    protected $modelBillCourse;

    protected $modelCourse;

    protected $modelCourseUser;

    protected $modelProcess;

    /**
     * Create a new controller instance.
     *
     * @param  User  $users
     * @return void
     */
    public function __construct(Bill $bill, BillCourse $billCourse, Course $course, CourseUser $courseUser, Process $process)
    {
        $this->modelBill = $bill;
        $this->modelBillCourse = $billCourse;
        $this->modelCourse = $course;
        $this->modelCourseUser = $courseUser;
        $this->modelProcess = $process;
    }

    public function index()
    {
        $billsList = Bill::all();

        return view('admin.bills.index', compact('billsList'));
    }

    public function create()
    {
        return view('admin.bills.create');
    }

    public function updateStatus(Request $requestAjax)
    {
        $data = $requestAjax->validate([
            'billId' => ['required', 'integer', 'exists:bills,id'],
            'statusId' => ['required', 'integer', Rule::in(array_keys(Bill::$status))],
        ]);

        $courseIdList = DB::transaction(function () use ($data) {
            $bill = Bill::whereKey($data['billId'])->lockForUpdate()->firstOrFail();
            $courseIdList = BillCourse::where('bill_id', $bill->id)->distinct()->pluck('course_id');
            $currentStatusId = $bill->status;
            $requestedStatusId = $data['statusId'];

            if ($currentStatusId === $requestedStatusId) {
                return $courseIdList;
            }

            if ($currentStatusId === Bill::ACTIVATED && $bill->user_id) {
                foreach ($courseIdList as $courseId) {
                    $courseUser = $this->modelCourseUser
                        ->where('course_id', $courseId)
                        ->where('user_id', $bill->user_id)
                        ->first();
                    if ($courseUser) {
                        $courseUser->delete();
                        $course = $this->modelCourse->whereKey($courseId)->lockForUpdate()->firstOrFail();
                        $course->update(['seller' => max(0, $course->seller - 1)]);
                    }

                    $lectureIds = $this->modelCourse->findOrFail($courseId)->lectures()->pluck('id');
                    $this->modelProcess
                        ->where('user_id', $bill->user_id)
                        ->whereIn('lecture_id', $lectureIds)
                        ->delete();
                }
            }

            if ($requestedStatusId === Bill::ACTIVATED && $bill->user_id) {
                foreach ($courseIdList as $courseId) {
                    $courseUser = $this->modelCourseUser->firstOrCreate([
                        'user_id' => $bill->user_id,
                        'course_id' => $courseId,
                    ]);
                    if ($courseUser->wasRecentlyCreated) {
                        $course = $this->modelCourse->whereKey($courseId)->lockForUpdate()->firstOrFail();
                        $course->increment('seller');
                    }

                    $lectures = $this->modelCourse->findOrFail($courseId)->lectures()->where('is_accepted', 1)->get();
                    foreach ($lectures as $lecture) {
                        $this->modelProcess->firstOrCreate(
                            ['lecture_id' => $lecture->id, 'user_id' => $bill->user_id],
                            ['status' => 0],
                        );
                    }
                }
            }

            $bill->update(['status' => $requestedStatusId]);

            return $courseIdList;
        });

        return json_encode($courseIdList);
    }

    public function show($id)
    {
        $bill = Bill::findOrFail($id);
        $billCourses = $this->modelBillCourse->where('bill_id', $id)->get();

        return view('admin.bills.show', compact('bill', 'billCourses'));
    }
}

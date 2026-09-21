<?php

namespace App\Http\Controllers\Admin;

use App\Models\Bill;
use App\Models\BillCourse;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\Process;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
     * @param User $users
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
        $bill = Bill::findOrFail($requestAjax->billId);
        $oldStatus = $bill->status;
        $newStatus = (int)$requestAjax->statusId;
        $userId = $bill->user_id;

        $bill->update(['status' => $newStatus]);
        $courseIdList = BillCourse::where('bill_id', $bill->id)->pluck('course_id');

        $isOldActive = in_array($oldStatus, [Bill::ALREADY_PAID, Bill::ACTIVATED]);
        $isNewActive = in_array($newStatus, [Bill::ALREADY_PAID, Bill::ACTIVATED]);

        if (!$isOldActive && $isNewActive && $userId) {
            // Activate courses for student
            foreach ($courseIdList as $courseId) {
                $checkExist = $this->modelCourseUser->where('course_id', $courseId)->where('user_id', $userId)->first();
                if (!$checkExist) {
                    $this->modelCourseUser->create(['user_id' => $userId, 'course_id' => $courseId]);
                    $course = $this->modelCourse->find($courseId);
                    if ($course) {
                        $course->update(['seller' => $course->seller + 1]);
                        // Generate process progress for all accepted lectures
                        $allLectures = $course->lectures()->where('is_accepted', 1)->get();
                        foreach ($allLectures as $lecture) {
                            $this->modelProcess->firstOrCreate([
                                'lecture_id' => $lecture->id,
                                'user_id' => $userId,
                            ], [
                                'status' => 0,
                            ]);
                        }
                    }
                }
            }
        } elseif ($isOldActive && !$isNewActive && $userId) {
            // Deactivate courses if cancelled or pending
            foreach ($courseIdList as $courseId) {
                $checkExist = $this->modelCourseUser->where('course_id', $courseId)->where('user_id', $userId)->first();
                if ($checkExist) {
                    $checkExist->delete();
                    $course = $this->modelCourse->find($courseId);
                    if ($course && $course->seller > 0) {
                        $course->update(['seller' => $course->seller - 1]);
                    }
                }
            }
        }

        return response()->json($courseIdList);
    }

    public function show($id)
    {
        $bill = Bill::findOrFail($id);
        $billCourses = $this->modelBillCourse->where('bill_id', $id)->get();

        return view('admin.bills.show', compact('bill', 'billCourses'));
    }
}

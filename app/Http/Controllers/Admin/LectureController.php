<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseUser;
use App\Models\Lecture;
use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LectureController extends Controller
{
    protected $modelLecture;

    protected $modelCourseUser;

    protected $modelProcess;

    public function __construct(Lecture $lecture, CourseUser $courseUser, Process $process)
    {
        $this->modelLecture = $lecture;
        $this->modelCourseUser = $courseUser;
        $this->modelProcess = $process;
    }

    public function getRequestList()
    {
        $requestLectureList = $this->modelLecture->where('is_accepted', 0)->orderBy('updated_at', 'desc')->get();

        return view('admin.lectures.request_list', compact(
            'requestLectureList'
        ));
    }

    public function acceptLectureRequest(Request $request)
    {
        $data = $request->validate([
            'lectureId' => ['required', 'integer', 'exists:lectures,id'],
        ]);

        $wasAccepted = DB::transaction(function () use ($data): bool {
            $selectedLecture = $this->modelLecture
                ->whereKey($data['lectureId'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($selectedLecture->is_accepted) {
                return true;
            }

            $this->modelLecture
                ->where('course_id', $selectedLecture->course_id)
                ->where('week', $selectedLecture->week)
                ->whereKeyNot($selectedLecture->id)
                ->where('index', '>=', $selectedLecture->index)
                ->increment('index');
            $selectedLecture->update(['is_accepted' => 1]);

            $studentIds = $this->modelCourseUser
                ->where('course_id', $selectedLecture->course_id)
                ->distinct()
                ->pluck('user_id');
            foreach ($studentIds as $studentId) {
                $this->modelProcess->firstOrCreate(
                    ['lecture_id' => $selectedLecture->id, 'user_id' => $studentId],
                    ['status' => 0],
                );
            }

            return false;
        });

        return response()->noContent($wasAccepted ? 200 : 201);
    }
}

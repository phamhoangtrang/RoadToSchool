<?php

namespace App\Http\Controllers\User;

use App\Models\Course;
use App\Models\Lecture;
use App\Models\Process;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProcessController extends Controller
{
    protected $modelProcess;
    protected $modelLecture;
    protected $modelCourse;

    public function __construct(Process $process, Lecture $lecture, Course $course)
    {
        $this->modelProcess = $process;
        $this->modelLecture = $lecture;
        $this->modelCourse = $course;
    }

    public function changeProcessStatus(Request $request, $lectureId, $userId)
    {
        $selectedProcess = $this->modelProcess->firstOrCreate(
            ['lecture_id' => $lectureId, 'user_id' => $userId],
            ['status' => 1]
        );
        if ($selectedProcess->status == 0) {
            $selectedProcess->update(['status' => 1]);
        }

        $inCourseId = $this->modelLecture->findOrFail($lectureId)->course->id;
        $courseLectures = $this->modelCourse->findOrFail($inCourseId)->lectures()->where('is_accepted', 1)->orderBy('week')->orderBy('index')->get();
        $lastLectureId = $courseLectures->last()?->id;
        $responseData['isLastLecture'] = ($lectureId == $lastLectureId) ? 1 : 0;
        $responseData['nextLecture'] = $this->modelLecture->find($lectureId + 1);
        $responseData['inCourseId'] = $inCourseId;

        return response()->json($responseData);
    }
}

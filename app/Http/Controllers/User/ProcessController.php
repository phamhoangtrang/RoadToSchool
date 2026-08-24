<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lecture;
use App\Models\Process;
use Illuminate\Http\Request;

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
        $selectedProcess = $this->modelProcess
            ->where('lecture_id', $lectureId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
        if ($selectedProcess->status == 0) {
            $selectedProcess->update(
                [
                    'status' => 1,
                ]
            );
        }

        $lecture = $this->modelLecture->findOrFail($lectureId);
        $inCourseId = $lecture->course_id;
        $outline = $this->modelCourse->findOrFail($inCourseId)
            ->lectures()
            ->where('is_accepted', 1)
            ->orderBy('week')
            ->orderBy('index')
            ->get();
        $currentOffset = $outline->search(fn (Lecture $item): bool => $item->is($lecture));
        $nextLecture = $currentOffset === false ? null : $outline->get($currentOffset + 1);
        $responseData['isLastLecture'] = $nextLecture === null ? 1 : 0;
        $responseData['nextLecture'] = $nextLecture;
        $responseData['inCourseId'] = $inCourseId;

        //        $currentLectureId = $lectureId + 1;
        //        if($lectureId)
        //
        return json_encode($responseData);
    }
}

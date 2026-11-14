<?php

namespace App\Http\Controllers\Admin;

use App\Models\CourseUser;
use App\Models\Lecture;
use App\Models\Process;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $data = $request->all();

        $lectureId = $data['lectureId'];
        $selectedLecture = $this->modelLecture->findOrFail($lectureId);
        $selectedLecture->update(['is_accepted' => 1]);
        $week = $selectedLecture->week;
        $currentIndex = $selectedLecture->index;
        $sameWeekLectureList = $this->modelLecture->where('course_id', $selectedLecture->course_id)->where('week', $week)->get();
        foreach($sameWeekLectureList as $lecture) {
            if($lecture->id != $lectureId && $lecture->index >= $currentIndex) {
                $lecture->update(['index' => ($lecture->index + 1)]);
            }
        }

        // All user add process of new lecture
        $studentList = $this->modelCourseUser->where('course_id', $selectedLecture->course_id)->pluck('user_id')->toArray();
        foreach($studentList as $studentId) {
            $createProcessData['lecture_id'] = $lectureId;
            $createProcessData['user_id'] = $studentId;
            $createProcessData['status'] = 0;
            $this->modelProcess->create($createProcessData);
        }

        return 201;
    }

    public function deleteLectureRequest(Request $request)
    {
        try {
            $lectureId = $request->input('lectureId');
            $selectedLecture = $this->modelLecture->findOrFail($lectureId);

            // Xóa dọn dẹp tiến trình, bình luận, câu hỏi quiz con
            \App\Models\Process::where('lecture_id', $lectureId)->delete();
            \App\Models\LectureComment::where('lecture_id', $lectureId)->delete();
            \App\Models\QuizElement::where('lecture_id', $lectureId)->delete();

            $selectedLecture->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Đã từ chối và xóa nội dung bài học thành công.',
                'id' => $lectureId
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi xóa bài học: ' . $e->getMessage()
            ], 500);
        }
    }
}

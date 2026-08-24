<?php

namespace App\Http\Controllers\User;

use App\Events\GetLectureCommentFromPusherEvent;
use App\Events\GetNotificationFromPusherEvent;
use App\Events\GetReplyLectureCommentFromPusherEvent;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Discussion;
use App\Models\Lecture;
use App\Models\LectureComment;
use App\Models\Notification;
use App\Models\Process;
use App\Models\QuizElement;
use App\Models\QuizElementzQuizResult;
use App\Models\QuizResult;
use App\Services\YouTubeMetadataService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LectureController extends Controller
{
    // protected $modelLecture;
    protected $modelDiscussion;

    protected $modelLectureComment;

    protected $modelNotification;

    protected $modelProcess;

    protected $modelLecture;

    protected $modelQuizElement;

    protected $modelQuizResult;

    protected $modelQuizElementzQuizResult;

    protected $youtubeMetadata;

    public function __construct(
        Discussion $discussion,
        LectureComment $lectureComment,
        Notification $notification,
        Process $process,
        Lecture $lecture,
        QuizElement $quizElement,
        QuizResult $quizResult,
        QuizElementzQuizResult $quizElementzQuizResult,
        YouTubeMetadataService $youtubeMetadata,
    ) {
        $this->modelDiscussion = $discussion;
        $this->modelLectureComment = $lectureComment;
        $this->modelNotification = $notification;
        $this->modelProcess = $process;
        $this->modelLecture = $lecture;
        $this->modelQuizElement = $quizElement;
        $this->modelQuizResult = $quizResult;
        $this->modelQuizElementzQuizResult = $quizElementzQuizResult;
        $this->youtubeMetadata = $youtubeMetadata;
    }

    public function show($id, $lectureId)
    {
        $course = Course::findOrFail($id);
        $lecture = $course->lectures()->where('is_accepted', 1)->findOrFail($lectureId);

        if ($lecture->is_quiz == 1) {
            $allQuestion = $this->modelQuizElement->where('lecture_id', $lectureId)->where('is_question', 1)->get();
            foreach ($allQuestion as $question) {
                // TODO xu ly cau hoi nhieu dap an va 1 dap an, pick dap an dung cho phu hop
                $answer = $this->modelQuizElement->where('lecture_id', $lectureId)->where('is_answer', 1)->where('question_parent_id', $question->id)->get();
                $question->answer = $answer;
            }

            return view('user.quiz_elements.index', compact(
                'lecture',
                'allQuestion'
            ));
        } else {
            $link = $lecture->video_link;
            $description = $lecture->description;
            $teacher = $course->user;
            $embedHtml = $this->youtubeMetadata->embedHtml($link);
            $lectures = $course->lectures()->where('is_accepted', 1)->orderBy('week')->orderBy('index')->get();
            $lectureComments = $this->modelLectureComment->where('lecture_id', $lectureId)->get();
            $processStatuses = $this->modelProcess
                ->where('user_id', \Auth::id())
                ->whereIn('lecture_id', $lectures->pluck('id'))
                ->pluck('status', 'lecture_id');

            // Get lecture follow week and index
            $maxWeek = 0;
            foreach ($lectures as $lecture) {
                if ($lecture->week > $maxWeek) {
                    $maxWeek = $lecture->week;
                }
            }

            $lectureOutline = [];
            for ($i = 0; $i < $maxWeek; $i++) {
                $result = $this->modelLecture->where('course_id', $id)->where('is_accepted', 1)->where('week', ($i + 1))->orderBy('index')->get();
                foreach ($result as $outlineLecture) {
                    $outlineLecture->status = (bool) $processStatuses->get($outlineLecture->id, 0);
                }
                $lectureOutline[$i] = $result;

            }
            // dd($lectureOutline);

            // if (!(\Auth::user()->is_admin || \Auth::user()->role == 1)) {
            // Get process
            $allLectureCount = $lectures->count();
            $learnedLectureCount = $processStatuses->filter()->count();
            $progressPercent = $allLectureCount > 0
                ? round($learnedLectureCount / $allLectureCount * 100, 2)
                : 0;
            $currentOffset = $lectures->search(fn (Lecture $outlineLecture): bool => $outlineLecture->is($lecture));
            $nextLecture = $currentOffset === false ? null : $lectures->get($currentOffset + 1);
            // }

            // Get all discussions in lecture
            $discussionsList = $this->modelDiscussion->where('lecture_id', $lectureId)->orderBy('created_at')->get();
            // Get all child lecture comment set to collection lecture comment
            foreach ($lectureComments as $lectureComment) {
                $lectureComment->child_comments = $this->modelLectureComment->where('parent_comment', $lectureComment->id)->orderBy('updated_at', 'asc')->get();
            }

            return view('user.lectures.show', compact(
                'embedHtml',
                'lectures',
                'id',
                'description',
                'teacher',
                'lectureId',
                'discussionsList',
                'lectureComments',
                'allLectureCount',
                'learnedLectureCount',
                'progressPercent',
                'nextLecture',
                'maxWeek',
                'lectureOutline'
            ));
        }
    }

    public function postCommentToPusher(Request $request, $lectureId)
    {
        $data = $request->validate(['content' => ['required', 'string', 'max:255']]);
        $this->modelLecture->findOrFail($lectureId);
        $data['content'] = e($data['content']);
        $data['user_id'] = $request->user()->id;
        $data['lecture_id'] = $lectureId;

        $createdLectureComment = $this->modelLectureComment->storeNewLectureComment($data);
        $lectureCommentedUserList = $this->modelLectureComment->where('lecture_id', $lectureId)->whereNull('parent_comment')->where('user_id', '!=', $data['user_id'])->groupBy('user_id')->pluck('user_id');
        // Create Notification
        $createdNotification = $this->modelNotification->createCommentNotification($lectureId, $lectureCommentedUserList, $createdLectureComment, Notification::LECTURE_COMMENT);
        $notificationContent = '<b>'.e($createdLectureComment->user->name).'</b>'.' has commented in lecture '.e(Lecture::findOrFail($lectureId)->title).': '.$createdLectureComment->content;
        $createNotificationIdList = $this->modelNotification->where('comment_id', $createdLectureComment->id)->pluck('id', 'user_id');

        if ($createdLectureComment && $createdNotification) {
            event(new GetLectureCommentFromPusherEvent($request, $createdLectureComment));
            event(new GetNotificationFromPusherEvent($lectureId, $lectureCommentedUserList, $createdLectureComment, $notificationContent, $createdLectureComment->user->avatar, Carbon::parse($createdLectureComment->created_at)->diffForHumans(), $createNotificationIdList));

            return 200;
        }

        return 500;
    }

    public function postReplyLectureCommentToPusher(Request $request, $lectureId, $parentCommentId)
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:255'],
            'firstChildComment' => ['nullable'],
            'prevCommentId' => ['nullable', 'integer'],
        ]);
        $this->modelLecture->findOrFail($lectureId);
        $this->modelLectureComment->where('lecture_id', $lectureId)->findOrFail($parentCommentId);
        $data['content'] = e($data['content']);
        $data['user_id'] = $request->user()->id;
        $data['lecture_id'] = $lectureId;
        $data['parent_comment'] = $parentCommentId;

        $createdComment = $this->modelLectureComment->storeNewLectureComment($data);
        //        $commentedUserList = $this->modelComment->where('user_id', '!=', $data['user_id'])->where('course_id', $courseId)->where('parent_comment', $parentCommentId)->groupBy('user_id')->pluck('user_id');
        //        $createdNotification = $this->modelNotification->createCommentNotification($courseId, $commentedUserList, $createdComment, 'replied');
        //        if($createdComment && $createdNotification) {
        if ($createdComment) {
            event(new GetReplyLectureCommentFromPusherEvent($request, $createdComment, $parentCommentId));

            if (! $request->boolean('firstChildComment')) {
                $responseData['prevCommentId'] = $data['prevCommentId'] ?? null;
                $responseData['parentCommentId'] = $parentCommentId;

                return json_encode($responseData);
            }

            return json_encode($parentCommentId);
        }

        return 500;
    }

    public function showQuizResult($courseId, $lectureId)
    {
        $lecture = $this->modelLecture->findOrFail($lectureId);
        $allQuestion = $this->modelQuizElement->where('lecture_id', $lectureId)->where('is_question', 1)->get();
        $quizResult = $this->modelQuizResult
            ->where('lecture_id', $lectureId)
            ->where('user_id', \Auth::user()->id)
            ->latest('id')
            ->firstOrFail();
        foreach ($allQuestion as $question) {
            // TODO xu ly cau hoi nhieu dap an va 1 dap an, pick dap an dung cho phu hop
            $answer = $this->modelQuizElement->where('lecture_id', $lectureId)->where('question_parent_id', $question->id)->get();
            $question->answer = $answer;
            $question->trueAnswer = $this->modelQuizElement
                ->where('question_parent_id', $question->id)
                ->where('is_right_answer', 1)
                ->pluck('id')->toArray();
            $userChoice = $this->modelQuizElementzQuizResult
                ->where('quiz_element_id', $question->id)
                ->where('quiz_result_id', $quizResult->id)
                ->firstOrFail()
                ->user_choice;
            $question->userChoice = $userChoice
                ? array_map('intval', explode(',', $userChoice))
                : [];

            if ($question->trueAnswer == $question->userChoice) {
                $question->checkResult = 1;
            } else {
                $question->checkResult = 0;
            }
        }

        return view('user.quiz_elements.check', compact(
            'lecture',
            'allQuestion',
            'quizResult'
        ));
    }
}

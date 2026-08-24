<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CourseUser;
use App\Models\Lecture;
use App\Models\QuizElement;
use App\Models\QuizElementzQuizResult;
use App\Models\QuizResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizResultController extends Controller
{
    protected $modelQuizResult;

    protected $modelQuizElement;

    protected $modelQuizElementQuizResult;

    protected $modelLecture;

    public function __construct(QuizResult $quizResult, QuizElement $quizElement, QuizElementzQuizResult $quizElementzQuizResult, Lecture $lecture)
    {
        $this->modelQuizResult = $quizResult;
        $this->modelQuizElement = $quizElement;
        $this->modelQuizElementQuizResult = $quizElementzQuizResult;
        $this->modelLecture = $lecture;
    }

    public function store(Request $request)
    {
        $data = $request->validate(['quizResultId' => ['required', 'integer']]);
        $quizResult = $this->modelQuizResult
            ->where('user_id', $request->user()->id)
            ->findOrFail($data['quizResultId']);
        $courseId = $this->modelLecture->findOrFail($quizResult->lecture_id)->course_id;

        $quizElementArray = $this->modelQuizElement->where('lecture_id', $quizResult->lecture_id)
            ->where('is_question', 1)
            ->get();

        $rightAnswer = 0;

        foreach ($quizElementArray as $quizElement) {
            $selected = $this->modelQuizElementQuizResult
                ->where('quiz_element_id', $quizElement->id)
                ->where('quiz_result_id', $quizResult->id)
                ->firstOrFail();

            $answerList = $this->modelQuizElement
                ->where('is_answer', 1)
                ->where('question_parent_id', $quizElement->id)
                ->get();
            $userChoice = [];
            foreach ($answerList as $answer) {
                if ($request->has('answer-'.$answer->id)) {
                    array_push($userChoice, $answer->id);
                }
            }
            $userChoiceStr = implode(', ', $userChoice);

            $selected->update(['user_choice' => $userChoiceStr]);

            $keyList = $answerList->where('is_right_answer', 1)->pluck('id')->values()->all();
            if ($userChoice === $keyList) {
                $rightAnswer++;
            }
        }

        $quizResult->update([
            'right_answer_count' => $rightAnswer,
            'wrong_answer_count' => $quizElementArray->count() - $rightAnswer,
        ]);

        return redirect()->route('quiz.result', [$courseId, $quizResult->lecture_id]);
    }

    public function storeNewResult(Request $request)
    {
        $data = $request->validate([
            'lectureId' => ['required', 'integer', 'exists:lectures,id'],
        ]);
        $lecture = $this->modelLecture->where('is_quiz', 1)->findOrFail($data['lectureId']);
        abort_unless(CourseUser::where([
            'course_id' => $lecture->course_id,
            'user_id' => $request->user()->id,
        ])->exists(), 403);
        abort_if($this->modelQuizResult->where([
            'lecture_id' => $lecture->id,
            'user_id' => $request->user()->id,
        ])->exists(), 409, 'This quiz has already been started.');

        $createdQuizResult = DB::transaction(function () use ($lecture, $request) {
            $questions = $this->modelQuizElement
                ->where('lecture_id', $lecture->id)
                ->where('is_question', 1)
                ->get();
            $quizResult = $this->modelQuizResult->create([
                'lecture_id' => $lecture->id,
                'user_id' => $request->user()->id,
                'right_answer_count' => 0,
                'wrong_answer_count' => $questions->count(),
            ]);
            foreach ($questions as $question) {
                $this->modelQuizElementQuizResult->create([
                    'quiz_element_id' => $question->id,
                    'quiz_result_id' => $quizResult->id,
                ]);
            }

            return $quizResult;
        });

        return json_encode($createdQuizResult->id);
    }
}

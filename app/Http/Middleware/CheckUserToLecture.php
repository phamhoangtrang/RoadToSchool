<?php

namespace App\Http\Middleware;

use App\Models\CourseUser;
use App\Models\Lecture;
use App\Models\QuizResult;
use Closure;
use Illuminate\Http\Request;

class CheckUserToLecture
{
    protected $modelCourseUser;

    protected $modelLecture;

    protected $modelQuizResult;

    public function __construct(CourseUser $courseUser, Lecture $lecture, QuizResult $quizResult)
    {
        $this->modelCourseUser = $courseUser;
        $this->modelLecture = $lecture;
        $this->modelQuizResult = $quizResult;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $routeParams = $request->route()->parameters();
        $courseId = $routeParams['id'];
        $lecture = $this->modelLecture
            ->where('course_id', $courseId)
            ->findOrFail($routeParams['lectureId']);

        if ($request->user()->is_admin || $request->user()->role == 1) {
            return $next($request);
        }

        if ($this->modelCourseUser->where('course_id', $courseId)->where('user_id', $request->user()->id)->get()->isEmpty()) {
            abort(403, 'Sorry! You do not have access to this course.');
        }

        if ($request->route()->getName() !== 'quiz.result' && $lecture->is_quiz) {
            $hasResult = $this->modelQuizResult
                ->where('lecture_id', $lecture->id)
                ->where('user_id', $request->user()->id)
                ->exists();
            if ($hasResult) {
                abort(404, 'You already completed this quiz.');
            }
        }

        return $next($request);
    }
}

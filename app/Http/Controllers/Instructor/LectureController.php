<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lecture;
use App\Models\QuizElement;
use App\Models\User;
use App\Services\YouTubeMetadataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Throwable;

class LectureController extends Controller
{
    /**
     * The dependency model instance.
     */
    protected $modelCourse;

    protected $modelLecture;

    protected $modelQuizElement;

    protected $youtubeMetadata;

    /**
     * Create a new controller instance.
     *
     * @param  Category  $category
     * @return void
     */
    public function __construct(
        Course $course,
        Lecture $lecture,
        QuizElement $quizElement,
        YouTubeMetadataService $youtubeMetadata,
    ) {
        $this->modelCourse = $course;
        $this->modelLecture = $lecture;
        $this->modelQuizElement = $quizElement;
        $this->youtubeMetadata = $youtubeMetadata;
    }

    public function create($courseId)
    {
        $selectedCourse = $this->modelCourse
            ->whereKey($courseId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        $allLectures = $selectedCourse->lectures;

        // Get lecture follow week and index
        $maxWeek = 0;
        foreach ($allLectures as $lecture) {
            if ($lecture->week > $maxWeek) {
                $maxWeek = $lecture->week;
            }
        }

        $lectureOutline = [];
        for ($i = 0; $i < $maxWeek; $i++) {
            $lectureOutline[$i] = $this->modelLecture->where('course_id', $courseId)->where('week', ($i + 1))->orderBy('index')->get();
        }

        return view('instructor.lectures.create', compact(
            'selectedCourse',
            'maxWeek',
            'lectureOutline'
        ));
    }

    public function getVideoDuration(Request $request)
    {
        abort_unless($request->user()->is_admin || $request->user()->role === User::ROLE_TEACHER, 403);
        $data = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
        ]);

        try {
            return response()->json($this->youtubeMetadata->metadata($data['url']));
        } catch (InvalidArgumentException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to read this YouTube video. Please check the URL and try again.',
            ], 422);
        }
    }

    public function store(Request $request, $courseId)
    {
        $isLecture = $request->exists('video_link');
        $positionField = $isLecture ? 'positionValue' : 'quizPositionValue';
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:50000'],
            'duration' => ['required', 'string', 'regex:/^(?:[0-5]?\d:[0-5]\d|\d{1,2}:[0-5]\d:[0-5]\d)$/'],
            'week' => ['required', 'integer', 'min:0'],
            $positionField => ['required', 'integer', 'min:-1'],
        ];

        if ($isLecture) {
            $rules['video_link'] = ['required', 'url:http,https', 'max:2048'];
        } else {
            $rules['question'] = ['required', 'array', 'min:1'];
            $rules['question.*'] = ['nullable', 'string', 'max:50000'];
        }

        $data = $request->validate($rules);
        $data['duration'] = $this->normalizeDuration($data['duration']);

        DB::transaction(function () use ($request, $courseId, $data, $isLecture, $positionField): void {
            $course = $this->modelCourse
                ->whereKey($courseId)
                ->where('user_id', $request->user()->id)
                ->lockForUpdate()
                ->firstOrFail();

            [$week, $index] = $this->resolvePosition(
                $course,
                (int) $data[$positionField],
                (int) $data['week'],
            );

            $createdLecture = $this->modelLecture->create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'video_link' => $isLecture ? $data['video_link'] : null,
                'duration' => $data['duration'],
                'week' => $week,
                'index' => $index,
                'is_accepted' => 0,
                'course_id' => $course->id,
                'is_lecture' => $isLecture,
                'is_quiz' => ! $isLecture,
            ]);

            if (! $isLecture) {
                $this->createQuizElements($request, $createdLecture);
            }
        });

        flash(__($isLecture
            ? 'messages.create_lecture_successfully'
            : 'messages.create_quiz_successfully'))->success();

        return redirect()->route('instructor.courses.show', $courseId);
    }

    /** @return array{int, int} */
    private function resolvePosition(Course $course, int $position, int $requestedWeek): array
    {
        if ($position === 0) {
            $course->lectures()->where('week', 1)->increment('index');

            return [1, 0];
        }

        if ($position === -1) {
            $nextWeek = ((int) $course->lectures()->max('week')) + 1;

            if ($requestedWeek !== $nextWeek) {
                throw ValidationException::withMessages([
                    'week' => "The next available week is {$nextWeek}.",
                ]);
            }

            return [$nextWeek, 0];
        }

        $previousLecture = $course->lectures()
            ->whereKey($position)
            ->lockForUpdate()
            ->firstOrFail();
        $index = $previousLecture->index + 1;

        $course->lectures()
            ->where('week', $previousLecture->week)
            ->where('index', '>=', $index)
            ->increment('index');

        return [$previousLecture->week, $index];
    }

    private function createQuizElements(Request $request, Lecture $lecture): void
    {
        $questions = collect($request->input('question', []))
            ->filter(fn ($question): bool => filled($question));

        if ($questions->isEmpty()) {
            throw ValidationException::withMessages(['question' => 'Add at least one quiz question.']);
        }

        foreach ($questions as $offset => $content) {
            $questionIndex = $offset + 1;
            $multipleField = "is_multiple_choice_question_{$questionIndex}";
            $singleField = "is_single_choice_question_{$questionIndex}";
            $isMultiple = $request->exists($multipleField);
            $isSingle = $request->exists($singleField);

            if ($isMultiple === $isSingle) {
                throw ValidationException::withMessages([
                    "question.{$offset}" => 'Choose one question type.',
                ]);
            }

            $answers = [];
            $rightAnswerCount = 0;

            for ($answerIndex = 1; $answerIndex <= 4; $answerIndex++) {
                $answerField = "question_{$questionIndex}_answer_{$answerIndex}";
                $rightField = "checkbox_question_{$questionIndex}_answer_{$answerIndex}";
                $answer = $request->input($answerField);

                if (! is_string($answer) || trim($answer) === '') {
                    throw ValidationException::withMessages([
                        $answerField => 'All four answers are required.',
                    ]);
                }

                $isRightAnswer = $request->boolean($rightField);
                $rightAnswerCount += (int) $isRightAnswer;
                $answers[] = [trim($answer), $isRightAnswer];
            }

            if ($rightAnswerCount === 0 || ($isSingle && $rightAnswerCount !== 1)) {
                throw ValidationException::withMessages([
                    "question.{$offset}" => $isSingle
                        ? 'A single-choice question must have exactly one right answer.'
                        : 'Choose at least one right answer.',
                ]);
            }

            $question = $this->modelQuizElement->create([
                'content' => trim($content),
                'is_question' => 1,
                'is_multiple_choice' => $isMultiple,
                'is_answer' => 0,
                'lecture_id' => $lecture->id,
            ]);

            foreach ($answers as [$answer, $isRightAnswer]) {
                $this->modelQuizElement->create([
                    'content' => $answer,
                    'is_question' => 0,
                    'is_answer' => 1,
                    'question_parent_id' => $question->id,
                    'is_right_answer' => $isRightAnswer,
                    'lecture_id' => $lecture->id,
                ]);
            }
        }
    }

    private function normalizeDuration(string $duration): string
    {
        $parts = array_map('intval', explode(':', $duration));

        if (count($parts) === 2) {
            array_unshift($parts, 0);
        }

        return sprintf('%02d:%02d:%02d', ...$parts);
    }
}

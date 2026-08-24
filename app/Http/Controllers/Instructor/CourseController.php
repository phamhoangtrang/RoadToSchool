<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateInstructorCourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\Lecture;
use App\Models\Process;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CourseController extends Controller
{
    /**
     * The dependency model instance.
     */
    protected $modelCourse;

    protected $modelLecture;

    protected $modelCategory;

    protected $modelCourseUser;

    protected $modelUser;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Course $course, Lecture $lecture, Category $category, CourseUser $courseUser, User $user)
    {
        $this->modelCourse = $course;
        $this->modelLecture = $lecture;
        $this->modelCategory = $category;
        $this->modelCourseUser = $courseUser;
    }

    public function index()
    {
        $instructorCourseList = $this->modelCourse->where('user_id', Auth::user()->id)->get();

        return view('instructor.courses.index', compact(
            'instructorCourseList'
        ));
    }

    public function show($id)
    {
        $selectedCourse = $this->modelCourse
            ->whereKey($id)
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
            $lectureOutline[$i] = $this->modelLecture->where('course_id', $id)->where('week', ($i + 1))->orderBy('index')->get();
        }

        $studentIdList = $this->modelCourseUser->where('course_id', $id)->pluck('user_id');
        $studentList = User::whereIn('id', $studentIdList)->get();
        foreach ($studentList as $student) {
            $processList = Process::whereIn('lecture_id', $selectedCourse->lectures()->pluck('id')->toArray())->where('user_id', $student->id);
            $learnedLectureCount = $processList->where('status', 1)->count();
            $allLectureCount = Process::whereIn('lecture_id', $selectedCourse->lectures()->pluck('id')->toArray())->where('user_id', $student->id)->count();
            if ($allLectureCount == 0) {
                $student->progress = 0;
            } else {
                $student->progress = round($learnedLectureCount / $allLectureCount * 100, 2);
            }
            $student->enrollCourseTime = $this->modelCourseUser->where('course_id', $id)->where('user_id', $student->id)->first()->created_at;
        }

        return view('instructor.courses.show', compact(
            'selectedCourse',
            'maxWeek',
            'lectureOutline',
            'studentList'
        ));
    }

    public function create()
    {
        $parentCategoryList = $this->modelCategory->where('parent_id', 0)->get();
        foreach ($parentCategoryList as $parentCategory) {
            $parentCategory->childCategory = $this->modelCategory->where('parent_id', $parentCategory->id)->get();
        }

        return view('instructor.courses.create', compact(
            'parentCategoryList'
        ));
    }

    public function store(CreateInstructorCourseRequest $request)
    {
        $data = $request->safe()->except(['course_avatar', 'course_avatar_2', 'course_avatar_3']);
        $storedImages = [];

        try {
            foreach (['course_avatar', 'course_avatar_2', 'course_avatar_3'] as $field) {
                $storedImages[] = $request->file($field)->store('', 'course_images');
                $data[$field] = 'public/images/course_avatar/'.end($storedImages);
            }

            $this->modelCourse->create([
                ...$data,
                'origin_price' => 0,
                'lecture_numbers' => 0,
                'duration' => 0,
                'seller' => 0,
                'course_rate' => 0,
                'is_accepted' => 0,
                'user_id' => $request->user()->id,
            ]);
            flash(__('messages.create_course_successfully'))->success();
        } catch (Throwable $exception) {
            Storage::disk('course_images')->delete($storedImages);

            throw $exception;
        }

        return redirect()->route('instructor.courses.index');
    }
}

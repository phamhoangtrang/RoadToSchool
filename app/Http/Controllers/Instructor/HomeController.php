<?php

namespace App\Http\Controllers\Instructor;

use App\Models\Bill;
use App\Models\BillCourse;
use App\Models\CourseUser;
use App\Models\Course;
use App\Models\Comment;
use App\Models\LectureComment;
use App\Models\Lecture;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * The user repository instance.
     */
    protected $modelCourseUser;
    protected $modelCourse;
    protected $modelComment;
    protected $modelLectureComment;
    protected $modelLecture;

    /**
     * Create a new controller instance.
     *
     * @param CourseUser $courseUser
     * @param Course $course
     * @param Comment $comment
     * @param LectureComment $lectureComment
     * @param Lecture $lecture
     * @return void
     */
    public function __construct(CourseUser $courseUser, Course $course, Comment $comment, LectureComment $lectureComment, Lecture $lecture)
    {
        $this->modelCourseUser = $courseUser;
        $this->modelCourse = $course;
        $this->modelComment = $comment;
        $this->modelLectureComment = $lectureComment;
        $this->modelLecture = $lecture;
    }

    public function index()
    {
        $instructorId = \Auth::user()->id;

        $instructorCourseListQuery = $this->modelCourse->with('category')->where('user_id', $instructorId);
        $newestInstructorCourseList = (clone $instructorCourseListQuery)->orderBy('updated_at', 'desc')->limit(8)->get();
        $instructorCourseIdList = (clone $instructorCourseListQuery)->pluck('id')->toArray();

        $totalCourses = count($instructorCourseIdList);
        $acceptedCourses = (clone $instructorCourseListQuery)->where('is_accepted', 1)->count();
        $pendingCourses = (clone $instructorCourseListQuery)->where('is_accepted', 0)->count();
        $totalStudents = $this->modelCourseUser->whereIn('course_id', $instructorCourseIdList)->count();
        $totalSales = (clone $instructorCourseListQuery)->sum('seller');

        // Tính doanh thu và thu nhập thực nhận (70% cho giảng viên, 30% phí nền tảng)
        $paidStatus = [Bill::ALREADY_PAID, Bill::ACTIVATED];
        $totalRevenue = (float) BillCourse::join('bills', 'bills.id', '=', 'bill_course.bill_id')
            ->whereIn('bills.status', $paidStatus)
            ->whereIn('bill_course.course_id', $instructorCourseIdList)
            ->sum('bill_course.price');

        if ($totalRevenue == 0 && $totalSales > 0) {
            $courses = (clone $instructorCourseListQuery)->get();
            foreach ($courses as $c) {
                $effectivePrice = ($c->promotion_price > 0 && $c->promotion_price < $c->origin_price)
                    ? $c->promotion_price
                    : $c->origin_price;
                $totalRevenue += ($c->seller * $effectivePrice);
            }
        }

        $instructorIncome = round($totalRevenue * 0.70);
        $platformFee = $totalRevenue - $instructorIncome;

        foreach ($newestInstructorCourseList as $course) {
            $course['student_count'] = $this->modelCourseUser->where('course_id', $course->id)->count();

            $cRevenue = (float) BillCourse::join('bills', 'bills.id', '=', 'bill_course.bill_id')
                ->whereIn('bills.status', $paidStatus)
                ->where('bill_course.course_id', $course->id)
                ->sum('bill_course.price');

            if ($cRevenue == 0 && $course->seller > 0) {
                $effectivePrice = ($course->promotion_price > 0 && $course->promotion_price < $course->origin_price)
                    ? $course->promotion_price
                    : $course->origin_price;
                $cRevenue = $course->seller * $effectivePrice;
            }

            $course['total_revenue'] = $cRevenue;
            $course['instructor_income'] = round($cRevenue * 0.70);
        }

        $commentInCourse = $this->modelComment->with(['user', 'course'])
            ->whereIn('course_id', $instructorCourseIdList)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        $lectureListInCourse = $this->modelLecture->whereIn('course_id', $instructorCourseIdList)->pluck('id')->toArray();
        $commentInLecture = $this->modelLectureComment->with(['user', 'lecture.course'])
            ->whereIn('lecture_id', $lectureListInCourse)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        $totalComments = $this->modelComment->whereIn('course_id', $instructorCourseIdList)->count()
            + $this->modelLectureComment->whereIn('lecture_id', $lectureListInCourse)->count();

        return view('instructor.home', compact(
            'newestInstructorCourseList',
            'commentInCourse',
            'commentInLecture',
            'totalCourses',
            'acceptedCourses',
            'pendingCourses',
            'totalStudents',
            'totalSales',
            'totalRevenue',
            'instructorIncome',
            'platformFee',
            'totalComments'
        ));
    }
}

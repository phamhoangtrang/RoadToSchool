<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Course;

class CourseController extends Controller
{
    protected $modelCourse;

    public function __construct(Course $course)
    {
        $this->modelCourse = $course;
    }

    public function index()
    {
        $courses = Course::all();

        return view('admin.courses.index', compact('courses'));
    }

    public function acceptCourse(Request $request, $courseId)
    {
        $selectedCourse = $this->modelCourse->findOrFail($courseId);
        $selectedCourse->update(['is_accepted' => 1]);

        return response()->json($selectedCourse);
    }

    public function destroy($id)
    {
        $course = $this->modelCourse->findOrFail($id);
        $course->delete();

        flash(__('delete status').$course->id)->success();

        return redirect()->route('admin.courses.index');
    }
}

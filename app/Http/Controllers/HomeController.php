<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\CourseRecommendationService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(private readonly CourseRecommendationService $recommendations)
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        $courseIds = $this->recommendations->courseIdsFor(Auth::id());
        $coursesById = Course::whereIn('id', $courseIds)->get()->keyBy('id');
        $recommmendCourseList = collect($courseIds)
            ->map(fn ($courseId) => $coursesById->get($courseId))
            ->filter()
            ->values();

        return view('home', compact(
            'recommmendCourseList'
        ));
    }

    public function changeLanguage($language)
    {
        \Session::put('website_language', $language);
        \Session::save();

        return redirect()->back();
    }

    //    public function export()
    //    {
    //        $people = Person::all();
    //
    //        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
    //
    //        $csv->insertOne(\Schema::getColumnListing('people'));
    //
    //        foreach ($people as $person) {
    //            $csv->insertOne($person->toArray());
    //        }
    //
    //        $csv->output('people.csv');
    //    }
}

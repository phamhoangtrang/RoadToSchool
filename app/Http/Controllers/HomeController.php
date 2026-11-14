<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $recommmendCourseList = [];
        try {
            $currentUserId = \Auth::id();
            $client = new \GuzzleHttp\Client(['timeout' => 0.8]);
            $res = $client->post(
                'http://127.0.0.1:5000/r2s/v1/recommend/itemstouser',
                array(
                    'form_params' => array(
                        'appId' => 1,
                        'userId' => $currentUserId ?: 98,
                        'count' => 10,
                    )
                )
            );
            $key = strval($currentUserId ?: 98);
            $recommendCourseIdList = json_decode($res->getBody()->getContents())->$key;
            $recommmendCourseList = \App\Models\Course::whereIn('id', $recommendCourseIdList)->where('is_accepted', 1)->get();
        } catch (\Exception $e) {
            $recommmendCourseList = [];
        }

        // Fallback thông minh: nếu API gợi ý chưa khả dụng, lấy các khóa học đánh giá cao nhất
        if (empty($recommmendCourseList) || $recommmendCourseList->isEmpty()) {
            $recommmendCourseList = \App\Models\Course::where('is_accepted', 1)
                ->orderBy('course_rate', 'desc')
                ->orderBy('seller', 'desc')
                ->limit(10)
                ->get();
        }

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

<?php

namespace App\Http\Controllers\Admin;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Http\Requests\CreateInstructorRequest;

class UserController extends Controller
{
    /**
     * The user repository instance.
     */
    protected $modelUser;

    /**
     * Create a new controller instance.
     *
     * @param User $users
     * @return void
     */
    public function __construct(User $user)
    {
        $this->modelUser = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $specializesName = 'None';

        $countStudent = 0;
        if ($user->courses) {
            foreach ($user->courses as $course) {
                $countStudent += $course->users ? $course->users->count() : 0;
            }
        }

        return view('admin.users.show', compact('user', 'countStudent', 'specializesName'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request, $id)
    {
        User::find($id)->update($request->all());

        return User::find('id');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->modelUser->deleteUser($id);

        if ($result) {
            flash(__('delete status') . $id)->success();
        } else {
            flash(__('something wrong'))->error();
        }

        return redirect(route('admin.users.index'));
    }

    public function getInstructorRanking()
    {
        $updateResult = $this->modelUser->updateInstructorRate();
        if ($updateResult) {
            $orderRankingInstructors = $this->modelUser->where('role', 1)->orderBy('instructor_rate', 'desc')->get();
            foreach ($orderRankingInstructors as $key => $instructor) {
                $instructor->ranking = $key + 1;
                $instructor->coursesCount = Course::where('is_accepted', 1)->where('user_id', $instructor->id)->get()->count();
                $instructor->studentsCount = $this->modelUser->getStudentsCount($instructor->id);
            }

            return view('admin.users.instructor_ranking', compact('orderRankingInstructors'));
        }

        return 404;
    }

    public function createNewInstructor()
    {
        return view('admin.users.create_instructor');
    }

    public function storeNewInstructor(CreateInstructorRequest $request)
    {
        $data = $request->all();
        $result = $this->modelUser->createInstructor($data);
        Notification::createWelcomeNotification($result->id);

        if ($result) {
            flash(__('messages.create_instructor_successfully'))->success();
        } else {
            flash(__('messages.create_instructor_failed'))->error();
        }

        return redirect()->route('admin.instructor_ranking');
    }
}

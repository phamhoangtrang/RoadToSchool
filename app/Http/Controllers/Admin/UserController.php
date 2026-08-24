<?php

namespace App\Http\Controllers\Admin;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseUser;
use App\Http\Requests\CreateInstructorRequest;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * The user repository instance.
     */
    protected $user;

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
        $users = User::query()->where('is_admin', false)->get();

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

        $courseIds = $user->courses()->pluck('id');
        $countStudent = CourseUser::whereIn('course_id', $courseIds)
            ->distinct()
            ->count('user_id');

        return view('admin.users.show', compact('user', 'countStudent'));
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
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'birthday' => ['sometimes', 'nullable', 'date', 'before_or_equal:today'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'personal_info' => ['sometimes', 'nullable', 'string', 'max:50000'],
            'working_place' => ['sometimes', 'nullable', 'string', 'max:255'],
            'grade' => ['sometimes', 'nullable', 'integer', 'between:0,14'],
            'role' => ['sometimes', 'integer', Rule::in([User::ROLE_TEACHER, User::ROLE_STUDENT])],
        ]);

        if (array_key_exists('personal_info', $data) && $data['personal_info'] === null) {
            $data['personal_info'] = '';
        }

        $user->update($data);

        return response()->json($user->fresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        abort_if($request->user()->is($user), 422, 'You cannot delete your own account.');

        $result = $user->delete();

        if ($result) {
            flash(__('delete status') . $id)->success();
        } else {
            flash(__('something wrong'))->error();
        }

        return redirect()->route('admin.users.index');
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

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Course;
use App\Models\Province;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class UserController extends Controller
{
    /**
     * The user model instance.
     */
    protected $modelUser;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(User $users)
    {
        $this->modelUser = $users;
    }

    /**
     * Show detail user
     *
     * @param  mixed  $id
     * @return void
     */
    public function show($id)
    {
        $selectedUser = $this->modelUser->findUser($id);

        // Get difftime from last login to now.
        $diffTime = Carbon::parse($selectedUser->last_login)->diffForHumans();
        // Get all province from database.
        $selectProvince = Province::all()->pluck('name', 'id');

        return view('user.users.show', compact(
            'selectedUser',
            'diffTime',
            'selectProvince'
        ));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = $this->modelUser->findOrFail($id);

        if ($request->has('update_password')) {
            $user->update([
                'password' => Hash::make($request->validated('new_password')),
                'remember_token' => Str::random(60),
            ]);
            flash(__('messages.update_successfully'))->success();

            return redirect()->route('users.show', $id);
        }

        $data = $request->safe()->except('avatar');
        $storedAvatar = null;

        try {
            if ($request->hasFile('avatar')) {
                $storedAvatar = $request->file('avatar')->store('', 'avatar_images');
                $data['avatar'] = 'images/dummy_image/'.$storedAvatar;
            }

            $oldAvatar = $user->avatar;
            $user->update($data);
            if ($storedAvatar && str_starts_with($oldAvatar, 'images/dummy_image/')) {
                Storage::disk('avatar_images')->delete(basename($oldAvatar));
            }
        } catch (Throwable $exception) {
            if ($storedAvatar) {
                Storage::disk('avatar_images')->delete($storedAvatar);
            }

            throw $exception;
        }

        flash(__('messages.update_successfully'))->success();

        return redirect()->route('users.show', $id);
    }

    public function getInstructorInfo($id)
    {
        $selectedInstructor = $this->modelUser->findUser($id);
        $instructorStudentsCount = $this->modelUser->getStudentsCount($id);
        $instructorCoursesCount = Course::where('is_accepted', 1)->where('user_id', $id)->get()->count();
        $instructorRating = round(Course::where('is_accepted', 1)->where('user_id', $id)->avg('course_rate'), 2);
        $bestCoursesInstructor = Course::where('is_accepted', 1)->where('user_id', $id)->orderBy('seller', 'desc')->limit(5)->get();

        return view('user.users.instructor_info', compact(
            'selectedInstructor',
            'instructorStudentsCount',
            'instructorCoursesCount',
            'instructorRating',
            'bestCoursesInstructor'
        ));
    }
}

<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Hash;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    const ROLE_ADMIN = 0;
    const ROLE_TEACHER = 1;
    const ROLE_STUDENT = 2;

    public static $roles = [
        self::ROLE_ADMIN => 'Quản trị viên',
        self::ROLE_TEACHER => 'Giảng viên',
        self::ROLE_STUDENT => 'Học viên',
    ];

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'address',
        'phone',
        'birthday',
        'personal_info',
        'working_place',
        'role',
        'is_admin',
        'grade',
        'instructor_rate',
        'email_verified_at',
        'last_login',
    ];

    const GRADE_0 = 0;
    const GRADE_1 = 1;
    const GRADE_2 = 2;
    const GRADE_3 = 3;
    const GRADE_4 = 4;
    const GRADE_5 = 5;
    const GRADE_6 = 6;
    const GRADE_7 = 7;
    const GRADE_8 = 8;
    const GRADE_9 = 9;
    const GRADE_10 = 10;
    const GRADE_11 = 11;
    const GRADE_12 = 12;
    const GRADE_13 = 13;
    const GRADE_14 = 14;

    public static $grades = [
        self::GRADE_0 => 'Mầm non',
        self::GRADE_1 => 'Lớp 1',
        self::GRADE_2 => 'Lớp 2',
        self::GRADE_3 => 'Lớp 3',
        self::GRADE_4 => 'Lớp 4',
        self::GRADE_5 => 'Lớp 5',
        self::GRADE_6 => 'Lớp 6',
        self::GRADE_7 => 'Lớp 7',
        self::GRADE_8 => 'Lớp 8',
        self::GRADE_9 => 'Lớp 9',
        self::GRADE_10 => 'Lớp 10',
        self::GRADE_11 => 'Lớp 11',
        self::GRADE_12 => 'Lớp 12',
        self::GRADE_13 => 'Đại học & Cao đẳng',
        self::GRADE_14 => 'Đã đi làm / Khác',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password'];

    /**
     * Get the courses for the teacher teaches.
     */
    public function courses()
    {
        return $this->hasMany('App\Models\Course');
    }

    public function cart_items()
    {
        return $this->hasMany('App\Models\CartItem');
    }

    public function permission_users()
    {
        return $this->hasMany('App\Models\PermissionUser');
    }

    public function permissions()
    {
        return $this->belongsToMany('App\Models\Permission', 'permission_user', 'user_id', 'permission_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }

    public function lecture_comments()
    {
        return $this->hasMany('App\Models\LectureComment');
    }

    public function notifications()
    {
        return $this->hasMany('App\Models\Notification');
    }

    public function discussions()
    {
        return $this->hasMany('App\Models\Discussion');
    }

    /**
     * Đường dẫn ảnh đại diện an toàn: nếu tệp không tồn tại (hoặc giá trị lưu bị hỏng)
     * thì dùng ảnh mặc định, tránh ảnh bị trắng/nhấp nháy.
     */
    public function getAvatarUrlAttribute()
    {
        $default = 'assets/img/avatar_users/default_avatar.png';
        $path = str_replace('public/', '', (string) $this->avatar);

        if ($path === '') {
            return asset($default);
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        $path = ltrim($path, '/');

        return file_exists(public_path($path)) ? asset($path) : asset($default);
    }

    public function findUser($id)
    {
        return User::findOrFail($id);
    }

    public function updateUser($data, $id)
    {

        $selectedUser = User::find($id);

        // array_key_exists: nút gửi form không có value nên Laravel đổi '' thành null, isset() sẽ sai
        if (array_key_exists('update_info', $data)) {
            $file = $data['avatar'] ?? null;
            $deleteAvatar = ($data['delete_avatar'] ?? '0') === '1';
            $defaultAvatar = 'assets/img/avatar_users/default_avatar.png';
            $oldAvatar = $selectedUser->avatar;

            // Chỉ cho phép cập nhật các trường thông tin cá nhân (không cho đổi role, is_admin...)
            $data = array_intersect_key($data, array_flip([
                'name', 'address', 'working_place', 'grade', 'personal_info', 'phone', 'birthday',
            ]));

            if ($deleteAvatar) {
                $data['avatar'] = $defaultAvatar;
            } elseif ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                $fileName = 'avatar_' . $selectedUser->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/avatar'), $fileName);
                $data['avatar'] = 'images/avatar/' . $fileName;
            }

            // Xóa tệp ảnh cũ do người dùng tự tải lên (không đụng tới ảnh mặc định của hệ thống)
            if (isset($data['avatar']) && $data['avatar'] !== $oldAvatar
                && $oldAvatar && strpos($oldAvatar, 'images/avatar/') === 0
                && file_exists(public_path($oldAvatar))) {
                @unlink(public_path($oldAvatar));
            }
        }

        if (array_key_exists('update_password', $data)) {
            if (isset($data['old_password'])) {
                $hasher = app('hash');
                $result = $hasher->check($data['old_password'], $selectedUser->password);
                if (!$result) {
                    return false;
                }
                $data['password'] = Hash::make($data['new_password']);
            }
            // Đổi mật khẩu chỉ được phép cập nhật cột password
            $data = array_intersect_key($data, ['password' => true]);
        }

        return $selectedUser->update($data);
    }

    public function getStudentsCount($idInstructor)
    {
        $coursesInstructor = Course::where('is_accepted', 1)->where('user_id', $idInstructor)->pluck('id');

        return CourseUser::whereIn('course_id', $coursesInstructor)->count();
    }

    public function updateInstructorRate()
    {
        $allInstructors = User::where('role', 1)->get();
        foreach ($allInstructors as $instructor) {
            $avgRate = round(Course::where('is_accepted', 1)->where('user_id', $instructor->id)->avg('course_rate'), 2);
            if ($avgRate === 0) {
                $avgRate = 0;
            }

            $updateResult = $instructor->update(['instructor_rate' => $avgRate]);
            if (!$updateResult) {
                return false;
            }
        }

        return true;
    }


    public static function ordinal($number)
    {
        $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
        if ((($number % 100) >= 11) && (($number % 100) <= 13))
            return $number . 'th';
        else
            return $number . $ends[$number % 10];
    }

    public function createInstructor($data)
    {
        $data['avatar'] = 'images/default_avatar/teacher.jpg';
        $data['role'] = self::ROLE_TEACHER;
        $data['is_admin'] = 0;
        $data['password'] = Hash::make($data['password']);
        // Giảng viên do trung tâm tạo tài khoản nên không cần xác thực email.
        $data['email_verified_at'] = now();

        $instructor = User::create($data);
        $instructor->assignDefaultInstructorPermissions();

        return $instructor;
    }

    /**
     * Quyền mặc định của giảng viên (khớp với các giảng viên có sẵn).
     * Thiếu các quyền này, middleware access_page sẽ trả về 403 khi vào trang giảng viên.
     */
    public const INSTRUCTOR_DEFAULT_PERMISSIONS = [
        'Access instructor dashboard',
        'Show instructor\'s course',
        'Create Course',
        'Show all self student',
        'Show a instructor\'s course',
        'Update a course',
        'Update a lecture',
        'Create a lecture in course',
        'Create a course',
    ];

    public const SUPER_ADMIN_ID = 1;

    public function isSuperAdmin()
    {
        return (int)$this->id === self::SUPER_ADMIN_ID;
    }

    public function assignDefaultInstructorPermissions()
    {
        $ids = Permission::whereIn('content', self::INSTRUCTOR_DEFAULT_PERMISSIONS)->pluck('id');
        $this->permissions()->syncWithoutDetaching($ids->all());
    }
}

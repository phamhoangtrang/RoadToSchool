<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $fillable = [
        'content',
        'group_permission',
    ];

    const ADMIN_GROUP_PERMISSION = 0;
    const INSTRUCTOR_GROUP_PERMISSION = 1;
    const STUDENT_GROUP_PERMISSION = 2;

    public static $permission_group = [
        self::ADMIN_GROUP_PERMISSION => 'Quyền hạn dành cho Quản trị viên (Admin)',
        self::INSTRUCTOR_GROUP_PERMISSION => 'Quyền hạn dành cho Giảng viên (Instructor)',
        self::STUDENT_GROUP_PERMISSION => 'Quyền hạn dành cho Học viên (Student)',
    ];

    const ADMIN_GROUP = 0;
    const INSTRUCTOR_GROUP = 1;
    const STUDENT_GROUP = 2;

    public static $user_group = [
        self::ADMIN_GROUP => 'Nhóm Quản trị viên',
        self::INSTRUCTOR_GROUP => 'Nhóm Giảng viên',
        self::STUDENT_GROUP => 'Nhóm Học viên',
    ];

    public static $permission_translations = [
        // Admin permissions
        'Access admin dashboard' => 'Truy cập Bảng điều khiển Quản trị viên',
        'Show all bill' => 'Xem danh sách tất cả hóa đơn',
        'Show a bill' => 'Xem chi tiết hóa đơn',
        'Update a bill status' => 'Cập nhật trạng thái đơn hàng',
        'Delete a bill' => 'Xóa đơn hàng / hóa đơn',
        'Show all course' => 'Xem danh sách tất cả khóa học',
        'Show user permission' => 'Xem phân quyền tài khoản',
        'Update user permission' => 'Cập nhật phân quyền tài khoản',
        'Add new permission' => 'Thêm quyền hạn mới vào hệ thống',
        'View all course information' => 'Xem toàn bộ thông tin khóa học',
        'View all categories' => 'Xem và quản lý các danh mục',
        'View all lecture requests' => 'Xem danh sách yêu cầu bài giảng',
        'Accept a lecture request' => 'Phê duyệt yêu cầu bài giảng',
        'View all users' => 'Xem và quản lý tất cả người dùng',
        'View instructor ranking' => 'Xem bảng xếp hạng giảng viên',
        'Create new instructor' => 'Tạo tài khoản giảng viên mới',
        'Check and reply all conversation waiting' => 'Kiểm tra & phản hồi tin nhắn chờ',
        'Update an user' => 'Cập nhật thông tin người dùng',

        // Instructor permissions
        'Access instructor dashboard' => 'Truy cập Bảng điều khiển Giảng viên',
        'Show instructor\'s course' => 'Xem danh sách khóa học của tôi',
        'Create Course' => 'Tạo khóa học mới',
        'Show all self student' => 'Xem danh sách học viên của tôi',
        'Show a instructor\'s course' => 'Xem chi tiết khóa học của tôi',
        'Update a course' => 'Chỉnh sửa nội dung khóa học',
        'Update a lecture' => 'Chỉnh sửa nội dung bài giảng',
        'Create a lecture in course' => 'Tạo bài giảng mới trong khóa học',
        'Create a course' => 'Khởi tạo khóa học mới',

        // Student permissions
        'Access page' => 'Truy cập các trang công khai',
        'Show profile' => 'Xem thông tin cá nhân',
        'Update self profile' => 'Cập nhật hồ sơ cá nhân',
        'Show instructor information' => 'Xem thông tin chi tiết giảng viên',
        'Show all courses' => 'Xem danh sách tất cả khóa học',
        'Show a course' => 'Xem chi tiết khóa học',
        'Show a lecture' => 'Xem nội dung bài giảng',
        'Add to cart' => 'Thêm khóa học vào giỏ hàng',
        'Checkout cart' => 'Truy cập trang thanh toán',
        'Checkout and create bill' => 'Xác nhận thanh toán và tạo đơn hàng',
        'Comment' => 'Đăng bình luận và đánh giá',
    ];

    public function getViContentAttribute()
    {
        return self::$permission_translations[$this->content] ?? $this->content;
    }

    public function permission_users()
    {
        return $this->hasMany('App\Models\PermissionUser');
    }
}

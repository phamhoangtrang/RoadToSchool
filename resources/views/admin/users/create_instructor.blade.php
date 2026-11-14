@extends('admin.layouts.default')

@section('title')
    {{ __('titles.create_new_instructor') }}
@endsection

@section('inline_styles')
<style>
    .form-create-card {
        border-radius: 12px;
        box-shadow: 0 2px 14px rgba(0, 0, 0, 0.06);
        border: none;
    }
    .form-create-card .card-header {
        background: #fff;
        padding: 20px 24px;
        border-bottom: 1px solid #f0f2f5;
    }
    .form-group label {
        font-weight: 600;
        color: #343a40;
        margin-bottom: 8px;
        font-size: 14px;
    }
    .form-control {
        border-radius: 8px;
        padding: 10px 14px;
        height: auto;
        border: 1px solid #ced4da;
        font-size: 14px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .form-control:focus {
        border-color: #0b82f8;
        box-shadow: 0 0 0 3px rgba(11, 130, 248, 0.15);
    }
    .form-helper-text {
        font-size: 12px;
        color: #6c757d;
        margin-top: 6px;
    }
</style>
@endsection

@section('content')
    <!-- Content Wrapper START -->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 15px;">
                    <div>
                        <h2 class="header-title" style="margin-bottom: 4px; font-weight: 700;">Tạo tài khoản giảng viên mới</h2>
                        <div class="header-sub-title">
                            <nav class="breadcrumb breadcrumb-dash">
                                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item"><i class="ti-home p-r-5"></i>Trang chủ</a>
                                <a class="breadcrumb-item" href="{{ route('admin.users.index') }}">Người dùng</a>
                                <span class="breadcrumb-item active">Tạo giảng viên mới</span>
                            </nav>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px; font-weight: 600; padding: 9px 16px;">
                            <i class="mdi mdi-arrow-left mr-1"></i> Danh sách người dùng
                        </a>
                        <a href="{{ route('admin.instructor_ranking') }}" class="btn btn-outline-primary" style="border-radius: 8px; font-weight: 600; padding: 9px 16px; margin-left: 8px;">
                            <i class="mdi mdi-star mr-1"></i> Xếp hạng giảng viên
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card Create Instructor -->
            <div class="card form-create-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title font-weight-bold mb-0" style="font-size: 16px;">
                            <i class="mdi mdi-account-plus text-primary mr-2" style="font-size: 20px;"></i>
                            Thông tin tài khoản giảng viên
                        </h4>
                        <p class="text-muted mb-0" style="font-size: 13px; margin-top: 4px;">
                            Vui lòng nhập đầy đủ các thông tin bắt buộc (<span class="text-danger">*</span>) bên dưới để cấp tài khoản giảng viên mới trên Road To School
                        </p>
                    </div>
                </div>

                <div class="card-body p-30">
                    @include('flash::message')

                    @if (isset($errors) && $errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
                            <div class="d-flex align-items-center mb-2">
                                <i class="mdi mdi-alert-circle mr-2" style="font-size: 20px;"></i>
                                <strong style="font-size: 15px;">Có lỗi xảy ra, vui lòng kiểm tra lại:</strong>
                            </div>
                            <ul class="mb-0 pl-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.store_new_instructor') }}" method="post" id="create-instructor-form">
                        @csrf
                        <div class="row">
                            <!-- Họ và tên -->
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="input-instructor-name">
                                        <i class="mdi mdi-account mr-1 text-primary"></i> Họ và tên giảng viên <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="input-instructor-name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           placeholder="Nhập họ và tên đầy đủ của giảng viên..."
                                           required>
                                    <div class="form-helper-text">Tên giảng viên sẽ hiển thị trên thông tin cá nhân và các khóa học đã tạo.</div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="input-instructor-email">
                                        <i class="mdi mdi-email mr-1 text-primary"></i> Địa chỉ Email đăng nhập <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                           class="form-control"
                                           id="input-instructor-email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           placeholder="Nhập email (ví dụ: giangvien@r2s.edu.vn)..."
                                           required>
                                    <div class="form-helper-text">Email dùng để đăng nhập vào hệ thống và nhận các thông báo quan trọng.</div>
                                </div>
                            </div>

                            <!-- Mật khẩu -->
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="input-instructor-password">
                                        <i class="mdi mdi-lock mr-1 text-primary"></i> Mật khẩu khởi tạo <span class="text-danger">*</span>
                                    </label>
                                    <input type="password"
                                           class="form-control"
                                           id="input-instructor-password"
                                           name="password"
                                           placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)..."
                                           required>
                                    <div class="form-helper-text">Mật khẩu gồm chữ và số, độ dài từ 6 đến 50 ký tự.</div>
                                </div>
                            </div>

                            <!-- Xác nhận mật khẩu -->
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="input-instructor-repassword">
                                        <i class="mdi mdi-lock-check mr-1 text-primary"></i> Xác nhận mật khẩu <span class="text-danger">*</span>
                                    </label>
                                    <input type="password"
                                           class="form-control"
                                           id="input-instructor-repassword"
                                           name="password_confirmation"
                                           placeholder="Nhập lại mật khẩu để xác nhận..."
                                           required>
                                    <div class="form-helper-text">Vui lòng nhập chính xác lại mật khẩu đã nhập ở trên.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info d-flex align-items-center mb-4" style="border-radius: 8px; background: #e3f2fd; border-color: #bbdefb; color: #0d47a1;">
                            <i class="mdi mdi-information-outline mr-2" style="font-size: 22px;"></i>
                            <div style="font-size: 13px;">
                                <strong>Ghi chú:</strong> Tài khoản mới sẽ được tự động gắn vai trò <strong>Giảng viên</strong> và ảnh đại diện mặc định. Giảng viên có thể cập nhật thêm thông tin cá nhân và mật khẩu sau khi đăng nhập.
                            </div>
                        </div>

                        <!-- Buttons Row -->
                        <div class="d-flex align-items-center justify-content-end flex-wrap pt-3 border-top" style="gap: 12px;">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light" style="border-radius: 8px; padding: 10px 20px; font-weight: 500;">
                                <i class="mdi mdi-close mr-1"></i> Hủy bỏ
                            </a>
                            <button type="reset" class="btn btn-default" id="btn-reset" style="border-radius: 8px; padding: 10px 20px; font-weight: 500;">
                                <i class="mdi mdi-refresh mr-1"></i> Nhập lại
                            </button>
                            <button type="submit" class="btn btn-primary" id="btn-submit" style="border-radius: 8px; padding: 10px 24px; font-weight: 600; box-shadow: 0 4px 12px rgba(11, 130, 248, 0.25);">
                                <i class="mdi mdi-account-plus mr-1"></i> Tạo tài khoản giảng viên
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Wrapper END -->
@endsection

@section('inline_scripts')
    <script>
        $(document).ready(function () {
            $('#btn-reset').on('click', function (e) {
                $('#input-instructor-name').val('');
                $('#input-instructor-email').val('');
                $('#input-instructor-password').val('');
                $('#input-instructor-repassword').val('');
            });
        });
    </script>
@endsection
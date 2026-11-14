@extends('admin.layouts.default')

@section('title')
    {{ $user->name }} - Chi tiết người dùng
@endsection

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 15px;">
                    <div>
                        <h2 class="header-title" style="margin-bottom: 4px; font-weight: 700;">Hồ sơ người dùng</h2>
                        <div class="header-sub-title">
                            <nav class="breadcrumb breadcrumb-dash">
                                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">
                                    <i class="ti-home p-r-5"></i>{{ __('titles.r2s_dashboard') }}
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="breadcrumb-item">{{ __('titles.users') }}</a>
                                <span class="breadcrumb-item active">{{ $user->name }}</span>
                            </nav>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px; font-weight: 600; padding: 9px 16px;">
                            <i class="ti-arrow-left mr-1"></i> Quay lại danh sách
                        </a>
                    </div>
                </div>
            </div>
            <div class="card" style="border-radius: 12px; box-shadow: 0 2px 14px rgba(0, 0, 0, 0.06); border: none;">
                <div class="card-body p-30">
                    <div class="row">
                        <div class="col-md-3 text-center border-right">
                            <img src="{{ asset($user->avatar ?? 'assets/img/avatar_users/default_avatar.png') }}" 
                                 class="img-fluid rounded-circle m-b-20" 
                                 style="width: 140px; height: 140px; object-fit: cover; border: 3px solid #e9ecef; box-shadow: 0 4px 12px rgba(0,0,0,0.08);" 
                                 alt="{{ $user->name }}">
                            <h4 class="font-weight-bold text-dark mt-2 mb-1">{{ $user->name }}</h4>
                            <p class="text-muted mb-2" style="font-size: 13px;">{{ $user->email }}</p>
                            <div class="mb-3">
                                @if($user->is_admin == 1)
                                    <span class="badge badge-pill badge-danger" style="font-size: 12px; padding: 6px 14px;">Quản trị viên</span>
                                @elseif($user->role == 1)
                                    <span class="badge badge-pill badge-success" style="font-size: 12px; padding: 6px 14px;">Giảng viên</span>
                                @else
                                    <span class="badge badge-pill badge-info" style="font-size: 12px; padding: 6px 14px;">Học viên</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-9 pl-4">
                            <h4 class="font-weight-bold text-dark mb-3" style="font-size: 16px;">
                                <i class="mdi mdi-information-outline text-primary mr-1"></i> Thông tin chi tiết
                            </h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%; background: #f8f9fa;">Địa chỉ Email</th>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <th style="background: #f8f9fa;">Số điện thoại</th>
                                            <td>{{ $user->phone ?? 'Chưa cập nhật' }}</td>
                                        </tr>
                                        <tr>
                                            <th style="background: #f8f9fa;">Ngày sinh</th>
                                            <td>{{ $user->birthday ?? 'Chưa cập nhật' }}</td>
                                        </tr>
                                        <tr>
                                            <th style="background: #f8f9fa;">Địa chỉ</th>
                                            <td>{{ $user->address ?? 'Chưa cập nhật' }}</td>
                                        </tr>
                                        <tr>
                                            <th style="background: #f8f9fa;">Nơi công tác / Trường</th>
                                            <td>{{ $user->working_place ?? 'Chưa cập nhật' }}</td>
                                        </tr>
                                        <tr>
                                            <th style="background: #f8f9fa;">Giới thiệu bản thân</th>
                                            <td>{!! $user->personal_info ?? 'Chưa có thông tin' !!}</td>
                                        </tr>
                                        @if($user->role == 1)
                                        <tr>
                                            <th style="background: #f8f9fa;">Tổng số học viên</th>
                                            <td>
                                                <span class="font-weight-bold text-primary">{{ number_format($countStudent) }}</span> học viên
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('admin.layouts.default')

@section('title')
    {{ __('titles.instructor_ranking') }}
@endsection

@section('inline_styles')
<style>
    .ranking-stat-card {
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none;
    }
    .ranking-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
    .badge-rank-1 {
        background: linear-gradient(135deg, #ffd700 0%, #ff9800 100%);
        color: #fff;
        font-weight: 700;
        font-size: 13px;
        padding: 6px 14px;
        border-radius: 20px;
        box-shadow: 0 3px 8px rgba(255, 152, 0, 0.35);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-rank-2 {
        background: linear-gradient(135deg, #cfd8dc 0%, #78909c 100%);
        color: #fff;
        font-weight: 700;
        font-size: 13px;
        padding: 6px 14px;
        border-radius: 20px;
        box-shadow: 0 3px 8px rgba(120, 144, 156, 0.35);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-rank-3 {
        background: linear-gradient(135deg, #ffab91 0%, #d84315 100%);
        color: #fff;
        font-weight: 700;
        font-size: 13px;
        padding: 6px 14px;
        border-radius: 20px;
        box-shadow: 0 3px 8px rgba(216, 67, 21, 0.35);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-rank-normal {
        background: #f1f3f5;
        color: #495057;
        font-weight: 700;
        font-size: 13px;
        padding: 6px 14px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
    }
    .instructor-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e9ecef;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .table-ranking th {
        font-weight: 700;
        color: #495057;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        border-top: none;
        padding: 14px 16px;
        background: #f8f9fa;
    }
    .table-ranking td {
        vertical-align: middle !important;
        padding: 16px;
        border-color: #f1f3f5;
    }
    .table-ranking tbody tr:hover {
        background-color: #fcfdfe;
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
                        <h2 class="header-title" style="margin-bottom: 4px; font-weight: 700;">Bảng xếp hạng giảng viên</h2>
                        <div class="header-sub-title">
                            <nav class="breadcrumb breadcrumb-dash">
                                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item"><i class="ti-home p-r-5"></i>Trang chủ</a>
                                <a class="breadcrumb-item" href="{{ route('admin.users.index') }}">Người dùng</a>
                                <span class="breadcrumb-item active">Bảng xếp hạng giảng viên</span>
                            </nav>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admin.users.create_instructor') }}" class="btn btn-primary" style="border-radius: 8px; font-weight: 600; padding: 10px 18px; box-shadow: 0 4px 12px rgba(11, 130, 248, 0.25);">
                            <i class="mdi mdi-account-plus mr-1"></i> Tạo giảng viên mới
                        </a>
                    </div>
                </div>
            </div>

            <!-- Summary KPI Cards -->
            @php
                $totalInstructors = count($orderRankingInstructors);
                $topInstructor = $orderRankingInstructors->first();
                $topRate = $topInstructor ? number_format($topInstructor->instructor_rate, 1) : '0.0';
                $totalCourses = $orderRankingInstructors->sum('coursesCount');
            @endphp
            <div class="row m-b-20">
                <div class="col-md-4">
                    <div class="card ranking-stat-card">
                        <div class="card-body p-20 d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #e3f2fd; color: #1976d2; font-size: 24px; margin-right: 16px;">
                                <i class="mdi mdi-account-star"></i>
                            </div>
                            <div>
                                <div class="text-muted" style="font-size: 13px; font-weight: 500;">Tổng số giảng viên</div>
                                <div class="text-dark font-weight-bold" style="font-size: 22px;">{{ $totalInstructors }} <span style="font-size: 14px; font-weight: 400; color: #6c757d;">giảng viên</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card ranking-stat-card">
                        <div class="card-body p-20 d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #fff8e1; color: #f57f17; font-size: 24px; margin-right: 16px;">
                                <i class="mdi mdi-crown"></i>
                            </div>
                            <div>
                                <div class="text-muted" style="font-size: 13px; font-weight: 500;">Giảng viên dẫn đầu (#1)</div>
                                <div class="text-dark font-weight-bold" style="font-size: 18px; line-height: 1.3;">
                                    {{ $topInstructor ? $topInstructor->name : 'Chưa có' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card ranking-stat-card">
                        <div class="card-body p-20 d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #e8f5e9; color: #388e3c; font-size: 24px; margin-right: 16px;">
                                <i class="mdi mdi-star-circle"></i>
                            </div>
                            <div>
                                <div class="text-muted" style="font-size: 13px; font-weight: 500;">Điểm đánh giá cao nhất</div>
                                <div class="text-dark font-weight-bold" style="font-size: 22px;">{{ $topRate }} <span style="font-size: 14px; font-weight: 400; color: #6c757d;">/ 5.0 ⭐</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ranking Table Card -->
            <div class="card" style="border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: none; overflow: hidden;">
                <div class="card-header border bottom d-flex align-items-center justify-content-between p-20" style="background: #fff;">
                    <div>
                        <h4 class="card-title font-weight-bold mb-0" style="font-size: 16px;">Danh sách xếp hạng giảng viên xuất sắc</h4>
                        <p class="text-muted mb-0" style="font-size: 13px; margin-top: 3px;">Thứ hạng được cập nhật tự động dựa trên điểm số đánh giá trung bình của các khóa học</p>
                    </div>
                </div>
                @include('flash::message')
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-ranking mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 140px;">Thứ hạng</th>
                                    <th>Giảng viên</th>
                                    <th style="width: 130px; text-align: center;">Khóa học</th>
                                    <th style="width: 140px; text-align: center;">Học viên</th>
                                    <th style="width: 160px; text-align: center;">Điểm đánh giá</th>
                                    <th style="width: 150px; text-align: center;">Trạng thái</th>
                                    <th style="width: 120px; text-align: center;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($orderRankingInstructors as $instructor)
                                <tr>
                                    <td>
                                        @if($instructor->ranking === 1)
                                            <span class="badge-rank-1">
                                                <i class="mdi mdi-crown"></i> Quán quân (#1)
                                            </span>
                                        @elseif($instructor->ranking === 2)
                                            <span class="badge-rank-2">
                                                <i class="mdi mdi-medal"></i> Á quân 1 (#2)
                                            </span>
                                        @elseif($instructor->ranking === 3)
                                            <span class="badge-rank-3">
                                                <i class="mdi mdi-medal"></i> Á quân 2 (#3)
                                            </span>
                                        @else
                                            <span class="badge-rank-normal">
                                                Hạng #{{ $instructor->ranking }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center" style="gap: 14px;">
                                            <img class="instructor-avatar"
                                                 src="{{ str_replace('public/', '', asset($instructor->avatar)) }}"
                                                 onerror="this.src='{{ asset('images/default_avatar/teacher.jpg') }}'"
                                                 alt="{{ $instructor->name }}">
                                            <div>
                                                <div class="font-weight-bold text-dark" style="font-size: 15px;">{{ $instructor->name }}</div>
                                                <div class="text-muted" style="font-size: 12px; margin-top: 2px;">
                                                    <i class="mdi mdi-email-outline mr-1"></i>{{ $instructor->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="font-weight-bold text-dark" style="font-size: 15px;">{{ $instructor->coursesCount }}</span>
                                        <div class="text-muted" style="font-size: 12px;">khóa học</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="font-weight-bold text-primary" style="font-size: 15px;">{{ number_format($instructor->studentsCount) }}</span>
                                        <div class="text-muted" style="font-size: 12px;">học viên</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge" style="background: #fff8e1; color: #b78103; font-weight: 700; font-size: 13px; padding: 7px 14px; border-radius: 8px; border: 1px solid #ffe082; display: inline-flex; align-items: center; gap: 5px;">
                                            <i class="mdi mdi-star text-warning"></i> {{ number_format($instructor->instructor_rate, 1) }} / 5.0
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if(!$instructor->deleted_at)
                                            <span class="badge badge-pill badge-success" style="font-size: 12px; padding: 6px 14px; font-weight: 600;">
                                                <i class="mdi mdi-check-circle mr-1"></i> Đang hoạt động
                                            </span>
                                        @else
                                            <span class="badge badge-pill badge-danger" style="font-size: 12px; padding: 6px 14px; font-weight: 600;">
                                                <i class="mdi mdi-lock mr-1"></i> Đã khóa
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.users.show', $instructor->id) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; font-weight: 600; padding: 5px 12px;" title="Xem chi tiết hồ sơ">
                                            <i class="mdi mdi-eye mr-1"></i> Chi tiết
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center p-40">
                                        <div class="text-muted">
                                            <i class="mdi mdi-account-off" style="font-size: 40px; color: #ccc;"></i>
                                            <p class="mt-2 mb-0">Hiện tại chưa có dữ liệu giảng viên để xếp hạng.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Wrapper END -->
@endsection

@section('inline_scripts')
@endsection
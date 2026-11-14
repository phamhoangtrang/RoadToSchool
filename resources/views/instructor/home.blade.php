@extends('admin.layouts.default')

@section('title')
    {{ __('titles.r2s_instructor_dashboard') }}
@endsection

@section('inline_styles')
    <style>
        .instructor-hero-banner {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: 16px;
            padding: 32px;
            color: #fff;
            margin-bottom: 24px;
            box-shadow: 0 10px 25px rgba(30, 41, 59, 0.15);
            position: relative;
            overflow: hidden;
        }
        .instructor-hero-banner::after {
            content: '';
            position: absolute;
            right: -30px;
            top: -30px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
        }
        .hero-btn {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none !important;
        }
        .hero-btn-primary {
            background: #2563eb;
            color: #fff;
            border: 1px solid #3b82f6;
        }
        .hero-btn-primary:hover {
            background: #1d4ed8;
            color: #fff;
            transform: translateY(-2px);
        }
        .hero-btn-outline {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
        }
        .hero-btn-outline:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            transform: translateY(-2px);
        }
        .inst-kpi-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            transition: all 0.25s ease;
            background: #fff;
            margin-bottom: 24px;
        }
        .inst-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }
        .kpi-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        .inst-table-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            background: #fff;
            margin-bottom: 24px;
            overflow: hidden;
        }
        .table-course-thumb {
            width: 68px;
            height: 46px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            flex-shrink: 0;
            margin-right: 18px !important;
        }
        .course-title-text {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: color 0.2s;
        }
        .course-title-text:hover {
            color: #2563eb;
            text-decoration: none;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }
        .status-pill.accepted {
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        .status-pill.pending {
            background-color: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
        }
        .student-count-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background-color: #f1f5f9;
            color: #475569;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .action-btn-circle {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            text-decoration: none !important;
            margin: 0 3px;
        }
        .action-btn-circle.view {
            background-color: #eff6ff;
            color: #2563eb;
        }
        .action-btn-circle.view:hover {
            background-color: #3b82f6;
            color: #fff;
            transform: scale(1.08);
        }
        .action-btn-circle.add {
            background-color: #ecfdf5;
            color: #059669;
        }
        .action-btn-circle.add:hover {
            background-color: #10b981;
            color: #fff;
            transform: scale(1.08);
        }
        .inst-table th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-top: none;
            border-bottom: 2px solid #e2e8f0;
            padding: 14px 16px;
        }
        .inst-table td {
            padding: 14px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
        .comment-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #e2e8f0;
        }
        .comment-box {
            background: #f8fafc;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            color: #334155;
            line-height: 1.5;
            border: 1px solid #f1f5f9;
        }
        .border-right-md {
            border-right: 1px dashed #e2e8f0;
        }
        @media (max-width: 767px) {
            .border-right-md {
                border-right: none !important;
                border-bottom: 1px dashed #e2e8f0;
                padding-bottom: 16px;
                margin-bottom: 16px;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Content Wrapper START -->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Hero Banner -->
            <div class="instructor-hero-banner">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center gap-2 m-b-10">
                            <span class="badge" style="background: rgba(59, 130, 246, 0.3); color: #93c5fd; font-size: 11px; padding: 5px 12px; border-radius: 20px; font-weight: 600;">
                                KHÔNG GIAN GIẢNG DẠY
                            </span>
                        </div>
                        <h2 style="font-weight: 800; font-size: 26px; color: #fff; margin-bottom: 8px;">
                            Xin chào, {{ Auth::user()->name }}! 👋
                        </h2>
                        <p style="color: #94a3b8; font-size: 14px; margin-bottom: 20px; max-width: 600px; line-height: 1.5;">
                            Chào mừng bạn trở lại với Bảng điều khiển Giảng viên R2S. Quản lý nội dung bài giảng, theo dõi tiến độ của học viên và trả lời bình luận nhanh chóng.
                        </p>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <a href="{{ route('instructor.courses.create') }}" class="hero-btn hero-btn-primary m-r-10">
                                <i class="ti-plus"></i> Tạo khóa học mới
                            </a>
                            <a href="{{ route('instructor.courses.index') }}" class="hero-btn hero-btn-outline">
                                <i class="ti-book"></i> Quản lý tất cả khóa học
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI Summary Cards -->
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="card inst-kpi-card">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="kpi-icon-box m-r-15" style="background-color: #eff6ff; color: #3b82f6;">
                                <i class="ti-book"></i>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Khóa học của bạn</div>
                                <div style="font-size: 24px; font-weight: 700; color: #1e293b; line-height: 1.2;">{{ $totalCourses ?? $newestInstructorCourseList->count() }}</div>
                                <div style="font-size: 11px; color: #10b981;">{{ $acceptedCourses ?? 0 }} đã duyệt &bull; {{ $pendingCourses ?? 0 }} chờ</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card inst-kpi-card">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="kpi-icon-box m-r-15" style="background-color: #ecfdf5; color: #10b981;">
                                <i class="ti-user"></i>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Lượt học viên</div>
                                <div style="font-size: 24px; font-weight: 700; color: #059669; line-height: 1.2;">{{ $totalStudents ?? 0 }}</div>
                                <div style="font-size: 11px; color: #64748b;">Đang theo học</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card inst-kpi-card">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="kpi-icon-box m-r-15" style="background-color: #fffbeb; color: #f59e0b;">
                                <i class="ti-shopping-cart"></i>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Tổng lượt bán</div>
                                <div style="font-size: 24px; font-weight: 700; color: #d97706; line-height: 1.2;">{{ $totalSales ?? 0 }}</div>
                                <div style="font-size: 11px; color: #64748b;">Lượt mua khóa học</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card inst-kpi-card">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="kpi-icon-box m-r-15" style="background-color: #fdf4ff; color: #a855f7;">
                                <i class="ti-comment-alt"></i>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Tương tác học viên</div>
                                <div style="font-size: 24px; font-weight: 700; color: #9333ea; line-height: 1.2;">{{ $totalComments ?? ($commentInCourse->count() + $commentInLecture->count()) }}</div>
                                <div style="font-size: 11px; color: #64748b;">Bình luận & hỏi đáp</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Báo cáo Doanh thu & Thu nhập Giảng viên (Chính sách 70%) -->
            <div class="row">
                <div class="col-12">
                    <div class="card inst-table-card" style="border-left: 4px solid #10b981; margin-bottom: 24px;">
                        <div class="card-header border-bottom p-3 d-flex align-items-center justify-content-between flex-wrap" style="background: #f8fafc; gap: 10px;">
                            <div class="d-flex align-items-center">
                                <div class="m-r-10" style="width: 34px; height: 34px; border-radius: 8px; background: #dcfce7; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                    <i class="ti-wallet"></i>
                                </div>
                                <div>
                                    <h5 class="m-b-0" style="font-weight: 700; color: #1e293b; font-size: 16px;">Thu nhập & Doanh thu của bạn</h5>
                                    <small class="text-muted" style="font-size: 12px;">Chính sách chia sẻ doanh thu: Giảng viên nhận <strong>70%</strong> doanh thu &bull; <strong>30%</strong> chi phí nền tảng Road To School</small>
                                </div>
                            </div>
                            <div>
                                <span class="badge badge-pill font-weight-bold" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 6px 14px; font-size: 12px;">
                                    Tỷ lệ nhận: 70% Doanh thu
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <!-- 1. Doanh thu khóa học 100% -->
                                <div class="col-md-4 border-right-md mb-3 mb-md-0">
                                    <div class="d-flex align-items-center">
                                        <div class="kpi-icon-box m-r-15" style="background-color: #eff6ff; color: #3b82f6;">
                                            <i class="ti-bar-chart"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 11.5px; color: #64748b; font-weight: 700; text-transform: uppercase;">1. Doanh thu khóa học (100%)</div>
                                            <div style="font-size: 22px; font-weight: 800; color: #1e293b; margin: 3px 0; line-height: 1.2;">
                                                {{ number_format($totalRevenue ?? 0, 0, ',', '.') }} đ
                                            </div>
                                            <div style="font-size: 12px; color: #64748b;">Tổng tiền bán ra từ các khóa học</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2. Thu nhập thực nhận 70% -->
                                <div class="col-md-4 border-right-md mb-3 mb-md-0">
                                    <div class="d-flex align-items-center">
                                        <div class="kpi-icon-box m-r-15" style="background-color: #ecfdf5; color: #10b981;">
                                            <i class="ti-money"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 11.5px; color: #059669; font-weight: 700; text-transform: uppercase;">2. Thu nhập của bạn (70%)</div>
                                            <div style="font-size: 24px; font-weight: 800; color: #059669; margin: 3px 0; line-height: 1.2;">
                                                {{ number_format($instructorIncome ?? 0, 0, ',', '.') }} đ
                                            </div>
                                            <div style="font-size: 12px; color: #059669; font-weight: 500;">Số tiền thực nhận đối soát định kỳ</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3. Phí sàn 30% -->
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <div class="kpi-icon-box m-r-15" style="background-color: #f1f5f9; color: #64748b;">
                                            <i class="ti-server"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 11.5px; color: #64748b; font-weight: 700; text-transform: uppercase;">3. Phí nền tảng (30%)</div>
                                            <div style="font-size: 20px; font-weight: 700; color: #64748b; margin: 3px 0; line-height: 1.2;">
                                                {{ number_format($platformFee ?? 0, 0, ',', '.') }} đ
                                            </div>
                                            <div style="font-size: 12px; color: #94a3b8;">Phí server, bảo mật & cổng thanh toán</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert m-t-20 m-b-0" style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 12px 16px; font-size: 12.5px; color: #475569;">
                                <i class="ti-info-alt text-primary m-r-5"></i>
                                <strong>Ghi chú quyết toán:</strong> Thu nhập giảng viên được hệ thống tự động bóc tách theo tỷ lệ 70% từ các đơn hàng học viên thanh toán thành công. Quản trị viên đối soát doanh thu và chuyển khoản trực tiếp vào tài khoản ngân hàng của bạn vào cuối mỗi tháng.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Khóa học gần đây của bạn -->
            <div class="row">
                <div class="col-12">
                    <div class="card inst-table-card">
                        <div class="card-header border-bottom p-3 d-flex align-items-center justify-content-between" style="background: #fff;">
                            <div class="d-flex align-items-center">
                                <div class="m-r-10" style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                    <i class="ti-layout-grid2"></i>
                                </div>
                                <h5 class="m-b-0" style="font-weight: 700; color: #1e293b; font-size: 16px;">Khóa học gần đây của bạn</h5>
                            </div>
                            <div>
                                <a href="{{ route('instructor.courses.index') }}" class="btn btn-sm" style="background: #f1f5f9; color: #334155; font-weight: 600; border-radius: 8px;">
                                    Xem tất cả khóa học <i class="ti-arrow-right m-l-5 font-size-11"></i>
                                </a>
                            </div>
                        </div>
                        <div class="table-overflow">
                            <table class="table table-hover inst-table m-b-0">
                                <thead>
                                <tr>
                                    <th style="width: 32%;">Khóa học</th>
                                    <th style="width: 14%;">Số học viên</th>
                                    <th style="width: 14%;">Giá niêm yết</th>
                                    <th style="width: 15%;">Thu nhập (70%)</th>
                                    <th style="width: 11%; text-align: center;">Trạng thái</th>
                                    <th style="width: 14%; text-align: right;">Cập nhật / Thao tác</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($newestInstructorCourseList as $course)
                                    @php
                                        $avatar = $course->course_avatar;
                                        if ($avatar) {
                                            if (strpos($avatar, 'http') !== 0) {
                                                $avatar = asset(str_replace('public/', '', $avatar));
                                            }
                                        } else {
                                            $avatar = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=100&auto=format&fit=crop&q=80';
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center" style="gap: 16px;">
                                                <img src="{{ $avatar }}" alt="{{ $course->title }}" class="table-course-thumb"
                                                     style="margin-right: 16px;"
                                                     onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=100&auto=format&fit=crop&q=80';"/>
                                                <div style="min-width: 0; padding-left: 2px;">
                                                    <a href="{{ route('instructor.courses.show', $course->id) }}" class="course-title-text" title="{{ $course->title }}">
                                                        {{ $course->title }}
                                                    </a>
                                                    <small class="text-muted d-block" style="font-size: 11px;">Mã: #{{ $course->id }} &bull; {{ $course->category ? ($course->category->vi_title ?: $course->category->title) : 'Chung' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="student-count-pill">
                                                <i class="ti-user text-primary"></i> {{ $course->student_count }} học viên
                                            </span>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #1e293b; font-size: 13px;">
                                                @if($course->promotion_price > 0)
                                                    {{ number_format($course->promotion_price, 0, ',', '.') }} đ
                                                @elseif($course->origin_price > 0)
                                                    {{ number_format($course->origin_price, 0, ',', '.') }} đ
                                                @else
                                                    <span class="text-success font-weight-bold">Miễn phí</span>
                                                @endif
                                            </div>
                                            <small class="text-muted" style="font-size: 11px;">
                                                <i class="ti-shopping-cart"></i> {{ $course->seller ?: 0 }} lượt mua
                                            </small>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: #059669; font-size: 13.5px;">
                                                {{ number_format($course->instructor_income ?? 0, 0, ',', '.') }} đ
                                            </div>
                                            <small class="text-muted" style="font-size: 11px;">
                                                Doanh thu: {{ number_format($course->total_revenue ?? 0, 0, ',', '.') }} đ
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            @if ($course->is_accepted == 1)
                                                <span class="status-pill accepted">
                                                    <i class="ti-check"></i> Đã duyệt
                                                </span>
                                            @else
                                                <span class="status-pill pending">
                                                    <i class="ti-time"></i> Chờ duyệt
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-right" style="white-space: nowrap;">
                                            <div class="m-b-5" style="font-size: 11px; color: #94a3b8;">
                                                {{ $course->updated_at ? $course->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                            </div>
                                            <a href="{{ route('instructor.courses.show', $course->id) }}" class="action-btn-circle view" title="Quản lý chi tiết khóa học">
                                                <i class="ti-eye"></i>
                                            </a>
                                            <a href="{{ route('instructor.courses.lectures.create', $course->id) }}" class="action-btn-circle add" title="Thêm bài giảng mới">
                                                <i class="ti-plus"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted p-5">
                                            <div style="font-size: 40px; color: #cbd5e1; margin-bottom: 10px;">
                                                <i class="ti-book"></i>
                                            </div>
                                            <h6 style="color: #64748b;">Bạn chưa tạo khóa học nào</h6>
                                            <a href="{{ route('instructor.courses.create') }}" class="btn btn-sm btn-primary m-t-10">
                                                <i class="ti-plus"></i> Tạo khóa học đầu tiên
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bình luận mới nhất trong khóa học & bài học -->
            <div class="row">
                <!-- Bình luận khóa học -->
                <div class="col-lg-6 col-12">
                    <div class="card inst-table-card">
                        <div class="card-header border-bottom p-3 d-flex align-items-center justify-content-between" style="background: #fff;">
                            <div class="d-flex align-items-center">
                                <div class="m-r-10" style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                    <i class="ti-comments"></i>
                                </div>
                                <h5 class="m-b-0" style="font-weight: 700; color: #1e293b; font-size: 15px;">Bình luận khóa học</h5>
                            </div>
                            <span class="badge badge-light" style="font-size: 11px; padding: 4px 8px; border-radius: 6px;">Mới nhất</span>
                        </div>
                        <div class="card-body p-3">
                            @forelse($commentInCourse as $comment)
                                @php
                                    $userAvatar = $comment->user && $comment->user->avatar 
                                        ? str_replace('public/', '', asset($comment->user->avatar)) 
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user ? $comment->user->name : 'User') . '&background=2563eb&color=fff';
                                @endphp
                                <div class="d-flex m-b-20 p-b-15 border-bottom">
                                    <img src="{{ $userAvatar }}" alt="" class="comment-avatar m-r-12"
                                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=User&background=2563eb&color=fff';"/>
                                    <div style="flex: 1; min-width: 0;">
                                        <div class="d-flex align-items-center justify-content-between m-b-5">
                                            <div style="font-weight: 600; color: #1e293b; font-size: 13px;">
                                                {{ $comment->user ? $comment->user->name : 'Học viên' }}
                                            </div>
                                            <small class="text-muted" style="font-size: 11px;">
                                                <i class="ti-time"></i> {{ $comment->created_at ? $comment->created_at->diffForHumans() : '' }}
                                            </small>
                                        </div>
                                        <div class="comment-box m-b-8">
                                            {{ $comment->content }}
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <small class="text-muted text-truncate" style="max-width: 280px; font-size: 12px;">
                                                <i class="ti-book text-primary"></i> 
                                                @if($comment->course)
                                                    <a href="{{ route('instructor.courses.show', $comment->course->id) }}" style="color: #475569; font-weight: 500;">
                                                        {{ $comment->course->title }}
                                                    </a>
                                                @else
                                                    <span>Khóa học</span>
                                                @endif
                                            </small>
                                            @if($comment->course)
                                                <a href="{{ '/courses/' . $comment->course->id . '#li-comment-' . $comment->id }}" target="_blank" class="btn btn-xs" style="background: #eff6ff; color: #2563eb; font-weight: 600; border-radius: 6px; padding: 3px 8px;">
                                                    Phản hồi <i class="ti-arrow-top-right"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted p-4">
                                    <div style="font-size: 32px; color: #cbd5e1; margin-bottom: 8px;">
                                        <i class="ti-comment"></i>
                                    </div>
                                    <div style="font-size: 13px; color: #64748b;">Chưa có bình luận nào trong các khóa học</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Bình luận bài học -->
                <div class="col-lg-6 col-12">
                    <div class="card inst-table-card">
                        <div class="card-header border-bottom p-3 d-flex align-items-center justify-content-between" style="background: #fff;">
                            <div class="d-flex align-items-center">
                                <div class="m-r-10" style="width: 32px; height: 32px; border-radius: 8px; background: #fdf4ff; color: #a855f7; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                    <i class="ti-comment-alt"></i>
                                </div>
                                <h5 class="m-b-0" style="font-weight: 700; color: #1e293b; font-size: 15px;">Bình luận bài học</h5>
                            </div>
                            <span class="badge badge-light" style="font-size: 11px; padding: 4px 8px; border-radius: 6px;">Mới nhất</span>
                        </div>
                        <div class="card-body p-3">
                            @forelse($commentInLecture as $comment)
                                @php
                                    $userAvatar = $comment->user && $comment->user->avatar 
                                        ? str_replace('public/', '', asset($comment->user->avatar)) 
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user ? $comment->user->name : 'User') . '&background=a855f7&color=fff';
                                @endphp
                                <div class="d-flex m-b-20 p-b-15 border-bottom">
                                    <img src="{{ $userAvatar }}" alt="" class="comment-avatar m-r-12"
                                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=User&background=a855f7&color=fff';"/>
                                    <div style="flex: 1; min-width: 0;">
                                        <div class="d-flex align-items-center justify-content-between m-b-5">
                                            <div style="font-weight: 600; color: #1e293b; font-size: 13px;">
                                                {{ $comment->user ? $comment->user->name : 'Học viên' }}
                                            </div>
                                            <small class="text-muted" style="font-size: 11px;">
                                                <i class="ti-time"></i> {{ $comment->created_at ? $comment->created_at->diffForHumans() : '' }}
                                            </small>
                                        </div>
                                        <div class="comment-box m-b-8">
                                            {{ $comment->content }}
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <small class="text-muted text-truncate" style="max-width: 280px; font-size: 12px;">
                                                <i class="ti-video-clapper text-primary"></i> 
                                                @if($comment->lecture)
                                                    <span style="color: #475569; font-weight: 500;">{{ $comment->lecture->title }}</span>
                                                @else
                                                    <span>Bài học</span>
                                                @endif
                                            </small>
                                            @if($comment->lecture && $comment->lecture->course)
                                                <a href="{{ '/courses/' . $comment->lecture->course->id . '/lectures/' . $comment->lecture_id . '#li-comment-' . $comment->id }}" target="_blank" class="btn btn-xs" style="background: #fdf4ff; color: #9333ea; font-weight: 600; border-radius: 6px; padding: 3px 8px;">
                                                    Phản hồi <i class="ti-arrow-top-right"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted p-4">
                                    <div style="font-size: 32px; color: #cbd5e1; margin-bottom: 8px;">
                                        <i class="ti-comment"></i>
                                    </div>
                                    <div style="font-size: 13px; color: #64748b;">Chưa có bình luận nào trong các bài học</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Wrapper END -->
@endsection
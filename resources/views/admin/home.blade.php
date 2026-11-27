@extends('admin.layouts.default')

@section('title')
    {{ __('titles.r2s_admin_dashboard') }} - Road To School
@endsection

@section('inline_styles')
<style>
    /* Admin Dashboard Custom Modern Styling */
    .dashboard-header {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px 28px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
        margin-bottom: 25px;
    }

    .dashboard-title-wrap h2 {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px 0;
    }
    .dashboard-title-wrap p {
        color: #64748b;
        font-size: 14px;
        margin: 0;
    }

    /* Filter Toolbar: một thanh gọn, nhóm "khoảng thời gian" và "ngày tùy chọn" rõ ràng */
    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 18px;
        margin-top: 18px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
    }
    .filter-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }
    /* Nút chọn nhanh dạng thanh liền nhau */
    .preset-segment {
        display: inline-flex;
        flex-wrap: wrap;
        background: #f1f5f9;
        border-radius: 12px;
        padding: 4px;
        gap: 2px;
    }
    .preset-pill {
        display: inline-flex;
        align-items: center;
        padding: 7px 12px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        background: transparent;
        border: none;
        text-decoration: none !important;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .preset-pill:hover {
        color: #0f172a;
        background: #e2e8f0;
    }
    .preset-pill.active {
        background: #2563eb;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
    }

    /* Khoảng ngày tùy chọn */
    .date-range-form {
        display: inline-flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin: 0;
        padding: 4px 4px 4px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
    }
    .date-range-form.is-custom {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }
    .date-range-form .range-icon { color: #94a3b8; font-size: 14px; }
    .date-range-form .range-sep { color: #94a3b8; font-size: 13px; }
    .date-range-form input[type="date"] {
        padding: 4px 8px;
        border-radius: 8px;
        border: 1px solid transparent;
        font-size: 13px;
        background: #f8fafc;
        color: #0f172a;
        height: 34px;
        width: 128px;
    }
    .date-range-form input[type="date"]:focus {
        border-color: #2563eb;
        outline: none;
        background: #ffffff;
    }
    .date-range-form .btn-apply {
        height: 34px;
        border-radius: 9px;
        font-weight: 600;
        font-size: 13px;
        padding: 0 14px;
        border: none;
        background: #2563eb;
        color: #ffffff;
    }
    .date-range-form .btn-apply:hover { background: #1d4ed8; }

    /* Dòng trạng thái bộ lọc đang áp dụng */
    .filter-status {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 12px;
        font-size: 13px;
        color: #64748b;
    }
    .filter-status i { color: #10b981; }
    .filter-status strong { color: #0f172a; }
    .filter-status a {
        margin-left: auto;
        font-size: 12.5px;
        font-weight: 600;
        color: #2563eb;
        text-decoration: none;
    }
    .filter-status a:hover { text-decoration: underline; }

    /* Metric Cards */
    .stat-cards-row {
        display: flex;
        flex-wrap: wrap;
    }
    .stat-cards-row > [class*="col-"] {
        display: flex;
        flex-direction: column;
    }
    .stat-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
        padding: 22px 24px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex;
        flex-direction: column;
        justify-content: center;
        flex: 1;
        min-height: 120px;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
    }
    .stat-card-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .stat-card-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #ffffff;
        flex-shrink: 0;
    }
    .stat-card-info {
        flex: 1;
        margin-left: 18px;
    }
    .stat-card-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin: 0 0 6px 0;
    }
    .stat-card-value {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px 0;
        line-height: 1.2;
    }
    .stat-card-sub {
        font-size: 12px;
        color: #64748b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Gradients for metric icons */
    .icon-bg-blue { background: linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3); }
    .icon-bg-green { background: linear-gradient(135deg, #059669, #047857); box-shadow: 0 6px 16px rgba(5, 150, 105, 0.3); }
    .icon-bg-purple { background: linear-gradient(135deg, #7c3aed, #6d28d9); box-shadow: 0 6px 16px rgba(124, 58, 237, 0.3); }
    .icon-bg-amber { background: linear-gradient(135deg, #ea580c, #c2410c); box-shadow: 0 6px 16px rgba(234, 88, 12, 0.3); }

    /* Section Cards */
    .dashboard-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .dashboard-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .dashboard-card-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .dashboard-card-body {
        padding: 24px;
    }

    /* Table Improvements */
    .dashboard-table {
        margin-bottom: 0;
        width: 100%;
    }
    .dashboard-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 10px;
        border-top: none;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .dashboard-table td {
        padding: 11px 10px;
        vertical-align: middle;
        font-size: 13px;
        border-color: #f1f5f9;
    }
    .dashboard-table tr:hover td {
        background-color: #f8fafc;
    }
    /* Giãn lề trái/phải để bảng không dính sát mép thẻ */
    .dashboard-table th:first-child, .dashboard-table td:first-child { padding-left: 20px; }
    .dashboard-table th:last-child, .dashboard-table td:last-child { padding-right: 20px; }
    .dashboard-table td { padding-top: 14px; padding-bottom: 14px; }
    .dashboard-table tbody tr:last-child td { border-bottom: none; }

    /* Hai thẻ bảng cao bằng nhau */
    .dashboard-equal { display: flex; flex-wrap: wrap; }
    .dashboard-equal > [class*="col-"] { display: flex; }
    .dashboard-equal .dashboard-card { width: 100%; display: flex; flex-direction: column; }
    .dashboard-equal .dashboard-card .table-responsive { flex: 1; }
    .dashboard-equal .dashboard-card-header { padding: 16px 24px; }

    /* Huy hiệu trạng thái đồng đều */
    .dashboard-table .badge-status-paid,
    .dashboard-table .badge-status-pending,
    .dashboard-table .badge-status-canceled {
        justify-content: center;
    }

    /* Số thứ tự khóa học bán chạy */
    .rank-badge {
        width: 24px; height: 24px; flex-shrink: 0;
        border-radius: 50%;
        background: #f1f5f9; color: #64748b;
        font-size: 12px; font-weight: 800;
        display: inline-flex; align-items: center; justify-content: center;
        margin-right: 12px;
    }
    .rank-badge.rank-1 { background: #fef3c7; color: #b45309; }
    .rank-badge.rank-2 { background: #e2e8f0; color: #475569; }
    .rank-badge.rank-3 { background: #ffedd5; color: #c2410c; }

    .course-table-title {
        font-weight: 700;
        color: #1e293b;
        font-size: 13px;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        word-break: break-word;
        text-decoration: none !important;
        transition: color 0.15s ease;
    }
    .course-table-title:hover {
        color: #2563eb !important;
    }

    .table-responsive {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
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

    .badge-status-paid {
        background: #dcfce7;
        color: #15803d;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .badge-status-pending {
        background: #fef9c3;
        color: #854d0e;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .badge-status-canceled {
        background: #fee2e2;
        color: #b91c1c;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
</style>
@endsection

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- Dashboard Header & Date Filter Toolbar -->
        <div class="dashboard-header">
            <div class="dashboard-title-wrap">
                <h2><i class="fa fa-dashboard" style="color: #2563eb; margin-right: 8px;"></i> Bảng điều khiển</h2>
                <p>Theo dõi doanh thu, số lượng khách hàng và khóa học đã bán</p>
            </div>

            <!-- Filter Card -->
            <div class="filter-card">
                <div class="filter-row">
                    <!-- Chọn nhanh khoảng thời gian -->
                    <div class="preset-segment" role="group" aria-label="Khoảng thời gian">
                        <a href="{{ route('admin.dashboard', ['preset' => 'all']) }}" class="preset-pill {{ $preset === 'all' ? 'active' : '' }}">Tất cả</a>
                        <a href="{{ route('admin.dashboard', ['preset' => 'today']) }}" class="preset-pill {{ $preset === 'today' ? 'active' : '' }}">Hôm nay</a>
                        <a href="{{ route('admin.dashboard', ['preset' => '7_days']) }}" class="preset-pill {{ $preset === '7_days' ? 'active' : '' }}">1 Tuần qua</a>
                        <a href="{{ route('admin.dashboard', ['preset' => '30_days']) }}" class="preset-pill {{ $preset === '30_days' ? 'active' : '' }}">1 Tháng qua</a>
                        <a href="{{ route('admin.dashboard', ['preset' => 'this_month']) }}" class="preset-pill {{ $preset === 'this_month' ? 'active' : '' }}">Tháng này</a>
                        <a href="{{ route('admin.dashboard', ['preset' => 'last_month']) }}" class="preset-pill {{ $preset === 'last_month' ? 'active' : '' }}">Tháng trước</a>
                        <a href="{{ route('admin.dashboard', ['preset' => 'this_year']) }}" class="preset-pill {{ $preset === 'this_year' ? 'active' : '' }}">Năm nay</a>
                    </div>

                    <!-- Chọn khoảng ngày tùy ý -->
                    <form action="{{ route('admin.dashboard') }}" method="GET" class="date-range-form {{ $preset === 'custom' ? 'is-custom' : '' }}">
                        <i class="fa fa-calendar range-icon"></i>
                        <input type="date" name="start_date" value="{{ $startDateInput }}" required aria-label="Từ ngày">
                        <span class="range-sep">đến</span>
                        <input type="date" name="end_date" value="{{ $endDateInput }}" required aria-label="Đến ngày">
                        <button type="submit" class="btn-apply">Áp dụng</button>
                    </form>
                </div>

                <!-- Bộ lọc đang áp dụng -->
                <div class="filter-status">
                    <i class="fa fa-check-circle"></i>
                    <span>Đang hiển thị: <strong>{{ $filterTitle }}</strong></span>
                    <a href="{{ route('admin.dashboard') }}"><i class="fa fa-refresh" style="color: inherit;"></i> Đặt lại</a>
                </div>
            </div>
        </div>

        @php
            $pendingCoursesHome = \App\Models\Course::where('is_accepted', 0)->count();
            $pendingLecturesHome = \App\Models\Lecture::where('is_accepted', 0)->count();
        @endphp
        @if($pendingCoursesHome > 0 || $pendingLecturesHome > 0)
            <div class="alert alert-warning d-flex align-items-center justify-content-between p-3 m-b-20 flex-wrap" style="border-radius: 12px; background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.1); gap: 12px;">
                <div class="d-flex align-items-center">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-right: 14px; flex-shrink: 0;">
                        <i class="fa fa-bell"></i>
                    </div>
                    <div>
                        <strong style="color: #92400e; font-size: 14px;">Yêu cầu phê duyệt nội dung mới từ Giảng viên:</strong>
                        <div style="font-size: 13px; color: #78350f; margin-top: 2px;">
                            @if($pendingCoursesHome > 0)
                                Có <strong>{{ $pendingCoursesHome }}</strong> khóa học mới đang chờ phê duyệt.
                            @endif
                            @if($pendingLecturesHome > 0)
                                Có <strong>{{ $pendingLecturesHome }}</strong> bài giảng/quiz đang chờ duyệt.
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    @if($pendingCoursesHome > 0)
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-warning btn-sm" style="font-weight: 700; border-radius: 8px; padding: 7px 15px;">
                            <i class="fa fa-book"></i> Duyệt Khóa học ngay ({{ $pendingCoursesHome }})
                        </a>
                    @endif
                    @if($pendingLecturesHome > 0)
                        <a href="{{ route('admin.lectures.requests.index') }}" class="btn btn-primary btn-sm" style="font-weight: 700; border-radius: 8px; padding: 7px 15px;">
                            <i class="fa fa-video-camera"></i> Duyệt Bài giảng ({{ $pendingLecturesHome }})
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- 4 Top KPI Metric Cards -->
        <div class="row stat-cards-row">
            <!-- 1. Doanh thu -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-card-inner">
                        <div class="stat-card-icon icon-bg-green">
                            <i class="fa fa-money"></i>
                        </div>
                        <div class="stat-card-info">
                            <div class="stat-card-title">Doanh thu</div>
                            <div class="stat-card-value text-success" style="font-size: 22px;">
                                {{ format_price($filteredRevenue) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Khóa học đã bán -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-card-inner">
                        <div class="stat-card-icon icon-bg-blue">
                            <i class="fa fa-graduation-cap"></i>
                        </div>
                        <div class="stat-card-info">
                            <div class="stat-card-title">Khóa học đã bán</div>
                            <div class="stat-card-value text-primary">
                                {{ number_format($filteredCoursesSold) }} <small style="font-size: 14px; font-weight: 500;">khóa</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Khách hàng / Học viên -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-card-inner">
                        <div class="stat-card-icon icon-bg-purple">
                            <i class="fa fa-users"></i>
                        </div>
                        <div class="stat-card-info">
                            <div class="stat-card-title">Học viên</div>
                            <div class="stat-card-value" style="color: #7c3aed;">
                                {{ number_format($totalCustomers) }} <small style="font-size: 14px; font-weight: 500;">người</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Đơn hàng thành công -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-card-inner">
                        <div class="stat-card-icon icon-bg-amber">
                            <i class="fa fa-file-text-o"></i>
                        </div>
                        <div class="stat-card-info">
                            <div class="stat-card-title">Đơn hàng trong kỳ</div>
                            <div class="stat-card-value" style="color: #ea580c;">
                                {{ $filteredPaidBillsCount }} / {{ $filteredTotalBillsCount }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Báo cáo phân bổ Tài chính & Chi phí Giảng viên (Chính sách 70% GV - 30% Sàn) -->
        <div class="row">
            <div class="col-12">
                <div class="dashboard-card" style="border-left: 4px solid #2563eb; margin-bottom: 24px;">
                    <div class="dashboard-card-body" style="padding: 22px 24px;">
                        <div class="row">
                            <!-- 1. Tổng doanh thu 100% -->
                            <div class="col-md-4 border-right-md">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #e0f2fe; color: #0284c7; font-size: 22px; margin-right: 16px; flex-shrink: 0;">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <div>
                                        <div style="font-size: 11.5px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">1. Tổng doanh thu (100%)</div>
                                        <div style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 3px 0; line-height: 1.2;">
                                            {{ format_price($filteredRevenue) }}
                                        </div>
                                        <div style="font-size: 12px; color: #64748b;">Tổng tiền thanh toán từ học viên qua cổng SePay</div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Chi trả Giảng viên 70% -->
                            <div class="col-md-4 border-right-md">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #fef3c7; color: #d97706; font-size: 22px; margin-right: 16px; flex-shrink: 0;">
                                        <i class="fa fa-graduation-cap"></i>
                                    </div>
                                    <div>
                                        <div style="font-size: 11.5px; color: #d97706; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">2. Chi trả Giảng viên (70%)</div>
                                        <div style="font-size: 22px; font-weight: 800; color: #b45309; margin: 3px 0; line-height: 1.2;">
                                            {{ format_price($instructorCost) }}
                                        </div>
                                        <div style="font-size: 12px; color: #64748b;">Số tiền đối soát quyết toán trả các Giảng viên</div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Lợi nhuận sàn 30% -->
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #dcfce7; color: #15803d; font-size: 22px; margin-right: 16px; flex-shrink: 0;">
                                        <i class="fa fa-shield"></i>
                                    </div>
                                    <div>
                                        <div style="font-size: 11.5px; color: #15803d; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">3. Lợi nhuận sàn R2S (30%)</div>
                                        <div style="font-size: 22px; font-weight: 800; color: #15803d; margin: 3px 0; line-height: 1.2;">
                                            {{ format_price($platformProfit) }}
                                        </div>
                                        <div style="font-size: 12px; color: #64748b;">Phí vận hành server & hạ tầng công nghệ</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Doanh thu theo thời gian -->
            <div class="col-md-8">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h4 class="dashboard-card-title">
                            <i class="fa fa-line-chart text-primary"></i> Biến động Doanh thu & Khóa học bán ra
                        </h4>
                        <span class="badge badge-pill badge-info font-weight-bold" style="padding: 6px 12px;">
                            {{ $filterTitle }}
                        </span>
                    </div>
                    <div class="dashboard-card-body">
                        <div style="height: 320px; position: relative;">
                            <canvas id="revenueTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Phân bổ tài khoản hệ thống -->
            <div class="col-md-4">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h4 class="dashboard-card-title">
                            <i class="fa fa-pie-chart text-success"></i> Cơ cấu người dùng
                        </h4>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-default btn-xs">Xem chi tiết</a>
                    </div>
                    <div class="dashboard-card-body">
                        <div style="height: 200px; position: relative;">
                            <canvas id="userDoughnutChart"></canvas>
                        </div>
                        <div class="m-t-20">
                            <div class="d-flex justify-content-between align-items-center p-v-8 border-bottom">
                                <span><span class="status success"></span> Quản trị viên</span>
                                <b>{{ $count['adminsCount'] }} ({{ $count['totalUsers'] > 0 ? round(($count['adminsCount']/$count['totalUsers'])*100, 1) : 0 }}%)</b>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-v-8 border-bottom">
                                <span><span class="status primary"></span> Giảng viên</span>
                                <b>{{ $count['teachersCount'] }} ({{ $count['totalUsers'] > 0 ? round(($count['teachersCount']/$count['totalUsers'])*100, 1) : 0 }}%)</b>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-v-8">
                                <span><span class="status info"></span> Học viên / Khách hàng</span>
                                <b>{{ $count['studentsCount'] }} ({{ $count['totalUsers'] > 0 ? round(($count['studentsCount']/$count['totalUsers'])*100, 1) : 0 }}%)</b>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="row dashboard-equal">
            <!-- Đơn hàng trong kỳ lọc -->
            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h4 class="dashboard-card-title">
                            <i class="fa fa-shopping-cart text-primary"></i> Đơn hàng trong kỳ lọc
                        </h4>
                        <a href="{{ route('admin.bills.index') }}" class="btn btn-default btn-xs">Xem tất cả</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table dashboard-table mb-0">
                            <thead>
                                <tr>
                                    <th>Đơn hàng</th>
                                    <th>Khách hàng</th>
                                    <th>Trạng thái</th>
                                    <th style="text-align: right;">Tổng tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($newestBills as $bill)
                                    <tr>
                                        <td style="white-space: nowrap;">
                                            <a href="{{ route('admin.bills.show', $bill->id) }}" class="font-weight-bold text-primary font-size-14">
                                                #{{ $bill->id }}
                                            </a>
                                            <small class="text-muted d-block" style="font-size: 11px; margin-top: 2px;">{{ $bill->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark" style="font-size: 13px; line-height: 1.3;">{{ $bill->customer_name }}</div>
                                            <small class="text-muted d-block" style="font-size: 11px; margin-top: 2px;">{{ $bill->customer_phone }}</small>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            @if($bill->status == \App\Models\Bill::ALREADY_PAID || $bill->status == \App\Models\Bill::ACTIVATED)
                                                <span class="badge-status-paid"><i class="fa fa-check"></i> {{ \App\Models\Bill::$status[$bill->status] }}</span>
                                            @elseif($bill->status == \App\Models\Bill::CANCELED)
                                                <span class="badge-status-canceled">{{ \App\Models\Bill::$status[$bill->status] }}</span>
                                            @else
                                                <span class="badge-status-pending">{{ \App\Models\Bill::$status[$bill->status] }}</span>
                                            @endif
                                        </td>
                                        <td style="white-space: nowrap; text-align: right;"><b class="text-dark" style="font-size: 13px;">{{ format_price($bill->total_amount) }}</b></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted p-v-30">
                                            <i class="fa fa-inbox fa-2x m-b-10 d-block"></i>
                                            Không có đơn hàng nào trong khoảng thời gian đã chọn
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top khóa học bán chạy -->
            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h4 class="dashboard-card-title">
                            <i class="fa fa-trophy text-warning"></i> Khóa học bán chạy trong kỳ lọc
                        </h4>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-default btn-xs">Xem tất cả</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table dashboard-table mb-0">
                            <thead>
                                <tr>
                                    <th>Khóa học</th>
                                    <th style="text-align: center;">Trong kỳ</th>
                                    <th style="text-align: center;">Tổng bán</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCoursesInPeriod as $course)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="rank-badge rank-{{ $loop->iteration }}">{{ $loop->iteration }}</span>
                                                <img src="{{ asset(str_replace('public/', '', $course->course_avatar)) }}"
                                                     style="width: 44px; height: 32px; border-radius: 6px; object-fit: cover; margin-right: 10px; border: 1px solid #e2e8f0; flex-shrink: 0;"
                                                     alt="{{ $course->title }}">
                                                <div style="min-width: 0; flex: 1;">
                                                    <a href="{{ route('courses.show', $course->id) }}" class="course-table-title" title="{{ $course->title }}">
                                                        {{ $course->title }}
                                                    </a>
                                                    <small class="text-muted d-block" style="font-size: 11px; margin-top: 2px;">
                                                        {{ $course->user ? $course->user->name : 'N/A' }}
                                                        &middot; <b style="color: #334155;">{{ format_price($course->promotion_price > 0 && $course->promotion_price < $course->origin_price ? $course->promotion_price : $course->origin_price) }}</b>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center" style="white-space: nowrap;">
                                            <span class="badge badge-info font-weight-bold font-size-12" style="padding: 4px 10px; border-radius: 12px; display: inline-block; background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;">
                                                +{{ $course->period_sales ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="text-center" style="white-space: nowrap;"><b style="font-size: 13px; color: #334155;">{{ number_format($course->seller) }}</b></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted p-v-30">
                                            Không có dữ liệu khóa học bán ra trong khoảng thời gian đã chọn
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
</div>
@endsection

@section('inline_scripts')
<!-- Chart.js -->
<script src="{{ asset('assets/admin/vendor/chart.js/dist/Chart.min.js') }}"></script>
<script>
    $(document).ready(function () {
        // 1. Biểu đồ Doanh thu (cột) & Khóa học bán ra (đường)
        var revenueCtx = document.getElementById('revenueTrendChart').getContext('2d');

        var chartLabels = {!! json_encode($chartLabels) !!};
        var chartRevenue = {!! json_encode($chartRevenueData) !!};
        var chartCourses = {!! json_encode($chartCoursesData) !!};

        var hasRevenue = chartRevenue.some(function (v) { return v > 0; });
        var maxRevenue = Math.max.apply(null, chartRevenue.concat([0]));
        var maxCourses = Math.max.apply(null, chartCourses.concat([0]));

        // Làm tròn trần trục lên số "đẹp" để các vạch không bị lặp giá trị
        function niceMax(v, minimum) {
            if (v <= 0) return minimum;
            var pow = Math.pow(10, Math.floor(Math.log10(v)));
            var n = v / pow;
            var step = n <= 1 ? 1 : n <= 2 ? 2 : n <= 5 ? 5 : 10;
            return Math.max(step * pow, minimum);
        }

        function formatMoneyShort(value) {
            if (value >= 1000000) {
                return (value / 1000000).toFixed(value % 1000000 === 0 ? 0 : 1).replace('.', ',') + ' tr';
            } else if (value >= 1000) {
                return (value / 1000).toFixed(0) + ' k';
            }
            return value;
        }

        if (!hasRevenue) {
            $('#revenueTrendChart').parent().append(
                '<div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#94a3b8;font-size:14px;text-align:center;pointer-events:none;">' +
                '<i class="fa fa-bar-chart" style="font-size:28px;display:block;margin-bottom:8px;"></i>Chưa có doanh thu trong kỳ này</div>'
            );
        }

        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    type: 'line',
                    label: 'Khóa học bán ra',
                    data: chartCourses,
                    yAxisID: 'y-courses',
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#f59e0b',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: chartCourses.map(function (v) { return v > 0 ? 5 : 0; }),
                    pointHoverRadius: 6,
                    fill: false,
                    lineTension: 0,
                    order: 1
                }, {
                    type: 'bar',
                    label: 'Doanh thu (VNĐ)',
                    data: chartRevenue,
                    yAxisID: 'y-revenue',
                    backgroundColor: 'rgba(37, 99, 235, 0.85)',
                    hoverBackgroundColor: '#1d4ed8',
                    borderRadius: 4,
                    barPercentage: 0.5,
                    categoryPercentage: 0.7,
                    maxBarThickness: 46,
                    order: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: { usePointStyle: false, fontColor: '#475569', fontSize: 12, boxWidth: 14, padding: 16 }
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var value = tooltipItem.yLabel;
                            if (tooltipItem.datasetIndex === 1) {
                                return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(value) + ' ₫';
                            }
                            return 'Khóa học bán ra: ' + value;
                        }
                    }
                },
                scales: {
                    xAxes: [{
                        gridLines: { display: false },
                        ticks: { fontColor: '#64748b', fontSize: 12, autoSkip: true, maxRotation: 0 }
                    }],
                    yAxes: [{
                        id: 'y-revenue',
                        position: 'left',
                        gridLines: { color: '#f1f5f9', zeroLineColor: '#e2e8f0' },
                        ticks: {
                            beginAtZero: true,
                            suggestedMax: niceMax(maxRevenue * 1.15, 100000),
                            maxTicksLimit: 6,
                            fontColor: '#64748b',
                            fontSize: 11,
                            callback: formatMoneyShort
                        }
                    }, {
                        id: 'y-courses',
                        position: 'right',
                        gridLines: { drawOnChartArea: false },
                        ticks: {
                            beginAtZero: true,
                            suggestedMax: Math.max(maxCourses, 4),
                            maxTicksLimit: 6,
                            fontColor: '#d97706',
                            fontSize: 11,
                            callback: function (value) {
                                return Number.isInteger(value) ? value : null;
                            }
                        }
                    }]
                }
            }
        });

        // 2. Biểu đồ cơ cấu người dùng
        var userCtx = document.getElementById('userDoughnutChart').getContext('2d');
        var totalUsers = {{ $count['totalUsers'] ?: 1 }};

        new Chart(userCtx, {
            type: 'doughnut',
            data: {
                labels: ["Quản trị viên", "Giảng viên", "Học viên"],
                datasets: [{
                    data: [
                        {{ $count['adminsCount'] }},
                        {{ $count['teachersCount'] }},
                        {{ $count['studentsCount'] }}
                    ],
                    backgroundColor: ['#059669', '#2563eb', '#0284c7'],
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutoutPercentage: 72,
                legend: {
                    display: false
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var dataset = data.datasets[tooltipItem.datasetIndex];
                            var currentValue = dataset.data[tooltipItem.index];
                            var label = data.labels[tooltipItem.index];
                            var percent = ((currentValue / totalUsers) * 100).toFixed(1);
                            return label + ': ' + currentValue + ' (' + percent + '%)';
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
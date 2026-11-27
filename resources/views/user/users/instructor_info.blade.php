@extends('user.layouts.default')

@section('title')
    {{ $selectedInstructor->name }} - {{ __('titles.instructor_information') }}
@endsection

@section('inline_styles')
<style>
    #content {
        background-color: #f8fafc;
        padding: 30px 0 60px;
    }

    .ins-page-wrapper {
        margin-bottom: 20px;
    }

    /* Main Profile Card */
    .ins-profile-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        padding: 36px;
        margin-bottom: 35px;
    }

    /* Left Sidebar: Avatar, Name, Stats, Social */
    .ins-sidebar-box {
        text-align: center;
        padding-right: 25px;
        border-right: 1px solid #f1f5f9;
    }
    @media (max-width: 991px) {
        .ins-sidebar-box {
            padding-right: 0;
            border-right: none;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 30px;
            margin-bottom: 30px;
        }
    }

    .ins-avatar-wrapper {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto 16px;
    }
    .ins-avatar-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #ffffff;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
    }
    .ins-verified-badge {
        position: absolute;
        bottom: 4px;
        right: 10px;
        background: #2563eb;
        color: #ffffff;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        border: 3px solid #ffffff;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.4);
    }

    .ins-name {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px 0;
        line-height: 1.3;
    }

    .ins-role-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 12.5px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 16px;
        margin-bottom: 20px;
    }

    /* Stats Grid */
    .ins-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-bottom: 22px;
    }
    .ins-stat-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 4px;
        text-align: center;
        transition: all 0.2s ease;
    }
    .ins-stat-card:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
    }
    .ins-stat-icon {
        font-size: 16px;
        margin-bottom: 4px;
    }
    .ins-stat-num {
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .ins-stat-label {
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-top: 3px;
    }

    /* Social Links */
    .ins-social-box {
        margin-top: 15px;
    }
    .ins-social-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        margin-bottom: 10px;
    }
    .ins-social-btns {
        display: flex;
        justify-content: center;
        gap: 10px;
    }
    .ins-social-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }
    .ins-social-btn:hover {
        color: #ffffff;
        transform: translateY(-2px);
    }
    .ins-social-btn.fb:hover { background: #1877f2; }
    .ins-social-btn.tw:hover { background: #1da1f2; }
    .ins-social-btn.li:hover { background: #0a66c2; }
    .ins-social-btn.mail:hover { background: #ea4335; }

    /* Right Main Content */
    .ins-content-box {
        padding-left: 15px;
    }
    @media (max-width: 991px) {
        .ins-content-box {
            padding-left: 0;
        }
    }

    .ins-header-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 16px 0;
        padding-bottom: 12px;
        border-bottom: 2px solid #f1f5f9;
    }
    .ins-header-title i {
        color: #2563eb;
    }

    .ins-bio-text {
        font-size: 15px;
        line-height: 1.75;
        color: #334155;
        background: #f8fafc;
        border-left: 4px solid #2563eb;
        padding: 18px 22px;
        border-radius: 0 12px 12px 0;
        margin-bottom: 26px;
    }

    /* Information Grid */
    .ins-info-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 14px 0;
    }
    .ins-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px 20px;
    }
    @media (max-width: 767px) {
        .ins-info-grid {
            grid-template-columns: 1fr;
        }
    }
    .ins-info-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        transition: all 0.2s ease;
    }
    .ins-info-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }
    .ins-info-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }
    .ins-info-body {
        flex: 1;
        min-width: 0;
    }
    .ins-info-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 2px;
    }
    .ins-info-value {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        word-break: break-word;
    }

    /* Courses by Instructor Section */
    .ins-courses-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        padding: 32px;
    }
    .ins-courses-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 24px;
        padding-bottom: 14px;
        border-bottom: 2px solid #f1f5f9;
    }
    .ins-courses-title {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ins-courses-title i {
        color: #2563eb;
    }
    .ins-view-all-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13.5px;
        font-weight: 700;
        color: #2563eb !important;
        text-decoration: none !important;
        padding: 6px 14px;
        border-radius: 20px;
        background: #eff6ff;
        transition: all 0.2s ease;
    }
    .ins-view-all-btn:hover {
        background: #dbeafe;
        color: #1d4ed8 !important;
    }

    /* Modern Course Item in Carousel */
    .ins-course-item {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: all 0.25s ease;
        margin: 8px 4px 14px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }
    .ins-course-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.09);
        border-color: #cbd5e1;
    }
    .ins-course-thumb {
        position: relative;
        height: 150px;
        overflow: hidden;
        background: #0f172a;
    }
    .ins-course-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .ins-course-item:hover .ins-course-thumb img {
        transform: scale(1.05);
    }
    .ins-course-body {
        padding: 16px;
    }
    .ins-course-name {
        font-size: 14.5px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.4;
        height: 40px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        margin-bottom: 10px;
        text-decoration: none !important;
    }
    .ins-course-name:hover {
        color: #2563eb;
    }
    .ins-course-price-wrap {
        display: flex;
        align-items: baseline;
        gap: 8px;
    }
    .ins-course-price {
        font-size: 15px;
        font-weight: 800;
        color: #e11d48;
    }
    .ins-course-old-price {
        font-size: 12.5px;
        color: #94a3b8;
        text-decoration: line-through;
    }
</style>
@endsection

@section('content')
    <div id="content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="ins-page-wrapper">
                        <!-- Profile Card -->
                        <div class="ins-profile-card">
                            <div class="row">
                                <!-- Left Sidebar: Avatar, Name, Stats, Social -->
                                <div class="col-md-4 col-sm-12">
                                    <div class="ins-sidebar-box">
                                        @php
                                            $instructorAvatar = $selectedInstructor->avatar
                                                ? (strpos($selectedInstructor->avatar, 'http') === 0 ? $selectedInstructor->avatar : asset(str_replace('public/', '', $selectedInstructor->avatar)))
                                                : asset('images/default_avatar/teacher.jpg');
                                        @endphp
                                        <div class="ins-avatar-wrapper">
                                            <img class="ins-avatar-img" src="{{ $instructorAvatar }}" alt="{{ $selectedInstructor->name }}" onerror="this.src='{{ asset('images/avatar/basic-avatar.png') }}'">
                                            <div class="ins-verified-badge" title="Giảng viên đã xác minh"><i class="fa fa-check"></i></div>
                                        </div>
                                        <h1 class="ins-name">{{ $selectedInstructor->name }}</h1>
                                        <div class="ins-role-tag"><i class="fa fa-graduation-cap"></i> Giảng viên Road To School</div>

                                        <!-- Stats -->
                                        <div class="ins-stats-grid">
                                            <div class="ins-stat-card">
                                                <div class="ins-stat-icon" style="color: #2563eb;"><i class="fa fa-users"></i></div>
                                                <div class="ins-stat-num">{{ number_format($instructorStudentsCount) }}</div>
                                                <div class="ins-stat-label">Học viên</div>
                                            </div>
                                            <div class="ins-stat-card">
                                                <div class="ins-stat-icon" style="color: #059669;"><i class="fa fa-book"></i></div>
                                                <div class="ins-stat-num">{{ $instructorCoursesCount }}</div>
                                                <div class="ins-stat-label">Khóa học</div>
                                            </div>
                                            <div class="ins-stat-card">
                                                <div class="ins-stat-icon" style="color: #f59e0b;"><i class="fa fa-star"></i></div>
                                                <div class="ins-stat-num">{{ number_format($instructorRating, 1) }}</div>
                                                <div class="ins-stat-label">Đánh giá</div>
                                            </div>
                                        </div>

                                        <!-- Social Contacts -->
                                        <div class="ins-social-box">
                                            <div class="ins-social-title">Liên hệ & Mạng xã hội</div>
                                            <div class="ins-social-btns">
                                                <a class="ins-social-btn fb" href="#" title="Facebook"><i class="fa fa-facebook"></i></a>
                                                <a class="ins-social-btn tw" href="#" title="Twitter"><i class="fa fa-twitter"></i></a>
                                                <a class="ins-social-btn li" href="#" title="LinkedIn"><i class="fa fa-linkedin"></i></a>
                                                @if($selectedInstructor->email)
                                                    <a class="ins-social-btn mail" href="mailto:{{ $selectedInstructor->email }}" title="Email"><i class="fa fa-envelope"></i></a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column: Bio and Information -->
                                <div class="col-md-8 col-sm-12">
                                    <div class="ins-content-box">
                                        <h2 class="ins-header-title">
                                            <i class="fa fa-user-circle-o"></i>
                                            <span>{{ __('titles.instructor_information') }}</span>
                                        </h2>

                                        @if($selectedInstructor->personal_info)
                                            <div class="ins-bio-text">
                                                {!! $selectedInstructor->personal_info !!}
                                            </div>
                                        @else
                                            <div class="ins-bio-text" style="color: #94a3b8; font-style: italic;">
                                                Giảng viên chưa cập nhật thông tin giới thiệu bản thân.
                                            </div>
                                        @endif

                                        <h3 class="ins-info-title"><i class="fa fa-id-card-o" style="color: #2563eb; margin-right: 6px;"></i> Thông tin liên hệ & Công tác</h3>
                                        <div class="ins-info-grid">
                                            <div class="ins-info-item">
                                                <div class="ins-info-icon"><i class="fa fa-envelope-o"></i></div>
                                                <div class="ins-info-body">
                                                    <div class="ins-info-label">Email</div>
                                                    <div class="ins-info-value">{{ $selectedInstructor->email ?: 'Chưa cập nhật' }}</div>
                                                </div>
                                            </div>
                                            <div class="ins-info-item">
                                                <div class="ins-info-icon"><i class="fa fa-phone"></i></div>
                                                <div class="ins-info-body">
                                                    <div class="ins-info-label">Số điện thoại</div>
                                                    <div class="ins-info-value">{{ $selectedInstructor->phone ?: 'Chưa cập nhật' }}</div>
                                                </div>
                                            </div>
                                            <div class="ins-info-item">
                                                <div class="ins-info-icon"><i class="fa fa-birthday-cake"></i></div>
                                                <div class="ins-info-body">
                                                    <div class="ins-info-label">Ngày sinh</div>
                                                    <div class="ins-info-value">{{ $selectedInstructor->birthday ? \Carbon\Carbon::parse($selectedInstructor->birthday)->format('d/m/Y') : 'Chưa cập nhật' }}</div>
                                                </div>
                                            </div>
                                            <div class="ins-info-item">
                                                <div class="ins-info-icon"><i class="fa fa-map-marker"></i></div>
                                                <div class="ins-info-body">
                                                    <div class="ins-info-label">Địa chỉ</div>
                                                    <div class="ins-info-value">{{ $selectedInstructor->address ?: 'Chưa cập nhật' }}</div>
                                                </div>
                                            </div>
                                            <div class="ins-info-item" style="grid-column: 1 / -1;">
                                                <div class="ins-info-icon"><i class="fa fa-building-o"></i></div>
                                                <div class="ins-info-body">
                                                    <div class="ins-info-label">Nơi công tác / Đơn vị</div>
                                                    <div class="ins-info-value">{{ $selectedInstructor->working_place ?: 'Chưa cập nhật' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Courses by Instructor Section -->
                        <div class="ins-courses-card">
                            <div class="ins-courses-header">
                                <h3 class="ins-courses-title">
                                    <i class="fa fa-mortar-board"></i>
                                    <span>{{ __('titles.courses_taught_by') . ' ' . $selectedInstructor->name }}</span>
                                </h3>
                                @if($instructorCoursesCount > 5)
                                    <a class="ins-view-all-btn" href="{{ route('courses.index', ['user_id' => $selectedInstructor->id]) }}">
                                        <span>Xem tất cả {{ $instructorCoursesCount }} khóa học</span>
                                        <i class="fa fa-angle-right"></i>
                                    </a>
                                @endif
                            </div>

                            @if(count($bestCoursesInstructor) > 0)
                                <div id="new-products" class="owl-carousel">
                                    @foreach($bestCoursesInstructor as $course)
                                        <div class="item">
                                            <div class="ins-course-item">
                                                <div class="ins-course-thumb">
                                                    <a href="{{ route('courses.show', $course->id) }}">
                                                        <img src="{{ str_replace('public/', '', asset($course->course_avatar)) }}" alt="{{ $course->title }}">
                                                    </a>
                                                </div>
                                                <div class="ins-course-body">
                                                    <a href="{{ route('courses.show', $course->id) }}" class="ins-course-name" title="{{ $course->title }}">
                                                        {{ $course->title }}
                                                    </a>
                                                    <div class="ins-course-price-wrap">
                                                        @if(isset($course->promotion_price) && $course->promotion_price > 0 && $course->promotion_price < $course->origin_price)
                                                            <span class="ins-course-price">{{ format_price($course->promotion_price) }}</span>
                                                            <span class="ins-course-old-price">{{ format_price($course->origin_price) }}</span>
                                                        @elseif($course->origin_price > 0)
                                                            <span class="ins-course-price">{{ format_price($course->origin_price) }}</span>
                                                        @else
                                                            <span class="ins-course-price" style="color: #10b981;">Miễn phí</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted" style="text-align: center; padding: 25px 0;">Giảng viên hiện chưa có khóa học nào.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('inline_scripts')
@endsection
<!-- Side Nav START -->
<div class="side-nav expand-lg">
    <div class="side-nav-inner">
        <ul class="side-nav-menu scrollable">
            <li class="side-nav-header">
                <span>Điều hướng</span>
            </li>
            @can('check-admin')
                <li class="nav-item dropdown {{ setOpen('admin') }}">
                    <a class="dropdown-toggle" href="{{ route('admin.dashboard') }}">
                                    <span class="icon-holder">
                                        <i class="mdi mdi-view-dashboard"></i>
                                    </span>
                        <span class="title">Bảng điều khiển</span>
                    </a>
                </li>
                <li class="nav-item dropdown {{ setOpen('admin/users') }}">
                    <a class="dropdown-toggle" href="javascript:void(0);">
                                    <span class="icon-holder">
                                        <i class="mdi mdi-account"></i>
                                    </span>
                        <span class="title">Người dùng</span>
                        <span class="arrow">
                                        <i class="mdi mdi-chevron-right"></i>
                                    </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li {{ setActive('admin/users') }}>
                            <a href="{{ route('admin.users.index') }}">Tất cả người dùng</a>
                        </li>
                        <li {{ setActive('admin/users/instructor_ranking') }}>
                            <a href="{{ route('admin.instructor_ranking') }}">Xếp hạng giảng viên</a>
                        </li>
                        <li {{ setActive('admin/users/create_instructor') }}>
                            <a href="{{ route('admin.users.create_instructor') }}">Tạo giảng viên mới</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown {{ setOpen('admin/permissions') }}">
                    <a class="dropdown-toggle" href="javascript:void(0);">
                                    <span class="icon-holder">
                                        <i class="mdi mdi-account-star"></i>
                                    </span>
                        <span class="title">Phân quyền</span>
                        <span class="arrow">
                                        <i class="mdi mdi-chevron-right"></i>
                                    </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li {{ setActive('admin/permissions') }}>
                            <a href="{{ route('admin.permissions.index') }}">Quản lý phân quyền</a>
                        </li>
                        <li {{ setActive('admin/permissions/create') }}>
                            <a href="{{ route('admin.permissions.create') }}">Thêm quyền mới</a>
                        </li>
                    </ul>
                </li>
                @php
                    $pendingCoursesSide = \App\Models\Course::where('is_accepted', 0)->count();
                    $pendingLecturesSide = \App\Models\Lecture::where('is_accepted', 0)->count();
                @endphp
                <li class="nav-item dropdown {{ setOpen('admin/courses') }}">
                    <a class="dropdown-toggle" href="javascript:void(0);">
                <span class="icon-holder">
                <i class="mdi mdi-book-multiple"></i>
                </span>
                        <span class="title">Khóa học</span>
                        @if($pendingCoursesSide > 0)
                            <span class="badge badge-warning" style="margin-left: 6px; font-size: 11px; padding: 2px 7px; border-radius: 10px;">{{ $pendingCoursesSide }}</span>
                        @endif
                        <span class="arrow">
                <i class="mdi mdi-chevron-right"></i>
                </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li {{ setActive('admin/courses') }}>
                            <a href="{{ route('admin.courses.index') }}">
                                Tất cả khóa học
                                @if($pendingCoursesSide > 0)
                                    <span class="badge badge-warning" style="float: right; margin-top: 3px; font-size: 10px; padding: 2px 6px;">{{ $pendingCoursesSide }} chờ</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown {{ setOpen('admin/lectures') }}">
                    <a class="dropdown-toggle" href="javascript:void(0);">
                <span class="icon-holder">
                <i class="mdi mdi-arrange-send-to-back"></i>
                </span>
                        <span class="title">Yêu cầu bài giảng</span>
                        @if($pendingLecturesSide > 0)
                            <span class="badge badge-warning" style="margin-left: 6px; font-size: 11px; padding: 2px 7px; border-radius: 10px;">{{ $pendingLecturesSide }}</span>
                        @endif
                        <span class="arrow">
                <i class="mdi mdi-chevron-right"></i>
                </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li {{ setActive('admin/lectures/requests') }}>
                            <a href="{{ route('admin.lectures.requests.index') }}">
                                Tất cả yêu cầu
                                @if($pendingLecturesSide > 0)
                                    <span class="badge badge-warning" style="float: right; margin-top: 3px; font-size: 10px; padding: 2px 6px;">{{ $pendingLecturesSide }} chờ</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown {{ setOpen('admin/categories') }}">
                    <a class="dropdown-toggle" href="javascript:void(0);">
                <span class="icon-holder">
                <i class="mdi mdi-dice-multiple"></i>
                </span>
                        <span class="title">Danh mục</span>
                        <span class="arrow">
                <i class="mdi mdi-chevron-right"></i>
                </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li {{ setActive('admin/categories') }}>
                            <a href="{{ route('admin.categories.index') }}">Tất cả danh mục</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown {{ setOpen('admin/bills') }}">
                    <a class="dropdown-toggle" href="javascript:void(0);">
                                    <span class="icon-holder">
                                        <i class="mdi mdi-receipt"></i>
                                    </span>
                        <span class="title">Đơn hàng</span>
                        <span class="arrow">
                                        <i class="mdi mdi-chevron-right"></i>
                                    </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li {{ setActive('admin/bills') }}>
                            <a href="{{ route('admin.bills.index') }}">Tất cả đơn hàng</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown {{ setOpen('admin/conversations') }}">
                    <a class="dropdown-toggle" href="javascript:void(0);">
                                    <span class="icon-holder">
                                        <i class="mdi mdi-message-reply-text"></i>
                                    </span>
                        <span class="title">Cuộc trò chuyện</span>
                        <span class="arrow">
                                        <i class="mdi mdi-chevron-right"></i>
                                    </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li {{ setActive('admin/conversations/waiting') }}>
                            <a href="{{ route('admin.conversations.waiting') }}">Cuộc trò chuyện chờ</a>
                        </li>
                    </ul>
                </li>
            @elsecan('check-instructor')
                <li class="nav-item dropdown {{ setOpen('instructor') }}">
                    <a class="dropdown-toggle" href="{{ route('instructor.dashboard') }}">
                                    <span class="icon-holder">
                                        <i class="mdi mdi-view-dashboard"></i>
                                    </span>
                        <span class="title">Bảng điều khiển</span>
                    </a>
                </li>

                <li class="nav-item dropdown {{ setOpen('instructor/courses') }}">
                    <a class="dropdown-toggle" href="javascript:void(0);">
                <span class="icon-holder">
                <i class="mdi mdi-book-multiple"></i>
                </span>
                        <span class="title">Khóa học</span>
                        <span class="arrow">
                <i class="mdi mdi-chevron-right"></i>
                </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li {{ setActive('instructor/courses') }}>
                            <a href="{{ route('instructor.courses.index') }}">Khóa học của tôi</a>
                        </li>
                        <li {{ setActive('instructor/courses/create') }}>
                            <a href="{{ route('instructor.courses.create') }}">Tạo khóa học mới</a>
                        </li>
                    </ul>
                </li>

                @php $qaUnanswered = \App\Http\Controllers\Instructor\QaController::unansweredCount(\Auth::user()); @endphp
                <li class="nav-item dropdown {{ setOpen('instructor/qa') }}">
                    <a class="dropdown-toggle" href="{{ route('instructor.qa.index') }}">
                        <span class="icon-holder">
                            <i class="mdi mdi-comment-question-outline"></i>
                        </span>
                        <span class="title">Hỏi đáp
                            @if($qaUnanswered > 0)
                                <span class="badge badge-pill badge-danger" style="margin-left: 6px;">{{ $qaUnanswered }}</span>
                            @endif
                        </span>
                    </a>
                </li>
            @endcan
            {{--<li class="nav-item dropdown">--}}
            {{--<a class="dropdown-toggle" href="javascript:void(0);">--}}
            {{--<span class="icon-holder">--}}
            {{--<i class="mdi mdi-tune-vertical"></i>--}}
            {{--</span>--}}
            {{--<span class="title">Tables</span>--}}
            {{--<span class="arrow">--}}
            {{--<i class="mdi mdi-chevron-right"></i>--}}
            {{--</span>--}}
            {{--</a>--}}
            {{--<ul class="dropdown-menu">--}}
            {{--<li>--}}
            {{--<a href="basic-table.html">Basic Table</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="data-table.html">Data Table</a>--}}
            {{--</li>--}}
            {{--</ul>--}}
            {{--</li>--}}
            {{--<li class="nav-item dropdown">--}}
            {{--<a class="dropdown-toggle" href="javascript:void(0);">--}}
            {{--<span class="icon-holder">--}}
            {{--<i class="mdi mdi-chart-donut"></i>--}}
            {{--</span>--}}
            {{--<span class="title">Charts</span>--}}
            {{--<span class="arrow">--}}
            {{--<i class="mdi mdi-chevron-right"></i>--}}
            {{--</span>--}}
            {{--</a>--}}
            {{--<ul class="dropdown-menu">--}}
            {{--<li>--}}
            {{--<a href="chartist.html">Chartist</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="chartjs.html">ChartJs</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="sparkline.html">Sparkline</a>--}}
            {{--</li>--}}
            {{--</ul>--}}
            {{--</li>--}}
            {{--<li class="nav-item dropdown">--}}
            {{--<a class="dropdown-toggle" href="javascript:void(0);">--}}
            {{--<span class="icon-holder">--}}
            {{--<i class="mdi mdi-map-marker-outline"></i>--}}
            {{--</span>--}}
            {{--<span class="title">Map</span>--}}
            {{--<span class="arrow">--}}
            {{--<i class="mdi mdi-chevron-right"></i>--}}
            {{--</span>--}}
            {{--</a>--}}
            {{--<ul class="dropdown-menu">--}}
            {{--<li>--}}
            {{--<a href="google-map.html">Google Map</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="vector-map.html">Vector Map</a>--}}
            {{--</li>--}}
            {{--</ul>--}}
            {{--</li>--}}
            {{--<li class="side-nav-header">--}}
            {{--<span>Others</span>--}}
            {{--</li>--}}
            {{--<li class="nav-item dropdown">--}}
            {{--<a class="dropdown-toggle" href="javascript:void(0);">--}}
            {{--<span class="icon-holder">--}}
            {{--<i class="mdi mdi-image-filter-tilt-shift"></i>--}}
            {{--</span>--}}
            {{--<span class="title">Extra</span>--}}
            {{--<span class="arrow">--}}
            {{--<i class="mdi mdi-chevron-right"></i>--}}
            {{--</span>--}}
            {{--</a>--}}
            {{--<ul class="dropdown-menu">--}}
            {{--<li>--}}
            {{--<a href="profile.html">Profile</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="invoice.html">Invoice</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="faq.html">FAQ</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="gallery.html">Gallery</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="login.html">Login</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="login-2.html">Login 2</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="sign-up.html">Sign Up</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="404.html">404</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="500.html">500</a>--}}
            {{--</li>--}}
            {{--</ul>--}}
            {{--</li>--}}
            {{--<li class="nav-item dropdown">--}}
            {{--<a class="dropdown-toggle" href="javascript:void(0);">--}}
            {{--<span class="icon-holder">--}}
            {{--<i class="mdi mdi-menu"></i>--}}
            {{--</span>--}}
            {{--<span class="title">Multiple Levels</span>--}}
            {{--<span class="arrow">--}}
            {{--<i class="mdi mdi-chevron-right"></i>--}}
            {{--</span>--}}
            {{--</a>--}}
            {{--<ul class="dropdown-menu">--}}
            {{--<li class="nav-item dropdown">--}}
            {{--<a href="javascript:void(0);">--}}
            {{--<span>Level 1.2</span>--}}
            {{--</a>--}}
            {{--</li>--}}
            {{--<li class="nav-item dropdown">--}}
            {{--<a href="javascript:void(0);">--}}
            {{--<span>Level 1.1</span>--}}
            {{--<span class="arrow">--}}
            {{--<i class="mdi mdi-chevron-right"></i>--}}
            {{--</span>--}}
            {{--</a>--}}
            {{--<ul class="dropdown-menu">--}}
            {{--<li>--}}
            {{--<a href="javascript:void(0);">Level 2</a>--}}
            {{--</li>--}}
            {{--</ul>--}}
            {{--</li>--}}
            {{--</ul>--}}
            {{--</li>--}}
        </ul>
    </div>
</div>
<!-- Side Nav END -->

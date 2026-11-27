@extends('user.layouts.default')

@section('title')
    {{ __('titles.my_course') }}
@endsection

@section('inline_styles')
    <style>
        /* Modern Clean My Courses Page */
        .modern-dashboard-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px 28px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
            margin-bottom: 30px;
        }

        .dashboard-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 18px;
            margin-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
            flex-wrap: wrap;
            gap: 12px;
        }

        .dashboard-header-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .dashboard-header-title h2 {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dashboard-header-title i {
            color: #2563eb;
            font-size: 20px;
        }

        .dashboard-counter-pill {
            font-size: 12.5px;
            font-weight: 600;
            color: #2563eb;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 4px 12px;
            border-radius: 20px;
        }

        /* Toolbar */
        .dashboard-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 16px;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .toolbar-search-wrap {
            position: relative;
            min-width: 260px;
            flex: 1 1 320px;
        }

        .toolbar-search-wrap i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 13.5px;
        }

        .toolbar-search-input {
            width: 100%;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 20px;
            padding: 7px 14px 7px 34px;
            font-size: 13px;
            color: #1e293b;
            outline: none;
            transition: all 0.2s ease;
        }

        .toolbar-search-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        /* Modern Table */
        .modern-table-wrap {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            background: #ffffff;
        }

        .modern-course-table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .modern-course-table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 12.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
            border-top: none;
        }

        .modern-course-table tbody td {
            padding: 16px;
            vertical-align: middle;
            border-top: 1px solid #f1f5f9;
        }

        .modern-course-table tbody tr:hover {
            background-color: #fbfcfe;
        }

        .td-thumb {
            width: 120px;
        }

        .course-thumb-box {
            display: block;
            width: 110px;
            height: 68px;
            border-radius: 8px;
            overflow: hidden;
            background: #0f172a;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .course-thumb-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .course-thumb-box:hover img {
            transform: scale(1.06);
        }

        .td-details {
            min-width: 220px;
        }

        .course-table-title {
            font-size: 14.5px;
            font-weight: 700;
            line-height: 1.4;
            margin: 0 0 6px;
        }

        .course-table-title a {
            color: #0f172a !important;
            text-decoration: none !important;
            transition: color 0.18s ease;
        }

        .course-table-title a:hover {
            color: #2563eb !important;
        }

        .course-table-meta {
            font-size: 12.5px;
            color: #64748b;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        .course-table-meta a {
            color: #475569;
            font-weight: 500;
            text-decoration: none;
        }

        .course-table-meta a:hover {
            color: #2563eb;
        }

        .meta-separator {
            color: #cbd5e1;
        }

        /* Progress Column */
        .td-progress-col {
            width: 180px;
        }

        .progress-box-wrap {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .progress-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
        }

        .progress-percent-val {
            font-weight: 700;
            color: #2563eb;
        }

        .progress-count-val {
            color: #64748b;
        }

        .modern-progress-bar-bg {
            width: 100%;
            height: 7px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .modern-progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #2563eb 0%, #3b82f6 100%);
            border-radius: 10px;
            transition: width 0.4s ease;
        }

        /* Action Column */
        .td-action-col {
            width: 130px;
            text-align: right;
        }

        .btn-study-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #2563eb;
            color: #ffffff !important;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none !important;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
            white-space: nowrap;
        }

        .btn-study-action:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        /* Clean Empty State */
        .modern-empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-icon-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #f1f5f9;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin: 0 auto 18px;
        }

        .modern-empty-state h4 {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 8px;
        }

        .modern-empty-state p {
            font-size: 14px;
            color: #64748b;
            margin: 0 0 22px;
            max-width: 440px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-empty-explore {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #2563eb;
            color: #ffffff !important;
            font-size: 14px;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 8px;
            text-decoration: none !important;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .btn-empty-explore:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }
    </style>
@endsection

@section('content')
    {{ Breadcrumbs::render('my_course', \Auth::user()->id) }}<br>
    <div id="content">
        <div class="container">
            <div class="row">
                @include('user.partials.user_management_info')
                <div class="col-sm-9 page-content" id="cart">
                    <div class="modern-dashboard-card">
                        <div class="dashboard-header-row">
                            <div class="dashboard-header-title">
                                <i class="fa fa-book"></i>
                                <h2>{{ __('titles.my_course') }}</h2>
                            </div>
                            <span class="dashboard-counter-pill">{{ count($courseList) }} khóa học</span>
                        </div>

                        @if($courseList->isEmpty())
                            <div class="modern-empty-state">
                                <div class="empty-icon-circle">
                                    <i class="fa fa-graduation-cap"></i>
                                </div>
                                <h4>{{ __('messages.you_haven_not_added_any_courses') }}</h4>
                                <p>Bạn chưa tham gia khóa học nào. Hãy khám phá ngay các khóa học chất lượng cao để bắt đầu lộ trình học tập của mình!</p>
                                <a class="btn-empty-explore" href="{{ route('home') }}">
                                    <i class="fa fa-compass"></i> {{ __('titles.keep_shopping') }}
                                </a>
                            </div>
                        @else
                            <div class="dashboard-toolbar">
                                <div class="toolbar-search-wrap">
                                    <i class="fa fa-search"></i>
                                    <input class="toolbar-search-input" id="filter" type="text" placeholder="Tìm kiếm trong khóa học của tôi...">
                                </div>
                            </div>

                            <div class="modern-table-wrap">
                                <table class="modern-course-table" id="in-cart-item-table">
                                    <thead>
                                    <tr>
                                        <th class="td-thumb">{{ __('titles.image') }}</th>
                                        <th class="td-details">{{ __('titles.course_detail') }}</th>
                                        <th class="td-progress-col">{{ __('titles.process') }}</th>
                                        <th class="td-action-col">{{ __('titles.option') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($courseList as $course)
                                        <tr id="cart-item-{{$course->id}}">
                                            <td class="td-thumb">
                                                <a href="{{ route('courses.show', $course->id) }}" class="course-thumb-box">
                                                    <img src="{{ str_replace('public/', '', asset($course->course_avatar)) }}"
                                                         onerror="this.src='{{ asset('images/default_course_avatar/web_development_1.jpeg') }}';"
                                                         alt="{{ $course->title }}">
                                                </a>
                                            </td>
                                            <td class="td-details">
                                                <h4 class="course-table-title">
                                                    <a href="{{ route('courses.show', $course->id) }}" title="{{ $course->title }}">
                                                        {{ $course->title }}
                                                    </a>
                                                </h4>
                                                <div class="course-table-meta">
                                                    <span>
                                                        <i class="fa fa-user-circle-o"></i>
                                                        <a href="{{ route('users.show', $course->user->id) }}">{{ $course->user->name }}</a>
                                                    </span>
                                                    <span class="meta-separator">•</span>
                                                    <span><i class="fa fa-play-circle-o"></i> {{ $course->lecture_numbers }} bài giảng</span>
                                                    <span class="meta-separator">•</span>
                                                    <span><i class="fa fa-clock-o"></i> {{ $course->duration }} phút</span>
                                                </div>
                                            </td>
                                            <td class="td-progress-col">
                                                <div class="progress-box-wrap">
                                                    <div class="progress-label-row">
                                                        <span class="progress-percent-val">{{ $course->process }}%</span>
                                                        <span class="progress-count-val">{{ $course->learnedCount }}/{{ $course->totalLecture }} bài</span>
                                                    </div>
                                                    <div class="modern-progress-bar-bg">
                                                        <div class="modern-progress-bar-fill" style="width: {{ min(100, max(0, $course->process)) }}%;"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="td-action-col">
                                                <a class="btn-study-action" href="{{ route('courses.show', $course->id) }}">
                                                    <i class="fa fa-play"></i> Vào học
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('inline_scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            // Lọc tìm kiếm thời gian thực
            $('#filter').on('keyup', function () {
                var value = $(this).val().toLowerCase().trim();
                $("#in-cart-item-table tbody tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        });
    </script>
@endsection

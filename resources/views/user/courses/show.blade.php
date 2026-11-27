@extends('user.layouts.default')

@section('title')
    {{ $selectedCourse->title . ' - ' . __('titles.course') }}
@endsection

@section('inline_styles')
    <style>
        .ratings {
            position: relative;
            vertical-align: middle;
            display: inline-block;
            color: #b1b1b1;
            overflow: hidden;
        }

        .full-stars {
            position: absolute;
            left: 0;
            top: 0;
            white-space: nowrap;
            overflow: hidden;
            color: #fde16d;
        }

        .empty-stars:before, .full-stars:before {
            content: "\2605\2605\2605\2605\2605";
            font-size: 14pt;
        }

        .empty-stars:before {
            -webkit-text-stroke: 1px #848484;
        }

        .full-stars:before {
            -webkit-text-stroke: 1px orange;
        }

        /* Webkit-text-stroke is not supported on firefox or IE */

        /* Firefox */
        @-moz-document url-prefix() {
            .full-stars {
                color: #ECBE24;
            }
        }

        div.stars {
            width: 270px;
            display: inline-block;
        }

        input.star {
            display: none;
        }

        label.star {
            float: right;
            padding: 10px;
            font-size: 36px;
            color: #444;
            transition: all .2s;
        }

        input.star:checked ~ label.star:before {
            content: '\f005';
            color: #FD4;
            transition: all .25s;
        }

        input.star-5:checked ~ label.star:before {
            color: #FE7;
            text-shadow: 0 0 20px #952;
        }

        input.star-1:checked ~ label.star:before {
            color: #F62;
        }

        label.star:hover {
            transform: rotate(-15deg) scale(1.3);
        }

        label.star:before {
            content: '\f006';
            font-family: FontAwesome;
        }

        /* Modern Course Detail Styling */
        #content {
            background-color: #f8fafc;
            padding: 24px 0 50px;
        }

        .cd-header-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px 28px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            margin-bottom: 24px;
        }

        .cd-category-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 13px;
            font-weight: 600;
            padding: 5px 14px;
            border-radius: 20px;
            margin-bottom: 12px;
            text-decoration: none !important;
            transition: all 0.2s ease;
        }
        .cd-category-pill:hover {
            background: #dbeafe;
            color: #1d4ed8;
            transform: translateY(-1px);
        }

        .cd-course-title {
            font-size: 27px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.35;
            margin: 0 0 14px 0;
            letter-spacing: -0.3px;
        }

        .cd-meta-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 16px 24px;
            padding-bottom: 18px;
            margin-bottom: 18px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            color: #64748b;
        }

        .cd-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .cd-meta-item i {
            color: #3b82f6;
            font-size: 15px;
        }
        .cd-meta-item a {
            color: #0f172a;
            font-weight: 600;
            text-decoration: none;
        }
        .cd-meta-item a:hover {
            color: #2563eb;
        }

        .cd-rating-score {
            font-weight: 800;
            color: #d97706;
            margin-right: 4px;
        }
        .cd-rating-count {
            color: #64748b;
            font-size: 13px;
            margin-left: 6px;
        }

        .cd-media-container {
            border-radius: 14px;
            overflow: hidden;
            background: #0f172a;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.1);
        }
        .cd-media-container .item img {
            width: 100%;
            max-height: 440px;
            object-fit: cover;
            display: block;
            border-radius: 14px;
        }

        /* Common card box on left */
        .product-info .box {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px 28px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            margin-bottom: 24px;
        }
        .product-info .box .title-2 {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 16px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f5f9;
        }

        /* Right Sidebar Purchase Card */
        .cd-sidebar-sticky {
            position: -webkit-sticky;
            position: sticky;
            top: 20px;
        }

        .cd-purchase-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            padding: 24px;
            margin-bottom: 20px;
            overflow: hidden;
            position: relative;
        }

        .cd-card-badge-header {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #2563eb;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .cd-price-section {
            display: flex;
            align-items: baseline;
            flex-wrap: wrap;
            gap: 10px 14px;
            margin-bottom: 18px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .cd-current-price {
            font-size: 28px;
            font-weight: 800;
            color: #e11d48;
            line-height: 1;
        }
        .cd-original-price {
            font-size: 16px;
            color: #94a3b8;
            text-decoration: line-through;
            font-weight: 500;
        }
        .cd-discount-badge {
            background: #fee2e2;
            color: #dc2626;
            font-weight: 700;
            font-size: 12.5px;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .cd-btn-buy-now {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 13px 20px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff !important;
            border: none;
            border-radius: 10px;
            font-size: 15.5px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
            transition: all 0.25s ease;
            text-decoration: none !important;
            margin-bottom: 10px;
        }
        .cd-btn-buy-now:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(37, 99, 235, 0.45);
        }

        .cd-btn-add-cart {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 20px;
            background: #f8fafc;
            color: #1e293b !important;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            text-decoration: none !important;
            margin-bottom: 12px;
        }
        .cd-btn-add-cart:hover {
            background: #e2e8f0;
            border-color: #94a3b8;
            color: #0f172a !important;
            transform: translateY(-1px);
        }

        .cd-btn-start-learning {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 13px 20px;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff !important;
            border: none;
            border-radius: 10px;
            font-size: 15.5px;
            font-weight: 700;
            text-decoration: none !important;
            box-shadow: 0 6px 18px rgba(5, 150, 105, 0.3);
            transition: all 0.25s ease;
            margin-bottom: 12px;
        }
        .cd-btn-start-learning:hover {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(5, 150, 105, 0.4);
        }

        .cd-enrolled-banner {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 14px;
        }
        .cd-enrolled-banner i {
            font-size: 18px;
            color: #10b981;
        }

        .cd-guarantee-note {
            font-size: 12.5px;
            color: #64748b;
            text-align: center;
            margin: 6px 0 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .cd-guarantee-note i {
            color: #10b981;
        }

        .cd-specs-header {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin: 18px 0 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .cd-specs-list {
            list-style: none;
            padding: 0;
            margin: 0 0 18px;
        }
        .cd-specs-list li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 0;
            font-size: 13.5px;
            color: #475569;
            border-bottom: 1px dashed #f1f5f9;
        }
        .cd-specs-list li:last-child {
            border-bottom: none;
        }
        .cd-specs-list li .spec-label {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .cd-specs-list li .spec-label i {
            width: 18px;
            color: #3b82f6;
            text-align: center;
            font-size: 14px;
        }
        .cd-specs-list li .spec-value {
            font-weight: 600;
            color: #0f172a;
        }

        .cd-share-section {
            padding-top: 14px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .cd-share-label {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .cd-social-links {
            display: flex;
            gap: 8px;
        }
        .cd-social-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #64748b;
            transition: all 0.2s ease;
            text-decoration: none !important;
            font-size: 13px;
        }
        .cd-social-btn:hover {
            color: #ffffff;
            transform: translateY(-2px);
        }
        .cd-social-btn.fb:hover { background: #1877f2; }
        .cd-social-btn.tw:hover { background: #1da1f2; }
        .cd-social-btn.wa:hover { background: #25d366; }
        .cd-social-btn.li:hover { background: #0a66c2; }

        /* Instructor Card */
        .cd-instructor-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            padding: 22px;
            margin-bottom: 20px;
        }
        .cd-instructor-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
        }
        .cd-instructor-avatar {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e2e8f0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
            flex-shrink: 0;
        }
        .cd-instructor-name {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 3px 0;
        }
        .cd-instructor-name a {
            color: #0f172a;
            text-decoration: none;
        }
        .cd-instructor-name a:hover {
            color: #2563eb;
        }
        .cd-instructor-role {
            font-size: 13px;
            color: #64748b;
            margin: 0;
        }
        .cd-instructor-contact {
            font-size: 13px;
            color: #475569;
            margin-bottom: 14px;
        }
        .cd-instructor-contact p {
            margin: 0 0 6px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .cd-instructor-contact i {
            width: 16px;
            color: #3b82f6;
            text-align: center;
        }
        .cd-btn-profile {
            display: block;
            text-align: center;
            padding: 9px 14px;
            border-radius: 8px;
            background: #eff6ff;
            color: #2563eb !important;
            font-weight: 600;
            font-size: 13px;
            border: 1px solid #bfdbfe;
            text-decoration: none !important;
            transition: all 0.2s ease;
        }
        .cd-btn-profile:hover {
            background: #dbeafe;
            color: #1d4ed8 !important;
        }

        /* Trust features compact card */
        .cd-trust-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            padding: 20px;
            margin-bottom: 20px;
        }
        .cd-trust-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .cd-trust-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
        }
        .cd-trust-item:last-child {
            margin-bottom: 0;
        }
        .cd-trust-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        .cd-trust-text h5 {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
            margin: 0 0 2px;
        }
        .cd-trust-text p {
            font-size: 12px;
            color: #64748b;
            margin: 0;
            line-height: 1.4;
        }
    </style>
@endsection

@section('content')
    {{ Breadcrumbs::render('courses_show', $selectedCourse) }}
    <div id="content">
        <div class="container">
            <div class="row">
                <div class="product-info">
                    <div class="col-sm-8">
                        @include('flash::message')
                        <div class="cd-header-card">
                            @if($selectedCourse->category)
                                <a href="/courses?sub_category_id={{ $selectedCourse->category->id }}" class="cd-category-pill">
                                    <i class="fa fa-folder-open-o"></i> {{ $selectedCourse->category->vi_title ?: $selectedCourse->category->title }}
                                </a>
                            @endif
                            <h1 class="cd-course-title">{{ $selectedCourse->title }}</h1>
                            <div class="cd-meta-bar">
                                <div class="cd-meta-item">
                                    <i class="fa fa-user-circle"></i>
                                    <span>{{ __('titles.teacher') }}:</span>
                                    <a href="{{ route('instructor_info', $selectedCourse->user->id) }}">
                                        <strong>{{ $selectedCourse->user->name }}</strong>
                                    </a>
                                </div>
                                <div class="cd-meta-item">
                                    <i class="fa fa-clock-o"></i>
                                    <span>{{ __('titles.updated_at') }}:</span>
                                    <strong>{{ \Carbon\Carbon::parse($selectedCourse->updated_at)->format('d/m/Y') }}</strong>
                                </div>
                                <div class="cd-meta-item">
                                    <span class="cd-rating-score">{{ number_format($selectedCourse->course_rate, 1) }}</span>
                                    <div class="ratings">
                                        <div class="empty-stars"></div>
                                        <div class="full-stars" style="width:{{ $selectedCourse->course_rate * 20 }}%"></div>
                                    </div>
                                    <span class="cd-rating-count">({{ number_format($selectedCourse->seller) }} học viên)</span>
                                </div>
                            </div>
                            <div class="cd-media-container">
                                <div id="owl-demo" class="owl-carousel owl-theme">
                                    <div class="item">
                                        <img src="{{ str_replace('public/', '', asset($selectedCourse->course_avatar)) }}"
                                             alt="{{ $selectedCourse->title }}">
                                    </div>
                                    @if($selectedCourse->course_avatar_2)
                                        <div class="item">
                                            <img src="{{ str_replace('public/', '', asset($selectedCourse->course_avatar_2)) }}"
                                                 alt="{{ $selectedCourse->title }}">
                                        </div>
                                    @endif
                                    @if($selectedCourse->course_avatar_3)
                                        <div class="item">
                                            <img src="{{ str_replace('public/', '', asset($selectedCourse->course_avatar_3)) }}"
                                                 alt="{{ $selectedCourse->title }}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="box">
                            <h2 class="title-2"><i class="fa fa-info-circle" style="color: #2563eb; margin-right: 6px;"></i><strong>{{ __('titles.course_description') }}</strong></h2>
                            <div class="course-description-content" style="font-size: 15px; line-height: 1.7; color: #334155;">
                                {!! $selectedCourse->description !!}
                            </div>
                        </div>
                        <div class="box">
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 2px solid #e2e8f0;">
                                <h2 style="margin: 0; font-size: 20px; font-weight: 700; color: #1e293b;" id="course-{{ $selectedCourse->id }}-outline">
                                    <strong> {{ __('titles.all_lectures') }} </strong>
                                </h2>
                                @if(!(\Auth::user() && (\Auth::user()->is_admin || \Auth::user()->role == 1)))
                                    @if($availableCourse)
                                        <div style="background: #e0f2fe; color: #0284c7; padding: 4px 12px; border-radius: 16px; font-weight: 600; font-size: 13.5px; white-space: nowrap; border: 1px solid #bae6fd;">
                                            <i class="fa fa-graduation-cap"></i> Tiến độ học tập: {{ $learnedLectureCount }}/{{ $allLectureCount }}
                                        </div>
                                    @endif
                                @endif
                            </div>
                            @if(!(\Auth::user() && (\Auth::user()->is_admin || \Auth::user()->role == 1)))
                                @if($availableCourse)
                                    @php
                                        $percent = $allLectureCount > 0 ? round(($learnedLectureCount / $allLectureCount) * 100, 2) : 0;
                                    @endphp
                                    <div class="progress" style="height: 20px">
                                        <div class="progress-bar progress-bar-info" role="progressbar"
                                             aria-valuenow="{{ $percent }}"
                                             aria-valuemin="0" aria-valuemax="100"
                                             style="width:{{ $percent }}%">
                                            {{ $percent }}%
                                        </div>
                                    </div>
                                @endif
                            @endif
                            @php
                                $tempLectureCount = 1;
                                $tempQuizCount = 1;
                            @endphp
                            @for($i = 0; $i < $maxWeek; $i++)
                                <h3><strong> {{ __('titles.week') . ' ' . ($i + 1) }} </strong></h3><br>
                                <table class="table table-striped table-bordered add-manage-table" id="table">
                                    <thead>
                                    <tr>
                                        @if(isset($availableCourse))
                                            <th class="text-center col-xs-1">Trạng thái</th>
                                        @endif
                                        <th class="text-center col-xs-2">Thứ tự</th>
                                        <th class="text-center col-xs-5">Tên bài học</th>
                                        @if(isset($availableCourse))
                                            <th class="text-center col-xs-2">Thời lượng</th>
                                            <th class="text-center col-xs-3"></th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($lectureOutline[$i] as $lecture)
                                        <tr id="lecture-{{ $lecture->id }}">
                                            @if($availableCourse)
                                                <td class="col-xs-1 text-center">
                                                    @php
                                                        $proc = \App\Models\Process::where('lecture_id', $lecture->id)->where('user_id', \Auth::id())->first();
                                                    @endphp
                                                    <i class="fa {{ ($proc && $proc->status) ? 'fa-star' : 'fa-star-o' }}">
                                                    </i>
                                                </td>
                                            @endif
                                            <td class="col-xs-2 text-center">
                                                <p>
                                                    @if($lecture->is_lecture  == 1)
                                                        {{ __('titles.lecture') . ' ' . $tempLectureCount }}
                                                        @php
                                                            $tempLectureCount++;
                                                        @endphp
                                                    @elseif($lecture->is_quiz == 1)
                                                        {{ __('titles.quiz') . ' ' . $tempQuizCount }}
                                                        @php
                                                            $tempQuizCount++;
                                                        @endphp
                                                    @endif
                                                </p>
                                            </td>
                                            <td class="col-xs-5">
                                                <p> {{ $lecture->title }} </p>
                                            </td>
                                            @if($availableCourse)
                                                <td class="col-xs-2 text-center">
                                                    <p> {{ $lecture->duration }} </p>
                                                </td>
                                                <td class="col-xs-3 text-center">
                                                    {{-- <a href="{{ url('/courses/' . $id . '/lectures/' . $lecture->id) }}" class="show-modal btn btn-info btn-sm" data-id="">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="#" class="edit-modal btn btn-warning btn-sm" data-id="{{ $lecture->id }}" data-title="{{ $lecture->uploaded_file_title }}" data-description="{{ $lecture->uploaded_file_description }}" data-link="{{ $lecture->uploaded_file_link }}">
                                                        <i class="glyphicon glyphicon-pencil"></i>
                                                    </a>
                                                    <a href="#" class="delete-modal btn btn-danger btn-sm" data-id="{{ $lecture->id }}">
                                                        <i class="glyphicon glyphicon-trash"></i>
                                                    </a> --}}
                                                    @if(\App\Models\QuizResult::where('lecture_id', $lecture->id)->where('user_id', \Auth::id())->first())
                                                        <a href="{{ url('/courses/' . $selectedCourse->id . '/lectures/' . $lecture->id . '/getResult') }}"
                                                           class="show-modal btn btn-info btn-sm" data-id="">
                                                            <i class="fa fa-eye"></i> {{ __('titles.result') }}
                                                        </a>
                                                    @else
                                                        <a href="{{ url('/courses/' . $selectedCourse->id . '/lectures/' . $lecture->id) }}"
                                                           class="show-modal btn btn-info btn-sm" data-id="">
                                                            <i class="fa fa-eye"></i> {{ __('titles.start') }}
                                                        </a>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <hr>
                            @endfor
                        </div>
                        <br>
                        <div id="comments">
                            <div class="inner-box">
                                <h3 id="comment-count-title" style="font-size: 30px; font-weight: bold;">Bình luận ({{ $comments->count() }})</h3>
                                <hr>
                                <ol class="comments-list" id="comment-list">
                                    @foreach($comments as $comment)
                                        @if($comment->user)
                                        <li id="li-comment-{{ $comment->id }}">
                                            <div class="media">
                                                <div class="thumb-left">
                                                    <a href="{{ route('users.show', $comment->user->id) }}">
                                                        <img alt="{{ $comment->user->name }}"
                                                             style="height: 75px; width: 75px"
                                                             src="{{ str_replace(' ', '', str_replace('public/', '', asset($comment->user->avatar ?? 'images/default_avatar/default_avatar.jpg'))) }}"/>
                                                    </a>
                                                </div>
                                                <div class="info-body">
                                                    <div class="media-heading">
                                                        <h4 class="name">{{ $comment->user->name }}</h4> |
                                                        <span class="comment-date">{{ $comment->updated_at }}</span>
                                                        @auth
                                                        <a class="reply-link" data-id="{{ $comment->id }}"
                                                           data-tagged="{{ $comment->user->id }}"><i
                                                                    class="fa fa-reply-all"></i> Trả lời</a>
                                                        @endauth
                                                        @if(Auth::check() && (Auth::id() === $comment->user_id || Auth::user()->is_admin))
                                                            <a class="delete-comment-link" data-id="{{ $comment->id }}" data-type="comment" title="Xóa bình luận này"><i class="fa fa-trash-o"></i> Xóa</a>
                                                        @endif
                                                    </div>
                                                    {!!  $comment->content !!}
                                                </div>
                                            </div>
                                            <br>
                                            @auth
                                            <div class="reply-comment" style="display: none"
                                                 id="reply-comment-{{ $comment->id }}">
                                                <form>
                                                    <div class="row">
                                                        @csrf
                                                        <input type="hidden" name="user_id"
                                                               value="{{ Auth::id() }}">
                                                        <div class="row">
                                                            <div class=" col-md-10 pull-right">
                                                                <div class="form-group">
                                    <textarea id="content-{{ $comment->id }}" class="form-control content-reply" name="content" cols="30"
                                              rows="1"
                                              placeholder="Viết phản hồi...">@if(Auth::check() && $comment->user->name !== \Auth::user()->name) {{ $comment->user->name }} @endif</textarea>
                                                                </div>
                                                                <input type="hidden" name="taggedUser"
                                                                       id="tagged-user-{{ $comment->user->id }}">
                                                                <button type="submit" id="submit"
                                                                        class="btn btn-sm btn-common submit-reply-comment"
                                                                        data-parent="{{ $comment->id }}">
                                                                    Gửi trả lời
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            @endauth
                                            <ul id="reply-comment-{{ $comment->id }}-list">
                                                @foreach($comment->child_comments as $child_comment)
                                                    @if($child_comment->user)
                                                    <li id="li-reply-{{ $child_comment->id }}">
                                                        <div class="media">
                                                            <div class="thumb-left">
                                                                <a href="{{ route('users.show', $child_comment->user->id) }}">
                                                                    <img alt="{{ $child_comment->user->name }}"
                                                                         style="height: 75px; width: 75px"
                                                                         src="{{ str_replace('public/', '', asset($child_comment->user->avatar ?? 'images/default_avatar/default_avatar.jpg')) }}"/>
                                                                </a>
                                                            </div>
                                                            <div class="info-body">
                                                                <div class="media-heading">
                                                                    <h4 class="name">{{ $child_comment->user->name }}</h4>
                                                                    |
                                                                    <span class="comment-date">{{ $child_comment->updated_at }}</span>
                                                                    @auth
                                                                    <a class="reply-link" data-id="{{ $comment->id }}"
                                                                       data-tagged="{{ $child_comment->user->id }}"
                                                                       data-child="{{ $child_comment->id }}"><i
                                                                                class="fa fa-reply-all"></i> Trả lời </a>
                                                                    @endauth
                                                                    @if(Auth::check() && (Auth::id() === $child_comment->user_id || Auth::user()->is_admin))
                                                                        <a class="delete-comment-link" data-id="{{ $child_comment->id }}" data-type="reply" title="Xóa phản hồi này"><i class="fa fa-trash-o"></i> Xóa</a>
                                                                    @endif
                                                                </div>
                                                                <p>{!! $child_comment->content !!} </p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <br>
                                                    @auth
                                                    <div class="reply-comment" style="display: none"
                                                         id="reply-comment-{{ $comment->id }}-from-{{ $child_comment->id }}">
                                                        <form>
                                                            <div class="row">
                                                                @csrf
                                                                <input type="hidden" name="user_id"
                                                                       value="{{ Auth::id() }}">
                                                                <div class="row">
                                                                    <div class=" col-md-12 pull-right">
                                                                        <div class="form-group">
                                    <textarea id="content-{{ $child_comment->id }}" class="form-control content-reply" name="content" cols="30"
                                              rows="1"
                                              placeholder="Viết phản hồi...">@if(Auth::check() && $child_comment->user->name !== \Auth::user()->name) {{ $child_comment->user->name }} @endif</textarea>
                                                                        </div>
                                                                        <input type="hidden" name="taggedUser"
                                                                               id="tagged-user-{{ $child_comment->user->id }}">
                                                                        <button type="submit" id="submit"
                                                                                class="btn btn-sm btn-common submit-reply-child-comment"
                                                                                data-child="{{ $child_comment->id }}"
                                                                                data-parent="{{ $comment->id }}">
                                                                            Gửi trả lời
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    @endauth
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </li>
                                        @endif
                                    @endforeach
                                </ol>

                                <div id="respond">
                                    <h2 class="respond-title">Bình luận về khóa học {{ $selectedCourse->title }}</h2>
                                    @auth
                                    <form>
                                        <div class="row">
                                            <input type="hidden" id="comment-course-id"
                                                   value="{{ $selectedCourse->id }}">
                                            <input type="hidden" id="comment-user-id" value="{{ Auth::id() }}">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                    <textarea id="comment-content" class="form-control" name="content"
                                                              cols="45"
                                                              rows="2" placeholder="Viết bình luận của bạn..."></textarea>
                                                    </div>
                                                    <button type="submit" id="submit-comment" class="btn btn-common">
                                                        Gửi bình luận
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    @else
                                    <p class="text-muted"><a href="{{ route('login') }}" class="btn btn-common btn-sm">Đăng nhập để bình luận</a></p>
                                    @endauth
                                </div>
                            </div>
                        </div>
                        @if($availableCourse && !($availableCourse->rate))
                            <br>
                            <div>
                                <div class="inner-box">
                                    <h3 style="font-size: 30px; font-weight: bold;">Đánh giá khóa học
                                        <i> {{ $selectedCourse->title }}</i></h3>
                                    <hr>
                                    <form method="post"
                                           action="{{ '/courses/' . $selectedCourse->id . '/postAppreciate' }}">
                                        @csrf
                                        <div class="form-group">
                                            <label class="control-label" for="star-rate">Đánh giá của bạn</label>
                                            <br>
                                            <div class="stars" id="star-rate">
                                                <input class="star star-5" id="star-5" type="radio" name="star" value="5"/>
                                                <label class="star star-5" for="star-5"></label>
                                                <input class="star star-4" id="star-4" type="radio" name="star" value="4"/>
                                                <label class="star star-4" for="star-4"></label>
                                                <input class="star star-3" id="star-3" type="radio" name="star" value="3"/>
                                                <label class="star star-3" for="star-3"></label>
                                                <input class="star star-2" id="star-2" type="radio" name="star" value="2"/>
                                                <label class="star star-2" for="star-2"></label>
                                                <input class="star star-1" id="star-1" type="radio" name="star" value="1"/>
                                                <label class="star star-1" for="star-1"></label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="textarea">Nhận xét của bạn</label>
                                            <textarea class="form-control" name="appreciate_content" rows="3"
                                                      placeholder="Viết nhận xét của bạn về khóa học này..."></textarea>
                                        </div>
                                        <input class="btn btn-common" type="submit" value="Gửi đánh giá">
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="cd-sidebar-sticky">
                        <!-- Course Purchase Card -->
                        <div class="cd-purchase-card">
                            <div class="cd-card-badge-header">
                                <i class="fa fa-graduation-cap"></i> Khóa học trực tuyến
                            </div>

                            @php
                                $hasDiscount = isset($selectedCourse->promotion_price) && $selectedCourse->promotion_price > 0 && $selectedCourse->promotion_price < $selectedCourse->origin_price;
                                $discountPercent = $hasDiscount && $selectedCourse->origin_price > 0 ? round((($selectedCourse->origin_price - $selectedCourse->promotion_price) / $selectedCourse->origin_price) * 100) : 0;
                            @endphp

                            <div class="cd-price-section">
                                @if($hasDiscount)
                                    <span class="cd-current-price">{{ format_price($selectedCourse->promotion_price) }}</span>
                                    <span class="cd-original-price">{{ format_price($selectedCourse->origin_price) }}</span>
                                    <span class="cd-discount-badge">-{{ $discountPercent }}%</span>
                                @elseif($selectedCourse->origin_price > 0)
                                    <span class="cd-current-price">{{ format_price($selectedCourse->origin_price) }}</span>
                                @else
                                    <span class="cd-current-price" style="color: #10b981;">Miễn phí</span>
                                @endif
                            </div>

                            @if($availableCourse)
                                <div class="cd-enrolled-banner">
                                    <i class="fa fa-check-circle"></i>
                                    <div>
                                        <div>Bạn đã sở hữu khóa học này</div>
                                        <small style="font-weight: 400; color: #047857;">Truy cập bài giảng không giới hạn</small>
                                    </div>
                                </div>
                                @php
                                    $firstLecture = $selectedCourse->lectures->first();
                                    $learningUrl = $firstLecture 
                                        ? route('courses.lectures.show', ['id' => $selectedCourse->id, 'lectureId' => $firstLecture->id]) 
                                        : '#course-' . $selectedCourse->id . '-outline';
                                @endphp
                                <a href="{{ $learningUrl }}"
                                   class="cd-btn-start-learning">
                                    <i class="fa fa-play-circle"></i> Vào học ngay
                                </a>
                            @else
                                <button type="button" class="cd-btn-buy-now" id="buy-now" data-id="{{ $selectedCourse->id }}">
                                    <i class="fa fa-credit-card"></i> <span>{{ __('titles.buy_now') }}</span>
                                </button>
                                <button type="button" class="cd-btn-add-cart" id="add-to-cart" data-id="{{ $selectedCourse->id }}">
                                    <i class="fa fa-cart-plus"></i> <span>{{ __('titles.add_to_cart') }}</span>
                                </button>
                                <div class="cd-guarantee-note">
                                    <i class="fa fa-shield"></i> Cam kết chất lượng - Hỗ trợ tận tâm
                                </div>
                            @endif

                            <div class="cd-specs-header">Khóa học này bao gồm:</div>
                            <ul class="cd-specs-list">
                                <li>
                                    <span class="spec-label"><i class="fa fa-clock-o"></i> Thời lượng</span>
                                    <span class="spec-value">{{ $selectedCourse->duration }} phút</span>
                                </li>
                                <li>
                                    <span class="spec-label"><i class="fa fa-play-circle-o"></i> Giáo trình</span>
                                    <span class="spec-value">{{ $selectedCourse->lectures->count() }} bài học</span>
                                </li>
                                <li>
                                    <span class="spec-label"><i class="fa fa-signal"></i> Cấp độ</span>
                                    <span class="spec-value">{{ $selectedCourse->level ?? 'Mọi cấp độ' }}</span>
                                </li>
                                <li>
                                    <span class="spec-label"><i class="fa fa-users"></i> Học viên đã mua</span>
                                    <span class="spec-value">{{ number_format($selectedCourse->seller) }}</span>
                                </li>
                                <li>
                                    <span class="spec-label"><i class="fa fa-certificate"></i> Chứng chỉ</span>
                                    <span class="spec-value">Hoàn thành</span>
                                </li>
                            </ul>

                            <div class="cd-share-section">
                                <span class="cd-share-label"><i class="fa fa-share-alt"></i> Chia sẻ khóa học:</span>
                                <div class="cd-social-links">
                                    <a class="cd-social-btn fb" target="_blank" title="Facebook"
                                       href="https://www.facebook.com/sharer/sharer.php?u={{ Request::url() }}">
                                        <i class="fa fa-facebook"></i>
                                    </a>
                                    <a class="cd-social-btn tw" target="_blank" title="Twitter"
                                       href="https://twitter.com/intent/tweet?text=Khóa học hay tại RoadToSchool&amp;url={{ Request::url() }}">
                                        <i class="fa fa-twitter"></i>
                                    </a>
                                    <a class="cd-social-btn wa" target="_blank" title="WhatsApp"
                                       href="https://wa.me/?text={{ Request::url() }}">
                                        <i class="fa fa-whatsapp"></i>
                                    </a>
                                    <a class="cd-social-btn li" target="_blank" title="LinkedIn"
                                       href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ Request::url() }}&amp;title={{ urlencode($selectedCourse->title) }}">
                                        <i class="fa fa-linkedin"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Instructor Card -->
                        <div class="cd-instructor-card">
                            <div class="cd-card-badge-header" style="color: #0f172a; margin-bottom: 12px;">
                                <i class="fa fa-id-card-o" style="color: #2563eb;"></i> Thông tin giảng viên
                            </div>
                            @php
                                $instructorAvatar = $selectedCourse->user->avatar 
                                    ? (strpos($selectedCourse->user->avatar, 'http') === 0 ? $selectedCourse->user->avatar : asset(str_replace('public/', '', $selectedCourse->user->avatar))) 
                                    : asset('images/default_avatar/teacher.jpg');
                            @endphp
                            <div class="cd-instructor-header">
                                <img class="cd-instructor-avatar" src="{{ $instructorAvatar }}" alt="{{ $selectedCourse->user->name }}" onerror="this.src='{{ asset('images/default_avatar/teacher.jpg') }}'">
                                <div>
                                    <h4 class="cd-instructor-name">
                                        <a href="{{ route('instructor_info', $selectedCourse->user->id) }}">{{ $selectedCourse->user->name }}</a>
                                    </h4>
                                    <p class="cd-instructor-role"><i class="fa fa-check-circle" style="color: #10b981;"></i> Giảng viên uy tín</p>
                                </div>
                            </div>
                            <div class="cd-instructor-contact">
                                @if($selectedCourse->user->email)
                                    <p><i class="fa fa-envelope-o"></i> <span>{{ $selectedCourse->user->email }}</span></p>
                                @endif
                                @if($selectedCourse->user->phone)
                                    <p><i class="fa fa-phone"></i> <span>{{ $selectedCourse->user->phone }}</span></p>
                                @endif
                            </div>
                            <a class="cd-btn-profile" href="{{ route('instructor_info', $selectedCourse->user->id) }}">
                                <i class="fa fa-user"></i> Xem hồ sơ giảng viên & các khóa học khác
                            </a>
                        </div>

                        <!-- Trust Features Card -->
                        <div class="cd-trust-card">
                            <h4 class="cd-trust-title"><i class="fa fa-shield" style="color: #2563eb;"></i> Cam kết tại Road To School</h4>
                            <div class="cd-trust-item">
                                <div class="cd-trust-icon"><i class="fa fa-lock"></i></div>
                                <div class="cd-trust-text">
                                    <h5>Bảo vệ quyền lợi tối đa</h5>
                                    <p>Thanh toán an toàn, bảo mật thông tin học viên 100%.</p>
                                </div>
                            </div>
                            <div class="cd-trust-item">
                                <div class="cd-trust-icon"><i class="fa fa-repeat"></i></div>
                                <div class="cd-trust-text">
                                    <h5>Học trọn đời - Không phát sinh chi phí</h5>
                                    <p>Thanh toán 1 lần, cập nhật giáo trình mới hoàn toàn miễn phí.</p>
                                </div>
                            </div>
                            <div class="cd-trust-item">
                                <div class="cd-trust-icon"><i class="fa fa-check-square-o"></i></div>
                                <div class="cd-trust-text">
                                    <h5>Kiểm duyệt nội dung chuẩn hóa</h5>
                                    <p>Bài giảng được chọn lọc kỹ bởi chuyên gia đầu ngành.</p>
                                </div>
                            </div>
                            <div class="cd-trust-item">
                                <div class="cd-trust-icon"><i class="fa fa-headphones"></i></div>
                                <div class="cd-trust-text">
                                    <h5>Hỗ trợ học tập 24/7</h5>
                                    <p>Giảng viên & đội ngũ trợ giảng hỗ trợ giải đáp trực tiếp.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="featured-lis mb30">
        <div class="container">
            <div class="row">
                <div class="col-md-12 wow fadeIn" data-wow-delay="0.5s">
                    <h3 class="section-title"> {{ __('titles.related_courses') }}</h3>
                    <div id="new-products" class="owl-carousel">
                        @foreach($mostRelatedCourse as $course)
                            <div class="item">
                                <div class="product-item">
                                    <div class="carousel-thumb">
                                        <img src="{{ str_replace('public/', '', asset($course->course_avatar)) }}"
                                             style="height:142px"
                                             alt="">
                                        <div class="overlay">
                                            <a href="{{ route('courses.show', $course->id) }}"><i
                                                        class="fa fa-info-circle"></i></a>
                                        </div>
                                    </div>
                                    <a href="ads-details.html" class="item-name"> {{ $course->title }} </a>
                                    <span class="price">@if(isset($course->promotion_price) && $course->promotion_price < $course->origin_price)<b
                                                style="color:red">{{ format_price($course->promotion_price) }} </b><strike> {{ format_price($course->origin_price) }}</strike>@else {{ format_price($course->origin_price) }}
                                        @endif</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($relatedCourseCount > 5)
                        <a class="btn btn-search btn-block"
                           href="{{ route('courses.index', ['sub_category_id' => $selectedCourse->category_id]) }}"
                           style="width: max-content;margin: 0 auto" ;><strong
                                    style="color: #3498db">{{ __('titles.see_more') }}</strong></a>
                    @endif
                </div>
            </div>
        </div>
    </section>
    </div>
    </div>
    </div>
    </div>
    </div>
@endsection

@include('user.courses.partials.javascript_common')
@section('inline_scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            // Thay giá trị PUSHER_APP_KEY vào chỗ xxx này nhé
            var pusher = new Pusher('f2b354d9cdae3999c31d', {
                encrypted: true,
                cluster: "ap1"
            });

            // // Like course
            // $('#like-course').on('click', function (event) {
            //     event.preventDefault();
            //     var childElement = $(this).children();
            //     $.ajaxSetup({
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         }
            //     });
            //     $.ajax({
            //         url: '/courses/' + $('#comment-course-id').val() + '/like/' + $('#comment-user-id').val() + '/changeStatus/' + $(this).data('like'),
            //         type: 'post',
            //         success: function () {
            //         }
            //     });
            //     // Unlike
            //     if (childElement.hasClass('fa-heart')) {
            //         $('#like-course').html('');
            //         $('#like-course').append('<i class="fa fa-heart-o"></i>&ensp; Like');
            //         $('#like-course').data('like', 0);
            //     } else {
            //         // Like
            //         $('#like-course').html('');
            //         $('#like-course').append('<i class="fa fa-heart"></i>&ensp; Unlike');
            //         $('#like-course').data('like', 1);
            //     }
            // });

            // Subscribe to the channel we specified in our Laravel Event
            // var channel = pusher.subscribe('like-count-update');
            //
            // // Bind a function to a Event (the full Laravel class)
            // channel.bind('App\\Events\\GetLikeCountFromPusherEvent', function (data) {
            //     $('#like-count').text(data.likeCount);
            // });
            // End like course
            $('.reply-link').css({'cursor': 'pointer'});

            replyJavascript();

            // Pusher area
            var channel = pusher.subscribe('comment');
            channel.bind('App\\Events\\GetCommentFromPusherEvent', function (data) {
                var commentedUser = (typeof data.user === 'string') ? jQuery.parseJSON(data.user) : data.user;
                var createdComment = (typeof data.createdComment === 'string') ? jQuery.parseJSON(data.createdComment) : data.createdComment;
                var userAvatar = commentedUser.avatar || '';
                userAvatar = userAvatar.replace("images/", "http://127.0.0.1:8000/images/");
                commentedUser.avatar = userAvatar;
                appendNewComment(createdComment, commentedUser, createdComment.updated_at || 'Vừa xong');
            });

            var replyChannel = pusher.subscribe('replyComment');
            replyChannel.bind('App\\Events\\GetReplyCommentFromPusherEvent', function (data) {
                var commentedUser = (typeof data.user === 'string') ? jQuery.parseJSON(data.user) : data.user;
                var createdComment = (typeof data.createdComment === 'string') ? jQuery.parseJSON(data.createdComment) : data.createdComment;
                var userAvatar = commentedUser.avatar || '';
                userAvatar = userAvatar.replace("images/", "http://127.0.0.1:8000/images/");
                commentedUser.avatar = userAvatar;
                appendNewReply(createdComment, commentedUser, createdComment.updated_at || 'Vừa xong', data.parentCommentId);
            });

            $('#add-to-cart').on('click', function (event) {
                event.preventDefault();
                var courseId = $(this).data('id');
                addCourseToCart(courseId, '{{ __('messages.add_to_cart_success') }}', '{{ __('messages.in_cart_already_failed') }}', '{{ __('messages.my_course_already_failed') }}', '{{ __('messages.my_bill_already_failed') }}', null, 'add-to-cart');
            });

            $('#buy-now').on('click', function (event) {
                event.preventDefault();
                var courseId = $(this).data('id');
                addCourseToCart(courseId, '{{ __('messages.add_to_cart_success_redirecting') }}', '{{ __('messages.in_cart_already_failed') }}', '{{ __('messages.my_course_already_failed') }}', '{{ __('messages.my_bill_already_failed') }}', $(this).attr('id'), 'add-to-cart');
            });

            // GỬI BÌNH LUẬN CHÍNH
            $('#comments').off('click', '#submit-comment').on('click', '#submit-comment', function (event) {
                event.preventDefault();
                var $btn = $(this);
                if ($btn.prop('disabled')) return;

                var contentVal = $('#comment-content').val().trim();
                if (!contentVal) {
                    alert('Vui lòng nhập nội dung bình luận.');
                    return;
                }
                var courseId = $('#comment-course-id').val();
                var userId = $('#comment-user-id').val();

                $btn.prop('disabled', true).text('Đang gửi...');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/courses/' + courseId + '/pusher/postComment',
                    type: 'post',
                    data: {
                        content: contentVal,
                        userId: userId,
                    },
                    success: function (res) {
                        $('#comment-content').val('');
                        var resData = (typeof res === 'string') ? JSON.parse(res) : res;
                        if (resData && resData.comment && resData.user) {
                            appendNewComment(resData.comment, resData.user, resData.created_at || 'Vừa xong');
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi gửi bình luận, vui lòng thử lại.');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Gửi bình luận');
                    }
                });
            });
        });

        var isAdminUser = {{ (Auth::check() && Auth::user()->is_admin) ? 'true' : 'false' }};

        // --- XÓA BÌNH LUẬN / PHẢN HỒI ---
        $(document).on('click', '.delete-comment-link', function (e) {
            e.preventDefault();
            var $link = $(this);
            var id = $link.data('id');
            var type = $link.data('type');
            var message = type === 'comment'
                ? 'Bạn có chắc chắn muốn xóa bình luận này? Các phản hồi của bình luận cũng sẽ bị xóa.'
                : 'Bạn có chắc chắn muốn xóa phản hồi này?';
            if (!confirm(message)) { return; }

            $link.css('pointer-events', 'none').css('opacity', 0.5);
            $.ajax({
                url: '/comments/' + id,
                type: 'DELETE',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                success: function (res) {
                    var $li = $(type === 'comment' ? '#li-comment-' + id : '#li-reply-' + id);
                    $li.next('br').remove();
                    $li.fadeOut(200, function () {
                        $(this).remove();
                        $('#comment-count-title').text('Bình luận (' + $('#comment-list > li').length + ')');
                    });
                },
                error: function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Không xóa được bình luận, vui lòng thử lại.';
                    alert(msg);
                    $link.css('pointer-events', '').css('opacity', 1);
                }
            });
        });

        // --- HÀM THÊM BÌNH LUẬN TRỰC TIẾP VÀO GIAO DIỆN KHÔNG CẦN F5 ---
        function appendNewComment(comment, user, createdAt) {
            if ($('#li-comment-' + comment.id).length > 0) return;
            var userAvatar = user.avatar || '/images/default_avatar/default_avatar.jpg';
            if (!userAvatar.startsWith('http') && !userAvatar.startsWith('/')) {
                userAvatar = '/' + userAvatar;
            }
            var currentUserId = '{{ \Auth::id() }}';
            var newCommentHtml =
                '<li id="li-comment-' + comment.id + '">' +
                    '<div class="media">' +
                        '<div class="thumb-left">' +
                            '<a href="/users/' + user.id + '">' +
                                '<img alt="' + user.name + '" style="height: 75px; width: 75px; border-radius: 50%; object-fit: cover;" src="' + userAvatar + '"/>' +
                            '</a>' +
                        '</div>' +
                        '<div class="info-body">' +
                            '<div class="media-heading">' +
                                '<h4 class="name">' + user.name + '</h4> | ' +
                                '<span class="comment-date">' + createdAt + '</span> ' +
                                '<a class="reply-link" data-id="' + comment.id + '" data-tagged="' + user.id + '" style="cursor: pointer;"><i class="fa fa-reply-all"></i> Trả lời</a>' +
                                ((String(user.id) === currentUserId || isAdminUser) ? ' <a class="delete-comment-link" data-id="' + comment.id + '" data-type="comment" title="Xóa bình luận này"><i class="fa fa-trash-o"></i> Xóa</a>' : '') +
                            '</div>' +
                            '<p>' + comment.content + '</p>' +
                        '</div>' +
                    '</div>' +
                    '<br>' +
                    '<div class="reply-comment" style="display: none" id="reply-comment-' + comment.id + '">' +
                        '<form>' +
                            '<div class="row">' +
                                '<input type="hidden" name="user_id" value="' + currentUserId + '">' +
                                '<div class="row">' +
                                    '<div class="col-md-10 pull-right">' +
                                        '<div class="form-group">' +
                                            '<textarea id="content-' + comment.id + '" class="form-control content-reply" name="content" cols="30" rows="1" placeholder="Viết phản hồi..."></textarea>' +
                                        '</div>' +
                                        '<input type="hidden" name="taggedUser" id="tagged-user-' + user.id + '">' +
                                        '<button type="submit" class="btn btn-sm btn-common submit-reply-comment" data-parent="' + comment.id + '">Gửi trả lời</button>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</form>' +
                    '</div>' +
                    '<ul id="reply-comment-' + comment.id + '-list"></ul>' +
                '</li>';

            $('#comment-list').append(newCommentHtml);
            var count = $('#comment-list > li').length;
            $('#comment-count-title').text('Bình luận (' + count + ')');
            replyJavascript();
        }

        function appendNewReply(comment, user, createdAt, parentCommentId) {
            if ($('#li-reply-' + comment.id).length > 0) return;
            var userAvatar = user.avatar || '/images/default_avatar/default_avatar.jpg';
            if (!userAvatar.startsWith('http') && !userAvatar.startsWith('/')) {
                userAvatar = '/' + userAvatar;
            }
            var currentUserId = '{{ \Auth::id() }}';
            var newReplyHtml =
                '<li id="li-reply-' + comment.id + '">' +
                    '<div class="media">' +
                        '<div class="thumb-left">' +
                            '<a href="/users/' + user.id + '">' +
                                '<img alt="' + user.name + '" style="height: 75px; width: 75px; border-radius: 50%; object-fit: cover;" src="' + userAvatar + '"/>' +
                            '</a>' +
                        '</div>' +
                        '<div class="info-body">' +
                            '<div class="media-heading">' +
                                '<h4 class="name">' + user.name + '</h4> | ' +
                                '<span class="comment-date">' + createdAt + '</span> ' +
                                '<a class="reply-link" data-id="' + parentCommentId + '" data-child="' + comment.id + '" data-tagged="' + user.id + '" style="cursor: pointer;"><i class="fa fa-reply-all"></i> Trả lời</a>' +
                                ((String(user.id) === currentUserId || isAdminUser) ? ' <a class="delete-comment-link" data-id="' + comment.id + '" data-type="reply" title="Xóa phản hồi này"><i class="fa fa-trash-o"></i> Xóa</a>' : '') +
                            '</div>' +
                            '<p>' + comment.content + '</p>' +
                        '</div>' +
                    '</div>' +
                    '<br>' +
                    '<div class="reply-comment" style="display: none" id="reply-comment-' + parentCommentId + '-from-' + comment.id + '">' +
                        '<form>' +
                            '<div class="row">' +
                                '<input type="hidden" name="user_id" value="' + currentUserId + '">' +
                                '<div class="row">' +
                                    '<div class="col-md-12 pull-right">' +
                                        '<div class="form-group">' +
                                            '<textarea id="content-' + comment.id + '" class="form-control content-reply" name="content" cols="30" rows="1" placeholder="Viết phản hồi..."></textarea>' +
                                        '</div>' +
                                        '<input type="hidden" name="taggedUser" id="tagged-user-' + user.id + '">' +
                                        '<button type="submit" class="btn btn-sm btn-common submit-reply-child-comment" data-child="' + comment.id + '" data-parent="' + parentCommentId + '">Gửi trả lời</button>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</form>' +
                    '</div>' +
                '</li>';

            var targetList = $('#reply-comment-' + parentCommentId + '-list');
            if (targetList.length === 0) {
                $('#li-comment-' + parentCommentId).append('<ul id="reply-comment-' + parentCommentId + '-list"></ul>');
                targetList = $('#reply-comment-' + parentCommentId + '-list');
            }
            targetList.append(newReplyHtml);
            replyJavascript();
        }

        function replyJavascript() {
            $('.content-reply').each(function() {
                var currentString = $(this).val();
                if(currentString && currentString.trim() != '' && !currentString.endsWith(': ')) {
                    currentString = currentString.trim() + ': ';
                    $(this).val(currentString);
                }
            });

            $('#comments').off('click', '.reply-link').on('click', '.reply-link', function (event) {
                event.preventDefault();
                if ($(this).data('child')) {
                    if ($('#reply-comment-' + $(this).data('id') + '-from-' + $(this).data('child')).css('display') == 'none') {
                        $('#reply-comment-' + $(this).data('id') + '-from-' + $(this).data('child')).show();
                        $('#content-' + $(this).data('child')).focus();
                        $('#content-' + $(this).data('child')).putCursorAtEnd();
                        $('#tagged-user-' + $(this).data('tagged')).val($(this).data('tagged'));
                    } else {
                        $('#reply-comment-' + $(this).data('id') + '-from-' + $(this).data('child')).hide();
                    }
                } else {
                    if ($('#reply-comment-' + $(this).data('id')).css('display') == 'none') {
                        $('#reply-comment-' + $(this).data('id')).show();
                        $('#content-' + $(this).data('id')).focus();
                        $('#content-' + $(this).data('id')).putCursorAtEnd();
                        $('#tagged-user-' + $(this).data('id')).val($(this).data('tagged') + ' ');
                    } else {
                        $('#reply-comment-' + $(this).data('id')).hide();
                    }
                }
            });

            // GỬI PHẢN HỒI (REPLY)
            $('#comments').off('click', '.submit-reply-comment, .submit-reply-child-comment').on('click', '.submit-reply-comment, .submit-reply-child-comment', function (event) {
                event.preventDefault();
                var $btn = $(this);
                if ($btn.prop('disabled')) return;

                var contentComment = '';
                var firstChildComment = true;
                var prevCommentId = 0;
                var parentId = $btn.data('parent');

                if ($btn.data('id') != undefined) {
                    contentComment = $('#content-' + $btn.data('id')).val();
                    firstChildComment = false;
                    prevCommentId = $btn.data('id');
                } else if ($btn.data('child') != undefined) {
                    contentComment = $('#content-' + $btn.data('child')).val();
                    firstChildComment = false;
                    prevCommentId = $btn.data('child');
                } else {
                    contentComment = $('#content-' + parentId).val();
                }

                if (!contentComment || !contentComment.trim()) {
                    alert('Vui lòng nhập nội dung phản hồi.');
                    return;
                }

                $btn.prop('disabled', true).text('Đang gửi...');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/courses/' + $('#comment-course-id').val() + '/pusher/replyComment/' + parentId,
                    type: 'post',
                    data: {
                        content: contentComment,
                        userId: $('#comment-user-id').val(),
                        firstChildComment: firstChildComment,
                        prevCommentId: prevCommentId,
                    },
                    success: function (data) {
                        var responseData = (typeof data === 'string') ? JSON.parse(data) : data;
                        if (prevCommentId) {
                            $('#content-' + prevCommentId).val('');
                            $('#reply-comment-' + parentId + '-from-' + prevCommentId).hide();
                        } else {
                            $('#content-' + parentId).val('');
                            $('#reply-comment-' + parentId).hide();
                        }
                        if (responseData && responseData.comment && responseData.user) {
                            appendNewReply(responseData.comment, responseData.user, responseData.created_at || 'Vừa xong', parentId);
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi gửi phản hồi, vui lòng thử lại.');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Gửi trả lời');
                    }
                });
            });
        }

        // https://css-tricks.com/snippets/jquery/move-cursor-to-end-of-textarea-or-input/
        jQuery.fn.putCursorAtEnd = function () {

            return this.each(function () {

                // Cache references
                var $el = $(this),
                    el = this;

                // Only focus if input isn't already
                if (!$el.is(":focus")) {
                    $el.focus();
                }

                // If this function exists... (IE 9+)
                if (el.setSelectionRange) {

                    // Double the length because Opera is inconsistent about whether a carriage return is one character or two.
                    var len = $el.val().length * 2;

                    // Timeout seems to be required for Blink
                    setTimeout(function () {
                        el.setSelectionRange(len, len);
                    }, 1);

                } else {

                    // As a fallback, replace the contents with itself
                    // Doesn't work in Chrome, but Chrome supports setSelectionRange
                    $el.val($el.val());

                }

                // Scroll to the bottom, in case we're in a tall textarea
                // (Necessary for Firefox and Chrome)
                this.scrollTop = 999999;

            });
        };
    </script>
@endsection

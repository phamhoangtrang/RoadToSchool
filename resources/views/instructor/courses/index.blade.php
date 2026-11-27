@extends('admin.layouts.default')

@section('title')
    {{ __('titles.all_courses') }}
@endsection

@section('inline_styles')
    <link rel="stylesheet"
          href="{{ asset('assets/admin/vendor/datatables/media/css/dataTables.bootstrap4.min.css') }}"/>
    <style>
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
        .badge-cat-tag {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 12px;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
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
        #dt-opt th {
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
        #dt-opt td {
            padding: 14px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="page-header m-b-20">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <h2 class="header-title" style="font-weight: 700; color: #1e293b;">Khóa học của tôi</h2>
                        <div class="header-sub-title">
                            <nav class="breadcrumb breadcrumb-dash" style="background: transparent; padding: 0;">
                                <a href="{{ route('instructor.dashboard') }}" class="breadcrumb-item" style="color: #64748b;">
                                    <i class="ti-home p-r-5"></i>{{ __('titles.r2s_dashboard') }}
                                </a>
                                <span class="breadcrumb-item active" style="color: #334155; font-weight: 500;">
                                    Khóa học của tôi
                                </span>
                            </nav>
                        </div>
                    </div>
                    <div class="col-md-5 text-right">
                        <a href="{{ route('instructor.courses.create') }}" class="btn btn-primary" style="border-radius: 8px; font-weight: 600; padding: 10px 18px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);">
                            <i class="ti-plus m-r-5"></i> Tạo khóa học mới
                        </a>
                    </div>
                </div>
            </div>

            @include('flash::message')

            <!-- KPI Cards -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card inst-kpi-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="kpi-icon-box m-r-20" style="background-color: #eff6ff; color: #3b82f6;">
                                <i class="ti-book"></i>
                            </div>
                            <div>
                                <div style="font-size: 13px; color: #64748b; font-weight: 500; text-transform: uppercase;">Tổng khóa học</div>
                                <div style="font-size: 26px; font-weight: 700; color: #1e293b; line-height: 1.2;">{{ $instructorCourseList->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card inst-kpi-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="kpi-icon-box m-r-20" style="background-color: #ecfdf5; color: #10b981;">
                                <i class="ti-check-box"></i>
                            </div>
                            <div>
                                <div style="font-size: 13px; color: #64748b; font-weight: 500; text-transform: uppercase;">Đã xuất bản / Duyệt</div>
                                <div style="font-size: 26px; font-weight: 700; color: #059669; line-height: 1.2;">{{ $instructorCourseList->where('is_accepted', 1)->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card inst-kpi-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="kpi-icon-box m-r-20" style="background-color: #fffbeb; color: #f59e0b;">
                                <i class="ti-time"></i>
                            </div>
                            <div>
                                <div style="font-size: 13px; color: #64748b; font-weight: 500; text-transform: uppercase;">Đang chờ duyệt</div>
                                <div style="font-size: 26px; font-weight: 700; color: #d97706; line-height: 1.2;">{{ $instructorCourseList->where('is_accepted', 0)->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card inst-table-card">
                <div class="card-header border-bottom p-3 d-flex align-items-center justify-content-between" style="background: #fff;">
                    <h5 class="m-b-0" style="font-weight: 600; color: #334155;">Danh sách khóa học do bạn giảng dạy</h5>
                    <span class="badge badge-light" style="font-size: 13px; padding: 6px 12px; border-radius: 8px;">
                        Tổng {{ $instructorCourseList->count() }} khóa học
                    </span>
                </div>
                <div class="card-body p-3">
                    <div class="table-overflow">
                        <table id="dt-opt" class="table table-hover table-xl" style="width: 100%;">
                            <thead>
                            <tr>
                                <th style="width: 42%;">Khóa học</th>
                                <th style="width: 20%;">Danh mục</th>
                                <th style="width: 18%; text-align: center;">Trạng thái</th>
                                <th style="width: 20%; text-align: center;">Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($instructorCourseList as $course)
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
                                <tr id="course{{ $course->id }}">
                                    <td>
                                        <div class="d-flex align-items-center" style="gap: 16px;">
                                            <img src="{{ $avatar }}" alt="{{ $course->title }}" class="table-course-thumb"
                                                 style="margin-right: 16px;"
                                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=100&auto=format&fit=crop&q=80';"/>
                                            <div style="min-width: 0; padding-left: 2px;">
                                                <a href="{{ route('instructor.courses.show', $course->id) }}" class="course-title-text" title="{{ $course->title }}">
                                                    {{ $course->title }}
                                                </a>
                                                <small class="text-muted d-block" style="font-size: 11px;">Mã: #{{ $course->id }} &bull; Cập nhật: {{ $course->updated_at ? $course->updated_at->format('d/m/Y') : 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-cat-tag">
                                            <i class="ti-folder text-muted"></i>
                                            {{ $course->category ? ($course->category->vi_title ?: $course->category->title) : 'Chung' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($course->is_accepted)
                                            <span class="status-pill accepted">
                                                <i class="ti-check"></i> Đã duyệt
                                            </span>
                                        @else
                                            <span class="status-pill pending">
                                                <i class="ti-time"></i> Chờ duyệt
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center" style="white-space: nowrap;">
                                        <a href="{{ route('instructor.courses.show', $course->id) }}" class="action-btn-circle view" title="Quản lý chi tiết khóa học">
                                            <i class="ti-eye"></i>
                                        </a>
                                        <a href="{{ route('instructor.courses.lectures.create', $course->id) }}" class="action-btn-circle add" title="Thêm bài giảng mới">
                                            <i class="ti-plus"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('admin.layouts.delete_modal')

@section('inline_scripts')
    <script src="{{ asset('assets/admin/vendor/datatables/media/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/datatables/media/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/tables/data-table.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#delete-modal').on('show.bs.modal', function (e) {
                var url = $(e.relatedTarget).data('url');
                $('#form-delete').attr('action', url);
            });

            $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
        });
    </script>
@endsection

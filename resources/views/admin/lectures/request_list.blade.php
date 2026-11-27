@extends('admin.layouts.default')

@section('title')
    {{ __('titles.lecture_request_list') }}
@endsection

@section('inline_styles')
    <link rel="stylesheet"
          href="{{ asset('assets/admin/vendor/datatables/media/css/dataTables.bootstrap4.min.css') }}"/>
    <style>
        .request-kpi-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            transition: all 0.25s ease;
            background: #fff;
            margin-bottom: 24px;
        }
        .request-kpi-card:hover {
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
        .requests-table-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            background: #fff;
            overflow: hidden;
        }
        .lecture-title-text {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            line-height: 1.4;
            transition: color 0.2s;
        }
        .lecture-title-text:hover {
            color: #2563eb;
            text-decoration: none;
        }
        .type-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }
        .type-badge.lecture {
            background-color: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }
        .type-badge.quiz {
            background-color: #fdf4ff;
            color: #9333ea;
            border: 1px solid #f0abfc;
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
        .action-btn-circle.approve {
            background-color: #ecfdf5;
            color: #059669;
        }
        .action-btn-circle.approve:hover {
            background-color: #10b981;
            color: #fff;
            transform: scale(1.08);
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
        .action-btn-circle.delete {
            background-color: #fef2f2;
            color: #dc2626;
        }
        .action-btn-circle.delete:hover {
            background-color: #ef4444;
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
    <!-- Content Wrapper START -->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="page-header m-b-20">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="header-title" style="font-weight: 700; color: #1e293b;">{{ __('titles.lecture_request_list') }}</h2>
                        <div class="header-sub-title">
                            <nav class="breadcrumb breadcrumb-dash" style="background: transparent; padding: 0;">
                                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item" style="color: #64748b;">
                                    <i class="ti-home p-r-5"></i>{{ __('titles.r2s_dashboard') }}
                                </a>
                                <span class="breadcrumb-item active" style="color: #334155; font-weight: 500;">
                                    {{ __('titles.lecture_request_list') }}
                                </span>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            @php $pendingCoursesCount = \App\Models\Course::where('is_accepted', 0)->count(); @endphp
            @if($pendingCoursesCount > 0)
                <div class="alert alert-warning d-flex align-items-center justify-content-between p-3 m-b-20 flex-wrap" style="border-radius: 12px; background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b; gap: 12px;">
                    <div class="d-flex align-items-center">
                        <div style="width: 38px; height: 38px; border-radius: 8px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 14px; flex-shrink: 0;">
                            <i class="ti-info-alt"></i>
                        </div>
                        <div>
                            <strong style="color: #92400e; font-size: 14px;">Bạn có {{ $pendingCoursesCount }} Khóa học mới đang chờ phê duyệt!</strong>
                            <div style="font-size: 13px; color: #78350f; margin-top: 2px;">Trang hiện tại chỉ dùng để duyệt từng Bài giảng / Quiz con. Để duyệt Khóa học mới, bạn hãy chuyển sang mục Tất cả khóa học.</div>
                        </div>
                    </div>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-warning btn-sm" style="font-weight: 700; white-space: nowrap; border-radius: 8px; padding: 8px 16px;">
                        <i class="ti-arrow-right m-r-5"></i> Duyệt Khóa học ngay ({{ $pendingCoursesCount }})
                    </a>
                </div>
            @endif

            <!-- KPI Cards -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card request-kpi-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="kpi-icon-box m-r-20" style="background-color: #fffbeb; color: #f59e0b;">
                                <i class="ti-layers-alt"></i>
                            </div>
                            <div>
                                <div style="font-size: 13px; color: #64748b; font-weight: 500; text-transform: uppercase;">Yêu cầu chờ duyệt</div>
                                <div style="font-size: 26px; font-weight: 700; color: #d97706; line-height: 1.2;">{{ $requestLectureList->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card request-kpi-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="kpi-icon-box m-r-20" style="background-color: #eff6ff; color: #3b82f6;">
                                <i class="ti-video-clapper"></i>
                            </div>
                            <div>
                                <div style="font-size: 13px; color: #64748b; font-weight: 500; text-transform: uppercase;">Bài giảng video</div>
                                <div style="font-size: 26px; font-weight: 700; color: #2563eb; line-height: 1.2;">{{ $requestLectureList->where('is_lecture', 1)->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card request-kpi-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="kpi-icon-box m-r-20" style="background-color: #fdf4ff; color: #a855f7;">
                                <i class="ti-help-alt"></i>
                            </div>
                            <div>
                                <div style="font-size: 13px; color: #64748b; font-weight: 500; text-transform: uppercase;">Bài trắc nghiệm</div>
                                <div style="font-size: 26px; font-weight: 700; color: #9333ea; line-height: 1.2;">{{ $requestLectureList->where('is_quiz', 1)->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card requests-table-card">
                <div class="card-header border-bottom p-3 d-flex align-items-center justify-content-between" style="background: #fff;">
                    <h5 class="m-b-0" style="font-weight: 600; color: #334155;">Danh sách yêu cầu phê duyệt nội dung bài học</h5>
                    <span class="badge badge-warning" style="font-size: 13px; padding: 6px 12px; border-radius: 8px;">
                        {{ $requestLectureList->count() }} yêu cầu cần xử lý
                    </span>
                </div>
                <div class="card-body p-3">
                    <div class="table-overflow">
                        <table id="dt-opt" class="table table-hover table-xl" style="width: 100%;">
                            <thead>
                            <tr>
                                <th style="width: 28%;">Thuộc khóa học</th>
                                <th style="width: 32%;">Tiêu đề bài học / bài thi</th>
                                <th style="width: 14%;">Định dạng</th>
                                <th style="width: 14%;">Thời gian gửi</th>
                                <th style="width: 12%; text-align: center;">Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($requestLectureList as $lecture)
                                <tr id="tr-request-{{ $lecture->id }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="m-r-10" style="width: 32px; height: 32px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #64748b;">
                                                <i class="ti-book"></i>
                                            </div>
                                            <div class="text-truncate" style="max-width: 240px;">
                                                @if($lecture->course)
                                                    <a href="{{ route('courses.show', $lecture->course->id) }}" style="color: #334155; font-weight: 600; font-size: 13px;" title="{{ $lecture->course->title }}">
                                                        {{ $lecture->course->title }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">Khóa học #{{ $lecture->course_id }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ '/courses/' . $lecture->course_id . '/lectures/' . $lecture->id }}" class="lecture-title-text" target="_blank">
                                            {{ $lecture->title }}
                                        </a>
                                        <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                                            Mã bài giảng: #{{ $lecture->id }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($lecture->is_lecture)
                                            <span class="type-badge lecture">
                                                <i class="ti-video-camera"></i> Bài giảng
                                            </span>
                                        @elseif($lecture->is_quiz)
                                            <span class="type-badge quiz">
                                                <i class="ti-check-box"></i> Trắc nghiệm
                                            </span>
                                        @else
                                            <span class="badge badge-light">Khác</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="font-size: 13px; color: #475569;">
                                            <i class="ti-time text-muted m-r-5"></i>{{ $lecture->updated_at ? $lecture->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="text-center" style="white-space: nowrap;">
                                        <a href="#" data-lecture="{{ $lecture->id }}" title="Duyệt bài học này"
                                           class="action-btn-circle approve accept-lecture-request">
                                            <i class="ti-check"></i>
                                        </a>
                                        <a href="{{ '/courses/' . $lecture->course_id . '/lectures/' . $lecture->id }}" target="_blank"
                                           class="action-btn-circle view" title="Xem nội dung bài học">
                                            <i class="ti-eye"></i>
                                        </a>
                                        <a href="#" data-lecture="{{ $lecture->id }}" title="Từ chối & Xóa bài học này"
                                           class="action-btn-circle delete delete-lecture-request">
                                            <i class="ti-trash"></i>
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
    <!-- Content Wrapper END -->
@endsection

@include('admin.bills.partials.edit_status_modal')

@section('inline_scripts')
    <script src="{{ asset('assets/admin/vendor/datatables/media/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/datatables/media/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/tables/data-table.js') }}"></script>
    <script>
        $(document).ready(function () {
            // Duyệt bài học
            $(document).on('click', '.accept-lecture-request', function (event) {
                event.preventDefault();
                var lectureId = $(this).data('lecture');
                var btn = $(this);

                btn.html('<i class="ti-reload" style="display:inline-block; animation: spin 1s infinite linear;"></i>').css('pointer-events', 'none');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: '/admin/lectures/postAccept',
                    data: {
                        lectureId: lectureId,
                    },
                    success: function () {
                        if (typeof $.notify === 'function') {
                            $.notify({
                                message: '{{ __('messages.this_lecture_is_accepted') }}'
                            }, {
                                type: 'success',
                                placement: {
                                    from: "bottom",
                                    align: "right"
                                },
                            });
                        }
                        $('#tr-request-' + lectureId).fadeOut(350, function() {
                            $(this).remove();
                        });
                    },
                    error: function() {
                        btn.html('<i class="ti-check"></i>').css('pointer-events', 'auto');
                        alert('Duyệt bài học không thành công, vui lòng thử lại.');
                    }
                });
            });

            // Từ chối và xóa bài học
            $(document).on('click', '.delete-lecture-request', function (event) {
                event.preventDefault();
                var lectureId = $(this).data('lecture');
                var btn = $(this);

                if (!confirm('Bạn có chắc chắn muốn từ chối và xóa nội dung bài học này không?')) {
                    return;
                }

                btn.html('<i class="ti-reload" style="display:inline-block; animation: spin 1s infinite linear;"></i>').css('pointer-events', 'none');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: '/admin/lectures/postDelete',
                    data: {
                        lectureId: lectureId,
                    },
                    success: function (response) {
                        if (typeof $.notify === 'function') {
                            $.notify({
                                message: 'Đã từ chối và xóa bài học thành công!'
                            }, {
                                type: 'danger',
                                placement: {
                                    from: "bottom",
                                    align: "right"
                                },
                            });
                        }
                        $('#tr-request-' + lectureId).fadeOut(350, function() {
                            $(this).remove();
                        });
                    },
                    error: function() {
                        btn.html('<i class="ti-trash"></i>').css('pointer-events', 'auto');
                        alert('Xóa bài học không thành công, vui lòng thử lại.');
                    }
                });
            });
        });
    </script>
@endsection
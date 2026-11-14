@extends('admin.layouts.default')

@section('title')
    {{ __('titles.all_courses') }}
@endsection

@section('inline_styles')
    <link rel="stylesheet"
          href="{{ asset('assets/admin/vendor/datatables/media/css/dataTables.bootstrap4.min.css') }}"/>
    <style>
        .courses-kpi-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            transition: all 0.25s ease;
            background: #fff;
            margin-bottom: 24px;
        }
        .courses-kpi-card:hover {
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
        .courses-table-card {
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
            color: #2b3445;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: color 0.2s;
        }
        .course-title-text:hover {
            color: #2196f3;
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
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }
        .status-pill.badge-success {
            background-color: #ecfdf5 !important;
            color: #059669 !important;
            border: 1px solid #a7f3d0;
        }
        .status-pill.badge-warning {
            background-color: #fffbeb !important;
            color: #d97706 !important;
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
        .action-btn-circle.edit-price {
            background-color: #fef9c3;
            color: #b45309;
        }
        .action-btn-circle.edit-price:hover {
            background-color: #f59e0b;
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
    <div class="message-display">
        @include('flash::message')
    </div>
    <div class="main-content">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="page-header m-b-20">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="header-title" style="font-weight: 700; color: #1e293b;">{{ __('titles.all_courses') }}</h2>
                        <div class="header-sub-title">
                            <nav class="breadcrumb breadcrumb-dash" style="background: transparent; padding: 0;">
                                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item" style="color: #64748b;">
                                    <i class="ti-home p-r-5"></i>{{ __('titles.r2s_dashboard') }}
                                </a>
                                <span class="breadcrumb-item active" style="color: #334155; font-weight: 500;">
                                    {{ __('titles.all_courses') }}
                                </span>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI Summary Cards -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card courses-kpi-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="kpi-icon-box m-r-20" style="background-color: #eff6ff; color: #3b82f6;">
                                <i class="ti-book"></i>
                            </div>
                            <div>
                                <div style="font-size: 13px; color: #64748b; font-weight: 500; text-transform: uppercase;">Tổng khóa học</div>
                                <div style="font-size: 26px; font-weight: 700; color: #1e293b; line-height: 1.2;">{{ $courses->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card courses-kpi-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="kpi-icon-box m-r-20" style="background-color: #ecfdf5; color: #10b981;">
                                <i class="ti-check-box"></i>
                            </div>
                            <div>
                                <div style="font-size: 13px; color: #64748b; font-weight: 500; text-transform: uppercase;">Đã xuất bản / Duyệt</div>
                                <div style="font-size: 26px; font-weight: 700; color: #059669; line-height: 1.2;">{{ $courses->where('is_accepted', 1)->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card courses-kpi-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="kpi-icon-box m-r-20" style="background-color: #fffbeb; color: #f59e0b;">
                                <i class="ti-time"></i>
                            </div>
                            <div>
                                <div style="font-size: 13px; color: #64748b; font-weight: 500; text-transform: uppercase;">Đang chờ duyệt</div>
                                <div style="font-size: 26px; font-weight: 700; color: #d97706; line-height: 1.2;">{{ $courses->where('is_accepted', 0)->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card courses-table-card">
                <div class="card-header border-bottom p-3 d-flex align-items-center justify-content-between flex-wrap" style="background: #fff; gap: 10px;">
                    <div>
                        <h5 class="m-b-0" style="font-weight: 600; color: #334155;">Danh sách khóa học hệ thống</h5>
                        <small class="text-muted">Bấm vào các nút lọc hoặc thẻ KPI bên trên để xem nhanh khóa học cần duyệt</small>
                    </div>
                    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                        <button type="button" class="btn btn-sm btn-secondary filter-course-status active" data-filter="" style="border-radius: 20px; font-weight: 600; font-size: 12px; padding: 5px 14px;">
                            Tất cả ({{ $courses->count() }})
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning filter-course-status" data-filter="{{ __('titles.pending') }}" style="border-radius: 20px; font-weight: 600; font-size: 12px; padding: 5px 14px;">
                            <i class="ti-time"></i> Chờ duyệt ({{ $courses->where('is_accepted', 0)->count() }})
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success filter-course-status" data-filter="{{ __('titles.accepted') }}" style="border-radius: 20px; font-weight: 600; font-size: 12px; padding: 5px 14px;">
                            <i class="ti-check"></i> Đã duyệt ({{ $courses->where('is_accepted', 1)->count() }})
                        </button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-overflow">
                        <table id="dt-opt" class="table table-hover table-xl" style="width: 100%;">
                            <thead>
                            <tr>
                                <th style="width: 32%;">Khóa học</th>
                                <th style="width: 16%;">Danh mục</th>
                                <th style="width: 16%;">Giảng viên</th>
                                <th style="width: 12%;">Giá / Bán</th>
                                <th style="width: 12%; text-align: center;">Trạng thái</th>
                                <th style="width: 12%; text-align: center;">Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($courses as $course)
                                @php
                                    $avatar = $course->course_avatar;
                                    if ($avatar) {
                                        if (strpos($avatar, 'http') !== 0) {
                                            $avatar = asset(str_replace('public/', '', $avatar));
                                        }
                                    } else {
                                        $avatar = asset('assets/admin/images/others/default-course.png');
                                    }
                                @endphp
                                <tr id="course{{ $course->id }}">
                                    <td>
                                        <div class="d-flex align-items-center" style="gap: 16px;">
                                            <img src="{{ $avatar }}" 
                                                 alt="{{ $course->title }}" 
                                                 class="table-course-thumb"
                                                 style="margin-right: 16px;"
                                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=100&auto=format&fit=crop&q=80';"/>
                                            <div style="min-width: 0; padding-left: 2px;">
                                                <a href="{{ route('courses.show', $course->id) }}" class="course-title-text" title="{{ $course->title }}">
                                                    {{ $course->title }}
                                                </a>
                                                <small class="text-muted d-block" style="font-size: 11px;">Mã: #{{ $course->id }} &bull; Cập nhật: {{ $course->updated_at ? $course->updated_at->format('d/m/Y') : 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.categories.index') }}" class="badge-cat-tag text-decoration-none">
                                            <i class="ti-folder text-muted"></i>
                                            {{ $course->category ? ($course->category->vi_title ?: $course->category->title) : '#' . $course->category_id }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="m-r-8" style="width: 28px; height: 28px; border-radius: 50%; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700;">
                                                {{ substr($course->user ? $course->user->name : 'G', 0, 1) }}
                                            </div>
                                            <div class="text-truncate" style="max-width: 130px;">
                                                @if($course->user)
                                                    <a href="{{ route('instructor_info', $course->user_id) }}" style="color: #334155; font-weight: 500; font-size: 13px;">
                                                        {{ $course->user->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">#{{ $course->user_id }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td id="course-price-cell-{{ $course->id }}">
                                        <div class="price-display-wrap">
                                            <div style="font-weight: 600; color: #1e293b; font-size: 13px;">
                                                @if($course->promotion_price > 0)
                                                    <span class="text-danger font-weight-bold">{{ number_format($course->promotion_price, 0, ',', '.') }} đ</span>
                                                    <span class="text-muted d-block" style="font-size: 11px; text-decoration: line-through;">{{ number_format($course->origin_price, 0, ',', '.') }} đ</span>
                                                @elseif($course->origin_price > 0)
                                                    {{ number_format($course->origin_price, 0, ',', '.') }} đ
                                                @else
                                                    <span class="text-success font-weight-bold">Miễn phí</span>
                                                @endif
                                            </div>
                                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                                <i class="ti-shopping-cart"></i> {{ $course->seller ?: 0 }} lượt bán
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if ($course->is_accepted === 0)
                                            <span class="badge badge-pill badge-warning status-pill" id="label-status-course-{{ $course->id }}">
                                                <i class="ti-time"></i> {{ __('titles.pending') }}
                                            </span>
                                        @else
                                            <span class="badge badge-pill badge-success status-pill" id="label-status-course-{{ $course->id }}">
                                                <i class="ti-check"></i> {{ __('titles.accepted') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center" style="white-space: nowrap;">
                                        @if ($course->is_accepted == 0)
                                            <a href="{{ route('admin.active-course', $course->id) }}" id="button-accept-course-{{ $course->id }}"
                                               class="action-btn-circle approve button-accept-course" data-id="{{ $course->id }}" 
                                               title="Duyệt khóa học">
                                                <i class="ti-check"></i>
                                            </a>
                                        @endif
                                        <a href="javascript:void(0);" class="action-btn-circle edit-price button-edit-price" 
                                           id="btn-edit-price-{{ $course->id }}"
                                           data-id="{{ $course->id }}" 
                                           data-title="{{ $course->title }}"
                                           data-origin-price="{{ (int)$course->origin_price }}" 
                                           data-promotion-price="{{ (int)$course->promotion_price }}"
                                           title="Chỉnh sửa học phí">
                                            <i class="ti-pencil"></i>
                                        </a>
                                        <a href="{{ route('courses.show', $course->id) }}" target="_blank"
                                           class="action-btn-circle view" title="Xem trước khóa học">
                                            <i class="ti-eye"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="action-btn-circle delete" data-toggle="modal"
                                           data-target="#delete-modal" title="Xóa khóa học"
                                           data-url="{{ route('admin.courses.destroy', $course->id) }}">
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

    <!-- Modal Chỉnh sửa học phí khóa học -->
    <div class="modal fade" id="modal-edit-course-price" tabindex="-1" role="dialog" aria-labelledby="modalEditPriceLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 16px 24px;">
                    <h5 class="modal-title font-weight-bold" id="modalEditPriceLabel" style="color: #1e293b; font-size: 16px;">
                        <i class="ti-pencil text-warning m-r-5"></i> Thiết lập giá bán khóa học
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-update-course-price">
                    <input type="hidden" id="edit-course-id" name="course_id" value="">
                    <div class="modal-body p-4">
                        <div class="p-3 m-b-20" style="background: #eff6ff; border-radius: 8px; border-left: 4px solid #3b82f6;">
                            <span class="text-muted d-block" style="font-size: 11px; text-transform: uppercase; font-weight: 600;">Khóa học</span>
                            <h6 id="edit-course-title" class="m-b-0 font-weight-bold" style="color: #1e40af; font-size: 14px;"></h6>
                        </div>
                        <div class="form-group m-b-15">
                            <label for="edit-origin-price" class="font-weight-semibold" style="font-size: 13px; color: #334155;">
                                Giá gốc (VNĐ) <span class="text-muted font-weight-normal">(Để 0 nếu khóa học miễn phí)</span>
                            </label>
                            <div class="input-group">
                                <input type="number" min="0" step="1000" class="form-control" id="edit-origin-price" name="origin_price" placeholder="Ví dụ: 500000" style="border-radius: 6px 0 0 6px;">
                                <div class="input-group-append">
                                    <span class="input-group-text font-weight-bold" style="background: #f1f5f9; color: #475569;">đ</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group m-b-0">
                            <label for="edit-promotion-price" class="font-weight-semibold" style="font-size: 13px; color: #334155;">
                                Giá khuyến mãi / Giá bán thực tế (VNĐ) <span class="text-muted font-weight-normal">(Tùy chọn)</span>
                            </label>
                            <div class="input-group">
                                <input type="number" min="0" step="1000" class="form-control" id="edit-promotion-price" name="promotion_price" placeholder="Để trống nếu không có khuyến mãi" style="border-radius: 6px 0 0 6px;">
                                <div class="input-group-append">
                                    <span class="input-group-text font-weight-bold" style="background: #f1f5f9; color: #475569;">đ</span>
                                </div>
                            </div>
                            <small class="form-text text-muted" style="font-size: 12px; margin-top: 6px;">
                                <i class="ti-info-alt text-info"></i> Nếu nhập giá khuyến mãi, hệ thống sẽ gạch ngang giá gốc và học sinh sẽ thanh toán theo giá này.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 24px;">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" style="border-radius: 6px; font-weight: 500;">Hủy</button>
                        <button type="submit" id="btn-submit-save-price" class="btn btn-warning" style="border-radius: 6px; font-weight: 600; color: #fff;">
                            <i class="ti-save m-r-5"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
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

            $(document).on('click', '.button-accept-course', function (e) {
                e.preventDefault();
                var btn = $(this);
                var courseId = btn.data('id');
                var originalHtml = btn.html();

                btn.html('<i class="ti-reload" style="display:inline-block; animation: spin 1s infinite linear;"></i>').css('pointer-events', 'none');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/admin/courses/' + courseId + '/active',
                    type: 'POST',
                    dataType: 'json',
                    success: function (response) {
                        var responseData = (typeof response === 'string') ? JSON.parse(response) : response;
                        var targetId = responseData.id || courseId;
                        var courseTitle = responseData.title || '';
                        var acceptedLabel = '{{ __('titles.accepted') }}';

                        // Đổi màu nhãn trạng thái sang Đã duyệt
                        var labelEl = $('#label-status-course-' + targetId);
                        labelEl.removeClass('badge-warning').addClass('badge-success');
                        labelEl.html('<i class="ti-check"></i> ' + acceptedLabel);
                        
                        // Ẩn nút duyệt
                        btn.fadeOut(300);

                        // Cập nhật số đếm thẻ KPI
                        var pendingCards = $('.courses-kpi-card').eq(2).find('div[style*="font-size: 26px"]');
                        var acceptedCards = $('.courses-kpi-card').eq(1).find('div[style*="font-size: 26px"]');
                        var curPending = parseInt(pendingCards.text()) || 0;
                        var curAccepted = parseInt(acceptedCards.text()) || 0;
                        if (curPending > 0) pendingCards.text(curPending - 1);
                        acceptedCards.text(curAccepted + 1);

                        // Cập nhật số đếm trên nút lọc
                        var pendingBtn = $('.filter-course-status[data-filter="{{ __('titles.pending') }}"]');
                        if (curPending > 0) {
                            pendingBtn.html('<i class="ti-time"></i> Chờ duyệt (' + (curPending - 1) + ')');
                        }

                        // Thông báo nổi
                        if (typeof $.notify === 'function') {
                            $.notify({
                                message: 'Khóa học <b>' + courseTitle + '</b> đã được phê duyệt thành công!',
                                url: '/courses/' + targetId,
                            }, {
                                type: 'success',
                                placement: {
                                    from: "bottom",
                                    align: "right"
                                }
                            });
                        }
                    },
                    error: function () {
                        // Nếu AJAX gặp trục trặc, fallback điều hướng trực tiếp
                        window.location.href = '/admin/courses/' + courseId + '/active';
                    }
                });
            });

            // Bộ lọc trạng thái khóa học
            $('.filter-course-status').on('click', function () {
                var filterVal = $(this).data('filter');
                $('.filter-course-status').removeClass('active btn-secondary btn-warning btn-success')
                    .each(function() {
                        var f = $(this).data('filter');
                        if (f === '{{ __('titles.pending') }}') $(this).addClass('btn-outline-warning');
                        else if (f === '{{ __('titles.accepted') }}') $(this).addClass('btn-outline-success');
                        else $(this).addClass('btn-outline-secondary');
                    });

                if (filterVal === '{{ __('titles.pending') }}') {
                    $(this).removeClass('btn-outline-warning').addClass('active btn-warning');
                } else if (filterVal === '{{ __('titles.accepted') }}') {
                    $(this).removeClass('btn-outline-success').addClass('active btn-success');
                } else {
                    $(this).removeClass('btn-outline-secondary').addClass('active btn-secondary');
                }

                if ($.fn.DataTable.isDataTable('#dt-opt')) {
                    $('#dt-opt').DataTable().column(4).search(filterVal).draw();
                }
            });

            // Tương tác bấm vào KPI Card để lọc
            $('.courses-kpi-card').css('cursor', 'pointer');
            $('.courses-kpi-card').eq(0).attr('title', 'Bấm để hiển thị tất cả khóa học').on('click', function () {
                $('.filter-course-status[data-filter=""]').trigger('click');
            });
            $('.courses-kpi-card').eq(1).attr('title', 'Bấm để lọc khóa học đã duyệt').on('click', function () {
                $('.filter-course-status[data-filter="{{ __('titles.accepted') }}"]').trigger('click');
            });
            $('.courses-kpi-card').eq(2).attr('title', 'Bấm để lọc khóa học đang chờ duyệt').on('click', function () {
                $('.filter-course-status[data-filter="{{ __('titles.pending') }}"]').trigger('click');
            });

            // Modal Chỉnh sửa học phí
            $(document).on('click', '.button-edit-price', function () {
                var btn = $(this);
                var courseId = btn.data('id');
                var title = btn.data('title');
                var originPrice = btn.attr('data-origin-price') || 0;
                var promotionPrice = btn.attr('data-promotion-price') || '';

                $('#edit-course-id').val(courseId);
                $('#edit-course-title').text(title);
                $('#edit-origin-price').val(originPrice > 0 ? originPrice : 0);
                $('#edit-promotion-price').val(promotionPrice > 0 ? promotionPrice : '');
                $('#modal-edit-course-price').modal('show');
            });

            $('#form-update-course-price').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                var courseId = $('#edit-course-id').val();
                var submitBtn = $('#btn-submit-save-price');
                var originalHtml = submitBtn.html();

                submitBtn.html('<i class="ti-reload" style="display:inline-block; animation: spin 1s infinite linear;"></i> Đang lưu...').prop('disabled', true);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '/admin/courses/' + courseId + '/update-price',
                    type: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function (res) {
                        submitBtn.html(originalHtml).prop('disabled', false);
                        $('#modal-edit-course-price').modal('hide');

                        var responseData = (typeof res === 'string') ? JSON.parse(res) : res;
                        if (responseData.status === 'success' && responseData.course) {
                            var course = responseData.course;
                            var origin = parseFloat(course.origin_price) || 0;
                            var promo = parseFloat(course.promotion_price) || 0;

                            // Cập nhật giao diện cột Giá
                            var priceHtml = '';
                            if (promo > 0) {
                                priceHtml = '<span class="text-danger font-weight-bold">' + Number(promo).toLocaleString('vi-VN') + ' đ</span>' +
                                            '<span class="text-muted d-block" style="font-size: 11px; text-decoration: line-through;">' + Number(origin).toLocaleString('vi-VN') + ' đ</span>';
                            } else if (origin > 0) {
                                priceHtml = '<span style="font-weight: 600; color: #1e293b;">' + Number(origin).toLocaleString('vi-VN') + ' đ</span>';
                            } else {
                                priceHtml = '<span class="text-success font-weight-bold">Miễn phí</span>';
                            }

                            $('#course-price-cell-' + courseId + ' .price-display-wrap > div:first-child').html(priceHtml);

                            // Cập nhật data attribute nút sửa
                            var editBtn = $('#btn-edit-price-' + courseId);
                            editBtn.attr('data-origin-price', origin);
                            editBtn.attr('data-promotion-price', promo > 0 ? promo : 0);

                            if (typeof $.notify === 'function') {
                                $.notify({
                                    message: responseData.message || 'Cập nhật giá khóa học thành công!',
                                }, {
                                    type: 'success',
                                    placement: { from: "bottom", align: "right" }
                                });
                            }
                        }
                    },
                    error: function (xhr) {
                        submitBtn.html(originalHtml).prop('disabled', false);
                        var errMsg = 'Không thể cập nhật giá khóa học.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        if (typeof $.notify === 'function') {
                            $.notify({
                                message: errMsg,
                            }, {
                                type: 'danger',
                                placement: { from: "bottom", align: "right" }
                            });
                        } else {
                            alert(errMsg);
                        }
                    }
                });
            });

            $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
        });
    </script>
@endsection

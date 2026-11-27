@extends('admin.layouts.default')

@section('title')
    {{ __('titles.all_bills') }}
@endsection

@section('inline_styles')
    <link rel="stylesheet"
          href="{{ asset('assets/admin/vendor/datatables/media/css/dataTables.bootstrap4.min.css') }}"/>
    <style>
        /* Card Container */
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #edf2f7;
        }
        .card-body {
            padding: 24px;
        }

        /* DataTables Controls (Length & Search) */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 20px;
        }
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            font-size: 13.5px;
            color: #475569;
            font-weight: 500;
        }
        .dataTables_wrapper .dataTables_length select {
            border-radius: 6px;
            border: 1.5px solid #cbd5e1;
            padding: 6px 12px;
            font-size: 13px;
            margin: 0 6px;
            background-color: #fff;
            color: #334155;
            outline: none;
            transition: all 0.2s;
        }
        .dataTables_wrapper .dataTables_length select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 6px;
            border: 1.5px solid #cbd5e1;
            padding: 6px 14px;
            font-size: 13.5px;
            margin-left: 8px;
            background-color: #fff;
            color: #1e293b;
            outline: none;
            transition: all 0.2s;
            width: 250px;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        /* Modern Table Header */
        #dt-opt {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }
        #dt-opt thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700 !important;
            font-size: 12.5px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 16px !important;
            vertical-align: middle !important;
            border-top: none !important;
            border-bottom: 2px solid #e2e8f0 !important;
            white-space: nowrap !important;
        }
        #dt-opt thead th:first-child {
            padding-left: 20px !important;
        }
        #dt-opt thead th:last-child {
            padding-right: 20px !important;
        }

        /* Table Body */
        #dt-opt tbody tr {
            transition: background-color 0.15s ease-in-out;
        }
        #dt-opt tbody tr:hover {
            background-color: #f8fafc;
        }
        #dt-opt tbody td {
            padding: 14px 16px !important;
            vertical-align: middle !important;
            border-top: 1px solid #f1f5f9;
            color: #334155;
            font-size: 13.5px;
        }
        #dt-opt tbody td:first-child {
            padding-left: 20px !important;
        }
        #dt-opt tbody td:last-child {
            padding-right: 20px !important;
        }

        /* Cell Details */
        .bill-id-link {
            font-weight: 700;
            color: #2563eb;
            text-decoration: none;
            display: inline-block;
        }
        .bill-id-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }
        .customer-name {
            font-weight: 600;
            color: #1e293b;
        }
        .course-tag {
            display: inline-block;
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            margin: 2px 2px;
            transition: all 0.15s;
        }
        .course-tag:hover {
            background: #dbeafe;
            color: #1d4ed8;
        }
        .price-text {
            font-weight: 700;
            color: #0f172a;
            font-size: 14px;
        }
        .date-text {
            color: #64748b;
            font-size: 13px;
            white-space: nowrap;
        }

        /* Action Buttons */
        .action-btn-group {
            display: inline-flex;
            gap: 6px;
            align-items: center;
        }
        .action-btn-group .btn {
            border-radius: 6px;
            padding: 5px 11px;
            font-size: 12px;
            font-weight: 500;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            transition: all 0.15s;
        }
        .action-btn-group .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .btn-view-detail {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-view-detail:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }

        /* Thẻ thống kê nhanh */
        .bill-summary { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 20px; }
        .bill-summary .sum-item {
            flex: 1 1 180px; display: flex; align-items: center; gap: 12px;
            background: #fff; border: 1px solid #edf2f7; border-radius: 12px; padding: 14px 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }
        .bill-summary .sum-icon {
            width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 18px;
        }
        .bill-summary .sum-num { font-size: 20px; font-weight: 800; color: #0f172a; line-height: 1.1; }
        .bill-summary .sum-label { font-size: 12.5px; color: #64748b; font-weight: 500; }
        .sum-all .sum-icon { background: #eff6ff; color: #2563eb; }
        .sum-pending .sum-icon { background: #fffbeb; color: #d97706; }
        .sum-paid .sum-icon { background: #f0fdf4; color: #16a34a; }
        .sum-canceled .sum-icon { background: #fef2f2; color: #dc2626; }

        /* Huy hiệu trạng thái */
        .status-pill {
            display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;
            font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 999px; border: 1px solid transparent;
        }
        .status-pending { background: #fffbeb; color: #b45309; border-color: #fde68a; }
        .status-paid { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
        .status-canceled { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
        .status-other { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }

        /* Ô khách hàng, khóa học, thời gian */
        .customer-phone { display: block; font-size: 12px; color: #94a3b8; margin-top: 2px; }
        .course-list { display: flex; flex-direction: column; gap: 4px; max-width: 260px; }
        .course-link {
            color: #1e293b; font-weight: 600; font-size: 13px; line-height: 1.35; text-decoration: none;
            display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;
        }
        .course-link:hover { color: #2563eb; }
        .course-more { font-size: 11.5px; color: #94a3b8; }
        .date-text small { display: block; color: #94a3b8; font-size: 11.5px; }

        /* Nút thao tác: gọn, cùng một hàng */
        .table-overflow .action-btn-group { flex-wrap: nowrap; max-width: none; justify-content: center; }
        .icon-btn { flex: 0 0 32px; margin: 0 !important; }
        #dt-opt td.col-actions { min-width: 124px; }
        #dt-opt .price-text { white-space: nowrap; }
        #dt-opt td.col-price { white-space: nowrap; min-width: 120px; }
        #dt-opt td.col-customer { min-width: 150px; }
        .icon-btn {
            width: 32px; height: 32px; padding: 0 !important; border-radius: 8px !important;
            display: inline-flex; align-items: center; justify-content: center; font-size: 14px !important;
            border: 1px solid transparent; transition: all .15s;
        }
        .icon-btn.btn-view-detail { background: #fff; color: #475569; border-color: #cbd5e1; }
        .icon-btn.btn-view-detail:hover { background: #f1f5f9; }
        .icon-btn.btn-approve { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
        .icon-btn.btn-approve:hover { background: #16a34a; color: #fff; }
        .icon-btn.btn-reject { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }
        .icon-btn.btn-reject:hover { background: #dc2626; color: #fff; }
        .icon-btn.btn-restore { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
        .icon-btn.btn-restore:hover { background: #2563eb; color: #fff; }
        #dt-opt thead th.no-sort, #dt-opt thead th.no-sort:hover { background-image: none !important; cursor: default; }
        #dt-opt thead th.no-sort::before, #dt-opt thead th.no-sort::after { display: none !important; }
    </style>
@endsection

@section('content')
    <!-- Content Wrapper START -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <h2 class="header-title">{{ __('titles.all_bills') }}</h2>
                <div class="header-sub-title">
                    <nav class="breadcrumb breadcrumb-dash">
                        <a href="#" class="breadcrumb-item"><i
                                    class="ti-home p-r-5"></i>{{ __('titles.r2s_dashboard') }}</a>
                        <a class="breadcrumb-item" href="#">{{ __('titles.bills') }}</a>
                        <span class="breadcrumb-item active">{{ __('titles.all_bills') }}</span>
                    </nav>
                </div>
            </div>
            @php
                $paidStatuses = [\App\Models\Bill::ALREADY_PAID, \App\Models\Bill::ACTIVATED];
                $countAll = $billsList->count();
                $countPending = $billsList->where('status', \App\Models\Bill::PENDING)->count();
                $countPaid = $billsList->whereIn('status', $paidStatuses)->count();
                $countCanceled = $billsList->where('status', \App\Models\Bill::CANCELED)->count();
            @endphp
            <div class="bill-summary">
                <div class="sum-item sum-all"><div class="sum-icon"><i class="fa fa-file-text-o"></i></div><div><div class="sum-num">{{ $countAll }}</div><div class="sum-label">Tổng đơn hàng</div></div></div>
                <div class="sum-item sum-pending"><div class="sum-icon"><i class="fa fa-clock-o"></i></div><div><div class="sum-num">{{ $countPending }}</div><div class="sum-label">Chờ thanh toán</div></div></div>
                <div class="sum-item sum-paid"><div class="sum-icon"><i class="fa fa-check-circle"></i></div><div><div class="sum-num">{{ $countPaid }}</div><div class="sum-label">Đã thanh toán</div></div></div>
                <div class="sum-item sum-canceled"><div class="sum-icon"><i class="fa fa-ban"></i></div><div><div class="sum-num">{{ $countCanceled }}</div><div class="sum-label">Đã hủy</div></div></div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-overflow">
                        <table id="dt-opt" class="table table-hover table-xl">
                            <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Trạng thái</th>
                                <th>Khóa học</th>
                                <th>Cập nhật lần cuối</th>
                                <th class="text-right">Tổng tiền</th>
                                <th class="text-center no-sort" data-orderable="false">Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($billsList as $bill)
                                @php $isPaid = in_array($bill->status, $paidStatuses); @endphp
                                <tr>
                                    <td data-order="{{ $bill->id }}">
                                        <a href="{{ route('admin.bills.show', $bill->id) }}" class="bill-id-link">#{{ $bill->id }}</a>
                                    </td>
                                    <td class="col-customer">
                                        <span class="customer-name">{{ $bill->customer_name }}</span>
                                        @if($bill->customer_phone)
                                            <span class="customer-phone"><i class="fa fa-phone"></i> {{ $bill->customer_phone }}</span>
                                        @endif
                                    </td>
                                    <td id="bill-status-{{ $bill->id }}" style="white-space: nowrap;">
                                        @if($bill->status === \App\Models\Bill::PENDING)
                                            <span class="status-pill status-pending"><i class="fa fa-clock-o"></i> Chờ thanh toán</span>
                                        @elseif($isPaid)
                                            <span class="status-pill status-paid"><i class="fa fa-check-circle"></i> Đã thanh toán</span>
                                        @elseif($bill->status === \App\Models\Bill::CANCELED)
                                            <span class="status-pill status-canceled"><i class="fa fa-ban"></i> Đã hủy</span>
                                        @else
                                            <span class="status-pill status-other">{{ \App\Models\Bill::$status[$bill->status] ?? 'Đang xử lý' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="course-list">
                                            @foreach($bill->billCourses->take(2) as $billCourse)
                                                @if($billCourse->course)
                                                    <a href="{{ route('courses.show', $billCourse->course_id) }}" class="course-link" title="{{ $billCourse->course->title }}">{{ $billCourse->course->title }}</a>
                                                @else
                                                    <span class="course-more">Khóa học #{{ $billCourse->course_id }} (đã xóa)</span>
                                                @endif
                                            @endforeach
                                            @if($bill->billCourses->count() > 2)
                                                <span class="course-more">+ {{ $bill->billCourses->count() - 2 }} khóa học khác</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-order="{{ optional($bill->updated_at)->timestamp }}">
                                        <span class="date-text">
                                            {{ optional($bill->updated_at)->format('d/m/Y') }}
                                            <small>{{ optional($bill->updated_at)->format('H:i') }}</small>
                                        </span>
                                    </td>
                                    <td class="text-right col-price" data-order="{{ (float)$bill->total_amount }}">
                                        <span class="price-text">{{ format_price($bill->total_amount) }}</span>
                                    </td>
                                    <td class="text-center col-actions" style="white-space: nowrap;">
                                        <div class="action-btn-group">
                                            <a href="{{ route('admin.bills.show', $bill->id) }}" class="btn icon-btn btn-view-detail" title="Xem chi tiết đơn hàng">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            @if($bill->status === \App\Models\Bill::PENDING)
                                                <button type="button" class="btn icon-btn btn-approve btn-manual-activate" data-id="{{ $bill->id }}" title="Duyệt tay: dùng khi học viên đã chuyển khoản nhưng thiếu nội dung">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                                <button type="button" class="btn icon-btn btn-reject btn-cancel-bill" data-id="{{ $bill->id }}" title="Hủy đơn hàng này">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            @elseif($isPaid)
                                                <button type="button" class="btn icon-btn btn-reject btn-cancel-bill" data-id="{{ $bill->id }}" data-revoke="1" title="Thu hồi khóa học">
                                                    <i class="fa fa-user-times"></i>
                                                </button>
                                            @elseif($bill->status === \App\Models\Bill::CANCELED)
                                                <button type="button" class="btn icon-btn btn-restore btn-manual-activate" data-id="{{ $bill->id }}" title="Kích hoạt lại khóa học cho đơn này">
                                                    <i class="fa fa-refresh"></i>
                                                </button>
                                            @endif
                                        </div>
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

@section('inline_scripts')
    <script src="{{ asset('assets/admin/vendor/datatables/media/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/datatables/media/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/tables/data-table.js') }}"></script>
    <script>
        $(document).ready(function () {
            // Duyệt đơn thủ công (khi học sinh chuyển khoản nhưng quên ghi nội dung RTS...)
            $(document).on('click', '.btn-manual-activate', function (e) {
                e.preventDefault();
                var billId = $(this).data('id');
                if (confirm('Xác nhận học viên đã thanh toán đơn hàng #' + billId + '?\nHệ thống sẽ chuyển trạng thái "Đã thanh toán" và tự động cấp quyền truy cập khóa học cho học viên.')) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: 'POST',
                        url: '/admin/bills/status/update',
                        data: {
                            billId: billId,
                            statusId: 2, // ALREADY_PAID
                        },
                        success: function () {
                            location.reload();
                        },
                        error: function () {
                            alert('Có lỗi xảy ra, vui lòng thử lại.');
                        }
                    });
                }
            });

            // Hủy đơn hàng chờ thanh toán / thu hồi khóa học của đơn đã thanh toán
            $(document).on('click', '.btn-cancel-bill', function (e) {
                e.preventDefault();
                var billId = $(this).data('id');
                var message = $(this).data('revoke')
                    ? 'Xác nhận THU HỒI khóa học của đơn hàng #' + billId + '?\nHọc viên sẽ mất quyền truy cập các khóa học trong đơn này.'
                    : 'Bạn có chắc chắn muốn HỦY đơn hàng #' + billId + '?';
                if (confirm(message)) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: 'POST',
                        url: '/admin/bills/status/update',
                        data: {
                            billId: billId,
                            statusId: 4, // CANCELED
                        },
                        success: function () {
                            location.reload();
                        },
                        error: function () {
                            alert('Có lỗi xảy ra, vui lòng thử lại.');
                        }
                    });
                }
            });

            // Mặc định hiển thị đơn mới nhất lên đầu
            $('#dt-opt').DataTable().order([0, 'desc']).draw();
        });
    </script>
@endsection
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
            <div class="card">
                <div class="card-body">
                    <div class="table-overflow">
                        <table id="dt-opt" class="table table-hover table-xl">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên khách hàng</th>
                                <th>Trạng thái</th>
                                <th>Mã khóa học</th>
                                <th>Giao dịch gần nhất</th>
                                <th>Tổng tiền</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($billsList as $bill)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.bills.show', $bill->id) }}" class="bill-id-link">
                                            #{{ $bill->id }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="customer-name">{{ $bill->customer_name }}</span>
                                    </td>
                                    <td id="bill-status-{{ $bill->id }}" style="white-space: nowrap;">
                                        @if($bill->status === \App\Models\Bill::PENDING)
                                            <span class="badge badge-pill" style="font-size: 12px; font-weight: 600; padding: 6px 12px; background: #fffbeb; color: #b45309; border: 1px solid #fde68a;">
                                                <i class="fa fa-clock-o m-r-5"></i> Chờ thanh toán
                                            </span>
                                        @elseif(in_array($bill->status, [\App\Models\Bill::ALREADY_PAID, \App\Models\Bill::ACTIVATED]))
                                            <span class="badge badge-pill" style="font-size: 12px; font-weight: 600; padding: 6px 12px; background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;">
                                                <i class="fa fa-check-circle m-r-5"></i> Đã thanh toán
                                            </span>
                                        @elseif($bill->status === \App\Models\Bill::CANCELED)
                                            <span class="badge badge-pill" style="font-size: 12px; font-weight: 600; padding: 6px 12px; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;">
                                                <i class="fa fa-ban m-r-5"></i> Đã hủy
                                            </span>
                                        @else
                                            <span class="badge badge-pill" style="font-size: 12px; font-weight: 600; padding: 6px 12px; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                                {{ \App\Models\Bill::$status[$bill->status] ?? 'Đang xử lý' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach(\App\Models\BillCourse::where('bill_id', $bill->id)->get() as $billCourse)
                                            <a href="{{ route('admin.courses.show', $billCourse->course_id) }}" class="course-tag">
                                                #{{ $billCourse->course_id }}
                                            </a>
                                        @endforeach
                                    </td>
                                    <td>
                                        <span class="date-text">{{ $bill->updated_at }}</span>
                                    </td>
                                    <td>
                                        <span class="price-text">{{ format_price($bill->total_amount) }}</span>
                                    </td>
                                    <td class="text-center" style="white-space: nowrap;">
                                        <div class="action-btn-group">
                                            <a href="{{ route('admin.bills.show', $bill->id) }}" class="btn btn-xs btn-view-detail" title="Xem chi tiết đơn hàng">
                                                <i class="fa fa-eye"></i> Chi tiết
                                            </a>

                                            @if($bill->status === \App\Models\Bill::PENDING)
                                                <button type="button" class="btn btn-xs btn-success btn-manual-activate" data-id="{{ $bill->id }}" title="Duyệt đơn thủ công khi học viên chuyển khoản bị sót nội dung">
                                                    <i class="fa fa-check"></i> Duyệt tay
                                                </button>
                                                <button type="button" class="btn btn-xs btn-danger btn-cancel-bill" data-id="{{ $bill->id }}" title="Hủy đơn hàng này">
                                                    <i class="fa fa-times"></i> Hủy
                                                </button>
                                            @elseif(in_array($bill->status, [\App\Models\Bill::ALREADY_PAID, \App\Models\Bill::ACTIVATED]))
                                                <button type="button" class="btn btn-xs btn-outline-danger btn-cancel-bill" data-id="{{ $bill->id }}" title="Hủy đơn và thu hồi quyền học">
                                                    <i class="fa fa-undo"></i> Hoàn / Hủy
                                                </button>
                                            @elseif($bill->status === \App\Models\Bill::CANCELED)
                                                <button type="button" class="btn btn-xs btn-outline-success btn-manual-activate" data-id="{{ $bill->id }}" title="Kích hoạt lại khóa học cho đơn này">
                                                    <i class="fa fa-refresh"></i> Kích hoạt lại
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

            // Hủy đơn hàng (hoặc hoàn tiền, thu hồi quyền học)
            $(document).on('click', '.btn-cancel-bill', function (e) {
                e.preventDefault();
                var billId = $(this).data('id');
                if (confirm('Bạn có chắc chắn muốn HỦY đơn hàng #' + billId + '?\nNếu đơn hàng đã kích hoạt, hệ thống sẽ tự động THU HỒI khóa học của học viên.')) {
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
        });  }, 'json');
            });
        })
    </script>
@endsection
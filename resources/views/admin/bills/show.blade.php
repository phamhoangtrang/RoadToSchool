@extends('admin.layouts.default')

@section('title')
    {{ __('titles.bill_detail') . ' #' . $bill->id }}
@endsection

@section('inline_styles')
@endsection

@section('content')
    <!-- Content Wrapper START -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="container">
                <div class="card">
                    <div class="p-v-5 p-h-10 border bottom print-invisible d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('admin.bills.index') }}" class="btn text-gray text-hover display-block p-10 m-b-0">
                                <i class="fa fa-arrow-left p-r-5"></i>
                                <b>Danh sách đơn hàng</b>
                            </a>
                        </div>
                        <ul class="list-unstyle list-inline text-right m-b-0">
                            <li class="list-inline-item">
                                <a href="#" class="btn text-gray text-hover display-block p-10 m-b-0"
                                    onclick="window.print();">
                                    <i class="ti-printer text-info p-r-5"></i>
                                    <b>In hóa đơn</b>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" class="text-gray text-hover display-block p-10 m-b-0">
                                    <i class="fa fa-file-pdf-o text-danger p-r-5"></i>
                                    <b>Xuất PDF</b>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="p-h-30">
                            <div class="m-t-15">
                                <div class="inline-block">
                                    <img class="img-fluid" src="{{ asset('assets/admin/images/logo/logo.jpg') }}"
                                         alt="">
                                    <address class="p-l-10 m-t-20">
                                        <b class="text-dark">RoadToSchool Inc.</b><br>
                                        <span>Số 1 Đại Cồ Việt</span><br>
                                        <span>Hà Nội, Việt Nam</span><br>
                                        <abbr class="text-dark" title="Phone">Điện thoại:</abbr>
                                        <span>(+84) 965 818 552</span>
                                    </address>
                                </div>
                                <div class="pull-right text-right">
                                    <h2>CHI TIẾT ĐƠN HÀNG #{{ $bill->id }}</h2>
                                    <div class="m-t-10 m-b-10">
                                        <span class="text-muted m-r-5">Trạng thái:</span>
                                        @if($bill->status === \App\Models\Bill::PENDING)
                                            <span class="badge badge-warning" style="font-size: 13px; padding: 6px 14px;">
                                                <i class="fa fa-clock-o"></i> Chờ thanh toán
                                            </span>
                                        @elseif(in_array($bill->status, [\App\Models\Bill::ALREADY_PAID, \App\Models\Bill::ACTIVATED]))
                                            <span class="badge badge-success" style="font-size: 13px; padding: 6px 14px;">
                                                <i class="fa fa-check-circle"></i> Đã thanh toán (Đã kích hoạt)
                                            </span>
                                        @elseif($bill->status === \App\Models\Bill::CANCELED)
                                            <span class="badge badge-secondary" style="font-size: 13px; padding: 6px 14px;">
                                                <i class="fa fa-ban"></i> Đã hủy
                                            </span>
                                        @else
                                            <span class="badge badge-info" style="font-size: 13px; padding: 6px 14px;">
                                                {{ \App\Models\Bill::$status[$bill->status] ?? 'Đang xử lý' }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="m-t-10">
                                        @if($bill->status === \App\Models\Bill::PENDING)
                                            <button type="button" class="btn btn-sm btn-success btn-manual-activate" data-id="{{ $bill->id }}" title="Duyệt đơn thủ công nếu học viên chuyển khoản thành công">
                                                <i class="fa fa-check"></i> Duyệt đơn thủ công
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger btn-cancel-bill" data-id="{{ $bill->id }}" title="Hủy đơn hàng">
                                                <i class="fa fa-times"></i> Hủy đơn
                                            </button>
                                        @elseif(in_array($bill->status, [\App\Models\Bill::ALREADY_PAID, \App\Models\Bill::ACTIVATED]))
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-cancel-bill" data-id="{{ $bill->id }}" title="Hoàn tiền và thu hồi quyền học">
                                                <i class="fa fa-undo"></i> Hoàn tiền / Thu hồi khóa học
                                            </button>
                                        @elseif($bill->status === \App\Models\Bill::CANCELED)
                                            <button type="button" class="btn btn-sm btn-outline-success btn-manual-activate" data-id="{{ $bill->id }}" title="Kích hoạt lại khóa học cho đơn này">
                                                <i class="fa fa-refresh"></i> Kích hoạt lại đơn hàng
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row m-t-20">
                                <div class="col-sm-9">
                                    <h3 class="p-l-10 m-t-10">Thông tin khách hàng</h3>
                                    <address class="p-l-10 m-t-10">
                                        <b class="text-dark">Họ tên: </b><span> {{ $bill->customer_name }} </span><br>
                                        <b class="text-dark">Số điện thoại: </b><span> {{ $bill->customer_phone }} </span><br>
                                        @if(!empty($bill->customer_address))
                                            <b class="text-dark">Địa chỉ: </b><span> {{ $bill->customer_address }} </span><br>
                                        @endif
                                        <b class="text-dark">Ghi chú: </b><span> {{ $bill->customer_note ?? 'Không có ghi chú' }} </span><br>
                                    </address>
                                </div>
                                <div class="col-sm-3">
                                    <div class="m-t-80">
                                        <div class="text-dark text-uppercase d-inline-block"><b>Mã đơn hàng :</b></div>
                                        <div class="pull-right">#{{ $bill->id }}</div>
                                    </div>
                                    <div class="text-dark text-uppercase d-inline-block"><b> {{ __('titles.date') }}
                                            :</b></div>
                                    <div class="pull-right">{{ $bill->created_at->toDateString() }}</div>
                                    <br>
                                    <div class="text-dark text-uppercase d-inline-block"><b> {{ __('titles.time') }}
                                            :</b></div>
                                    <div class="pull-right">{{ $bill->created_at->toTimeString() }}</div>
                                </div>
                            </div>
                            <div class="row m-t-20">
                                <div class="col-sm-12">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th>Mã khóa học</th>
                                            <th>Tên khóa học</th>
                                            <th class="text-right">Đơn giá</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($billCourses as $billCourse)
                                            <tr>
                                                <td><a href="{{ route('courses.show', $billCourse->course_id) }}"> #{{ $billCourse->course_id }} </a></td>
                                                <td>{{ App\Models\Course::findOrFail($billCourse->course_id)->title }}</td>
                                                <td class="text-right">{{ format_price($billCourse->price) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <div class="row m-t-30">
                                        <div class="col-sm-12">
                                            <div class="pull-right text-right">
                                                <p>Tạm tính: {{ format_price($bill->total_amount) }}</p>
                                                <p>Giảm giá : 0 ₫ </p>
                                                <hr>
                                                <h3><b>Tổng cộng :</b> {{ format_price($bill->total_amount) }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    {{--<div class="row m-t-30">--}}
                                    {{--<div class="col-sm-12">--}}
                                    {{--<div class="border top bottom p-v-20">--}}
                                    {{--<p class="text-opacity"><small>In exceptional circumstances, Financial Services can provide an urgent manually processed special cheque. Note, however, that urgent special cheques should be requested only on an emergency basis as manually produced cheques involve duplication of effort and considerable staff resources. Requests need to be supported by a letter explaining the circumstances to justify the special cheque payment.</small></p>--}}
                                    {{--</div>--}}
                                    {{--</div>--}}
                                    {{--</div>--}}
                                    <div class="row m-v-20">
                                        <div class="col-sm-6">
                                            <img class="img-fluid text-opacity m-t--5" width="100"
                                                 src="{{ asset('assets/admin/images/logo/logo.jpg') }}" alt="">
                                        </div>
                                        <div class="col-sm-6 text-right">
                                            <small><b>Phone:</b> (+84) 965 818 552</small>
                                            <br>
                                            <small>hungph.dev.ict@gmail.com</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Wrapper END -->
@endsection

@section('inline_scripts')
    <script>
        $(document).ready(function () {
            // Duyệt đơn thủ công
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

            // Hủy đơn hàng / Hoàn tiền
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
        });
    </script>
@endsection
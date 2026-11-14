@extends('user.layouts.default')

@section('title')
    Thanh toán đơn hàng #{{ $bill->id }} qua VietQR
@endsection

@section('inline_styles')
<style>
    .payment-container {
        padding: 20px 0 60px;
    }
    .payment-container .breadcrumb {
        position: static !important;
        display: inline-block;
        margin: 0 0 20px 0 !important;
        border-radius: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 8px 16px;
    }
    .payment-box {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        padding: 30px;
        margin-bottom: 25px;
    }
    .qr-card {
        text-align: center;
        background: linear-gradient(145deg, #f8fbfd, #ffffff);
        border: 1px solid #e2edf6;
        border-radius: 12px;
        padding: 25px 20px;
    }
    .qr-image-wrapper {
        display: inline-block;
        padding: 12px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        margin: 15px 0;
    }
    .qr-image-wrapper img {
        max-width: 250px;
        width: 100%;
        height: auto;
        display: block;
    }
    .pulse-status {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 15px;
        color: #0284c7;
        font-weight: 600;
        font-size: 14px;
    }
    .spinner-dot {
        width: 10px;
        height: 10px;
        background-color: #0284c7;
        border-radius: 50%;
        margin-right: 8px;
        animation: pulseAnimation 1.5s infinite ease-in-out;
    }
    @keyframes pulseAnimation {
        0%, 100% { transform: scale(0.8); opacity: 0.5; }
        50% { transform: scale(1.3); opacity: 1; }
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px dashed #e5e7eb;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label {
        color: #6b7280;
        font-size: 14px;
    }
    .info-value {
        font-weight: 600;
        color: #1f2937;
        font-size: 15px;
        text-align: right;
    }
    .btn-copy {
        margin-left: 8px;
        padding: 2px 8px;
        font-size: 12px;
        border-radius: 4px;
        border: 1px solid #d1d5db;
        background: #f9fafb;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-copy:hover {
        background: #e5e7eb;
        color: #111827;
    }
    .notice-card {
        background: #fffbeb;
        border: 1px solid #fef3c7;
        border-left: 4px solid #f59e0b;
        border-radius: 6px;
        padding: 15px;
        margin-top: 20px;
        font-size: 13.5px;
        color: #92400e;
        line-height: 1.5;
    }
    .demo-sandbox-btn {
        margin-top: 20px;
        padding: 10px 16px;
        font-size: 13px;
        background: #f0fdf4;
        color: #166534;
        border: 1px dashed #22c55e;
        border-radius: 6px;
        cursor: pointer;
        width: 100%;
        font-weight: 600;
        transition: all 0.2s;
    }
    .demo-sandbox-btn:hover {
        background: #dcfce7;
    }
</style>
@endsection

@section('content')
<div class="payment-container">
    <div class="container">
        <div style="margin-bottom: 10px;">
            {{ Breadcrumbs::render('bills_payment', $bill) }}
        </div>
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center" style="margin-top: 15px; margin-bottom: 30px; font-weight: 700; color: #1e293b;">
                    <i class="fa fa-qrcode text-primary"></i> Thanh toán đơn hàng qua mã QR
                </h2>
            </div>
        </div>

        <div class="row">
            <!-- Cột trái: Mã QR và Trạng thái -->
            <div class="col-md-5 col-sm-12">
                <div class="payment-box qr-card">
                    <h4 style="font-weight: 700; color: #0f172a; margin-top: 0;">Quét mã VietQR bằng app Ngân hàng</h4>
                    <p style="color: #64748b; font-size: 13px; margin-bottom: 5px;">Hỗ trợ tất cả ứng dụng Ngân hàng (MB, VCB, Techcombank...) & Ví MoMo, ZaloPay</p>

                    <div class="qr-image-wrapper">
                        <img src="{{ $qrUrl }}" alt="Mã VietQR thanh toán" id="vietqr-image">
                    </div>

                    <div class="pulse-status">
                        <span class="spinner-dot"></span>
                        <span id="payment-status-text">Đang chờ hệ thống nhận thanh toán...</span>
                    </div>

                    <div style="margin-top: 10px; font-size: 12.5px; color: #64748b;">
                        Hệ thống sẽ <strong>tự động chuyển trang</strong> ngay sau khi chuyển khoản thành công.
                    </div>

                    <!-- Nút Demo Giả Lập Dành Cho Báo Cáo Đồ Án -->
                    <button type="button" class="demo-sandbox-btn" id="btn-simulate-payment">
                        <i class="fa fa-magic"></i> Demo: Giả lập thanh toán thành công (Báo cáo đồ án)
                    </button>
                </div>
            </div>

            <!-- Cột phải: Thông tin đơn hàng & Số tài khoản chuyển khoản thủ công -->
            <div class="col-md-7 col-sm-12">
                <div class="payment-box">
                    <h4 style="font-weight: 700; color: #0f172a; margin-top: 0; margin-bottom: 15px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                        <i class="fa fa-file-text-o text-primary"></i> Thông tin chuyển khoản
                    </h4>

                    <div class="info-row">
                        <span class="info-label">Ngân hàng thụ hưởng</span>
                        <span class="info-value"><strong>MB Bank (Ngân hàng Quân Đội)</strong></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Chủ tài khoản</span>
                        <span class="info-value"><strong>{{ $accountName }}</strong></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Số tài khoản</span>
                        <span class="info-value">
                            <span id="text-account-no">{{ $accountNo }}</span>
                            <button class="btn-copy" onclick="copyText('{{ $accountNo }}', 'Đã sao chép số tài khoản!')" title="Sao chép">
                                <i class="fa fa-copy"></i> Sao chép
                            </button>
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Số tiền thanh toán</span>
                        <span class="info-value" style="color: #dc2626; font-size: 18px;">
                            {{ format_price($amount) }}
                            <button class="btn-copy" onclick="copyText('{{ $amount }}', 'Đã sao chép số tiền!')" title="Sao chép">
                                <i class="fa fa-copy"></i>
                            </button>
                        </span>
                    </div>

                    <div class="info-row" style="background: #f8fafc; padding: 12px 10px; border-radius: 6px;">
                        <span class="info-label" style="font-weight: 600; color: #1e293b;">Nội dung chuyển khoản:</span>
                        <span class="info-value" style="color: #0284c7; font-size: 17px; font-family: monospace;">
                            <strong id="text-order-code">{{ $orderCode }}</strong>
                            <button class="btn-copy" onclick="copyText('{{ $orderCode }}', 'Đã sao chép nội dung chuyển khoản!')" title="Sao chép">
                                <i class="fa fa-copy"></i> Sao chép
                            </button>
                        </span>
                    </div>

                    <div class="notice-card">
                        <i class="fa fa-exclamation-triangle"></i> <strong>Lưu ý quan trọng:</strong>
                        Vui lòng chuyển <strong>chính xác số tiền</strong> và giữ nguyên <strong>nội dung chuyển khoản ({{ $orderCode }})</strong> để hệ thống tự động kích hoạt khóa học trong vài giây.
                    </div>

                    <!-- Khóa học đã mua -->
                    <div style="margin-top: 25px;">
                        <h5 style="font-weight: 700; color: #334155; margin-bottom: 12px;">Khóa học trong đơn hàng:</h5>
                        <ul class="list-group" style="margin-bottom: 0;">
                            @foreach($bill->billCourses as $billCourse)
                                @if($billCourse->course)
                                    <li class="list-group-item" style="display: flex; justify-content: space-between; align-items: center; border-color: #f1f5f9;">
                                        <span>
                                            <i class="fa fa-book text-muted"></i>
                                            <strong>{{ $billCourse->course->title }}</strong>
                                            <small class="text-muted">({{ $billCourse->course->user->name ?? 'Giảng viên' }})</small>
                                        </span>
                                        <span class="text-danger font-weight-bold">{{ format_price($billCourse->price) }}</span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>

                    <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #f1f5f9; text-align: right;">
                        <a href="{{ route('home') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #ffffff; color: #1e293b; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 700; font-size: 13.5px; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.06); transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'; this.style.borderColor='#94a3b8'; this.style.color='#0f172a';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#cbd5e1'; this.style.color='#1e293b';">
                            <i class="fa fa-angle-left" style="font-size: 16px; font-weight: bold;"></i> QUAY LẠI TRANG CHỦ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('inline_scripts')
<script>
    // Copy clipboard function
    function copyText(text, successMsg) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function() {
                alert(successMsg);
            });
        } else {
            var tempInput = document.createElement("input");
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
            alert(successMsg);
        }
    }

    $(document).ready(function() {
        // Polling check order status every 3 seconds
        var checkInterval = setInterval(function() {
            $.ajax({
                url: "{{ route('bills.check_status', $bill->id) }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if (response.is_paid) {
                        clearInterval(checkInterval);
                        $('#payment-status-text').html('<span style="color: #16a34a;"><i class="fa fa-check-circle"></i> Đã nhận thanh toán! Đang chuyển hướng...</span>');
                        setTimeout(function() {
                            window.location.href = response.redirect_url;
                        }, 1000);
                    }
                }
            });
        }, 3000);

        // Simulation Demo button for graduation thesis defense
        $('#btn-simulate-payment').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý giả lập...');

            $.ajax({
                url: "{{ route('bills.simulate_payment', $bill->id) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        $('#payment-status-text').html('<span style="color: #16a34a;"><i class="fa fa-check-circle"></i> ' + response.message + '</span>');
                        setTimeout(function() {
                            window.location.href = response.redirect_url;
                        }, 1200);
                    }
                },
                error: function() {
                    alert('Lỗi khi giả lập thanh toán, vui lòng thử lại.');
                    btn.prop('disabled', false).html('<i class="fa fa-magic"></i> Demo: Giả lập thanh toán thành công (Báo cáo đồ án)');
                }
            });
        });
    });
</script>
@endsection

@extends('user.layouts.default')

@section('title')
    {{ __('titles.checkout_success') }}
@endsection

@section('inline_styles')
@endsection

@section('content')
    {{ Breadcrumbs::render('checkout_success') }} <br>
    <section id="content" style="padding: 40px 0 60px;">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="inner-box posting text-center" style="background: #fff; padding: 40px 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
                        <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: #dcfce7; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 38px;">
                            <i class="fa fa-check"></i>
                        </div>
                        <h2 style="font-weight: 700; color: #1e293b; margin-bottom: 12px;">Thanh toán thành công!</h2>
                        <p style="font-size: 16px; color: #475569; margin-bottom: 25px;">
                            Cảm ơn bạn đã đăng ký khóa học tại <strong>Road To School</strong>.<br>
                            Hệ thống đã nhận được thanh toán và <strong>tự động kích hoạt khóa học</strong> vào tài khoản của bạn.
                        </p>

                        @if(isset($bill))
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px 20px; text-align: left; margin-bottom: 30px; max-width: 500px; margin-left: auto; margin-right: auto;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span style="color: #64748b;">Mã đơn hàng:</span>
                                    <strong style="color: #0284c7;">#RTS{{ $bill->id }}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span style="color: #64748b;">Phương thức:</span>
                                    <span>Chuyển khoản VietQR (MB Bank)</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: #64748b;">Tổng tiền thanh toán:</span>
                                    <strong style="color: #dc2626;">{{ format_price($bill->total_amount) }}</strong>
                                </div>
                            </div>
                        @endif

                        <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                            <a href="{{ route('courses.my_course', \Auth::id()) }}" class="btn btn-common" style="font-weight: 600; padding: 12px 26px; border-radius: 6px; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3);">
                                <i class="fa fa-graduation-cap"></i> Vào khóa học của tôi
                            </a>
                            <a href="{{ route('home') }}" class="btn" style="font-weight: 600; padding: 12px 24px; background: #ffffff; color: #1e293b; border: 2px solid #64748b; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#0f172a'; this.style.color='#0f172a';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#64748b'; this.style.color='#1e293b';">
                                <i class="fa fa-home"></i> Về trang chủ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('inline_scripts')
    <script type="text/javascript">
    </script>
@endsection
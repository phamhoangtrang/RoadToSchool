@extends('user.layouts.default')

@section('title')
    Xác thực Email
@endsection

@section('inline_styles')
@endsection

@section('content')
    <section id="content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="inner-box posting" style="margin: 40px auto; max-width: 850px;">
                        <div class="alert alert-danger alert-lg" role="alert" style="padding: 30px; border-radius: 8px;">
                            <h1 class="postin-title text-center" style="color: #fff; font-size: 26px; font-weight: bold; margin-bottom: 15px;">
                                <i class="fa fa-envelope-o"></i> Xác thực Email
                            </h1>
                            @if (session('resent'))
                                <div class="alert alert-success text-center" role="alert" style="background-color: #d4edda; color: #155724; border-color: #c3e6cb; margin-bottom: 15px; border-radius: 4px; padding: 10px;">
                                    Đường link xác thực mới đã được gửi đến địa chỉ email của bạn.
                                </div>
                            @endif
                            <div class="text-center" style="font-size: 16px; line-height: 1.6; color: #fff;">
                                Trước khi tiếp tục, vui lòng kiểm tra hộp thư email của bạn để lấy đường link xác thực tài khoản.
                            </div>
                            <div class="text-center" style="margin-top: 20px;">
                                <a href="https://mail.google.com" target="_blank" class="btn btn-common" style="background-color: #fff; color: #d9534f; font-weight: bold; border-radius: 4px; padding: 8px 24px; text-transform: none; font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                                    <i class="fa fa-external-link"></i> Mở hộp thư Gmail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('inline_scripts')
@endsection

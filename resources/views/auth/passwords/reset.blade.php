@extends('user.layouts.default')

@section('title')
    Đặt lại mật khẩu
@endsection

@section('inline_styles')
    <link rel="stylesheet" href="{{ asset('assets/css/custom/auth.css') }}">
@endsection

@section('content')
    <section class="auth-page">
        <div class="container">
            <div class="auth-card">
                <div class="auth-icon"><i class="fa fa-lock"></i></div>
                <h1 class="auth-title">Đặt lại mật khẩu</h1>
                <p class="auth-subtitle">Nhập mật khẩu mới cho tài khoản của bạn</p>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="auth-group">
                        <label class="auth-label" for="email">Địa chỉ Email</label>
                        <div class="auth-input-wrap @if($errors->has('email')) error-input @endif">
                            <i class="fa fa-envelope"></i>
                            <input id="email" type="email" class="form-control" name="email"
                                   value="{{ $email ?? old('email') }}" required autocomplete="email">
                        </div>
                        @if($errors->has('email'))<p class="auth-field-error">{{ $errors->first('email') }}</p>@endif
                    </div>

                    <div class="auth-group">
                        <label class="auth-label" for="password">Mật khẩu mới</label>
                        <div class="auth-input-wrap @if($errors->has('password')) error-input @endif">
                            <i class="fa fa-lock"></i>
                            <input id="password" type="password" class="form-control" name="password"
                                   placeholder="Tối thiểu 6 ký tự" required autofocus autocomplete="new-password">
                        </div>
                        @if($errors->has('password'))<p class="auth-field-error">{{ $errors->first('password') }}</p>@endif
                    </div>

                    <div class="auth-group">
                        <label class="auth-label" for="password-confirm">Xác nhận mật khẩu mới</label>
                        <div class="auth-input-wrap">
                            <i class="fa fa-lock"></i>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                                   placeholder="Nhập lại mật khẩu mới" required autocomplete="new-password">
                        </div>
                    </div>

                    <button type="submit" class="auth-submit">Đặt lại mật khẩu</button>
                </form>
            </div>
        </div>
    </section>
@endsection

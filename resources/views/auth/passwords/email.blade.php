@extends('user.layouts.default')

@section('title')
    Quên mật khẩu
@endsection

@section('inline_styles')
    <link rel="stylesheet" href="{{ asset('assets/css/custom/auth.css') }}">
@endsection

@section('content')
    <section class="auth-page">
        <div class="container">
            <div class="auth-card">
                <div class="auth-icon"><i class="fa fa-key"></i></div>
                <h1 class="auth-title">Quên mật khẩu?</h1>
                <p class="auth-subtitle">Nhập email đã đăng ký, chúng tôi sẽ gửi liên kết để bạn đặt lại mật khẩu</p>

                @if (session('status'))
                    <div class="auth-alert success">{{ session('status') }}</div>
                @endif
                @if ($errors->has('email'))
                    <div class="auth-alert">{{ $errors->first('email') }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="auth-group">
                        <label class="auth-label" for="email">Địa chỉ Email</label>
                        <div class="auth-input-wrap @if($errors->has('email')) error-input @endif">
                            <i class="fa fa-envelope"></i>
                            <input id="email" type="email" class="form-control" name="email"
                                   value="{{ old('email') }}" placeholder="name@example.com" required autofocus autocomplete="email">
                        </div>
                    </div>
                    <button type="submit" class="auth-submit">Gửi liên kết đặt lại mật khẩu</button>
                </form>

                <p class="auth-switch"><a href="{{ route('login') }}"><i class="fa fa-angle-left"></i> Quay lại đăng nhập</a></p>
            </div>
        </div>
    </section>
@endsection

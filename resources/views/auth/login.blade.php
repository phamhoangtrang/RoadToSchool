@extends('user.layouts.default')

@section('title')
    {{ __('titles.login') }}
@endsection

@section('inline_styles')
    <link rel="stylesheet" href="{{ asset('assets/css/custom/auth.css') }}">
@endsection

@section('content')
    <section class="auth-page">
        <div class="container">
            <div class="auth-card">
                <div class="auth-icon"><i class="fa fa-sign-in"></i></div>
                <h1 class="auth-title">Chào mừng trở lại</h1>
                <p class="auth-subtitle">Đăng nhập để tiếp tục học tập cùng Road To School</p>

                @if (session('status'))
                    <div class="auth-alert success">{{ session('status') }}</div>
                @endif
                @if (session('warning'))
                    <div class="auth-alert warning">{{ session('warning') }}</div>
                @endif
                @if(!$errors->isEmpty())
                    <div class="auth-alert" id="error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                {{ Form::open(['method' => 'post', 'class' => 'login-form']) }}
                <div class="auth-group">
                    <label class="auth-label" for="email">{{ __('titles.email') }}</label>
                    <div class="auth-input-wrap @if(!$errors->isEmpty()) error-input @endif">
                        <i class="fa fa-envelope"></i>
                        {{ Form::text('email', null, ['class' => 'form-control', 'id' => 'email', 'placeholder' => 'name@example.com', 'autocomplete' => 'email']) }}
                    </div>
                </div>
                <div class="auth-group">
                    <label class="auth-label" for="password">{{ __('titles.password') }}</label>
                    <div class="auth-input-wrap @if(!$errors->isEmpty()) error-input @endif">
                        <i class="fa fa-lock"></i>
                        {{ Form::password('password', ['class' => 'form-control', 'id' => 'password', 'placeholder' => 'Nhập mật khẩu', 'autocomplete' => 'current-password']) }}
                    </div>
                </div>
                <div class="auth-row-end">
                    <a href="{{ route('password.request') }}">{{ __('titles.forgot_password') }}</a>
                </div>
                {{ Form::submit(__('titles.login'), ['class' => 'auth-submit']) }}
                {{ Form::close() }}

                <p class="auth-switch">Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a></p>
            </div>
        </div>
    </section>
@endsection

@section('inline_scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#email').focus(function () {
                $('.auth-input-wrap').removeClass('error-input');
                $('#error').remove();
            });
            $('#password').focus(function () {
                $('.auth-input-wrap').removeClass('error-input');
                $('#error').remove();
            })
        })
    </script>
@endsection
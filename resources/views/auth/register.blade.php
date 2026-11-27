@extends('user.layouts.default')

@section('title')
    {{ __('titles.register') }}
@endsection

@section('inline_styles')
    <link rel="stylesheet" href="{{ asset('assets/css/custom/auth.css') }}">
@endsection

@section('content')
    <section class="auth-page">
        <div class="container">
            <div class="auth-card">
                <div class="auth-icon"><i class="fa fa-user-plus"></i></div>
                <h1 class="auth-title">Tạo tài khoản mới</h1>
                <p class="auth-subtitle">Đăng ký để bắt đầu hành trình học tập của bạn</p>

                {{ Form::open(['method' => 'post']) }}
                <div class="auth-group">
                    <label class="auth-label" for="name">Họ và tên</label>
                    <div class="auth-input-wrap @if($errors->has('name')) error-input @endif">
                        <i class="fa fa-user"></i>
                        {{ Form::text('name', null, ['class' => 'form-control', 'id' => 'name', 'placeholder' => 'Nhập họ và tên', 'autocomplete' => 'name']) }}
                    </div>
                    @if($errors->has('name'))<p class="auth-field-error">{{ $errors->first('name') }}</p>@endif
                </div>
                <div class="auth-group">
                    <label class="auth-label" for="email">Địa chỉ Email</label>
                    <div class="auth-input-wrap @if($errors->has('email')) error-input @endif">
                        <i class="fa fa-envelope"></i>
                        {{ Form::text('email', null, ['class' => 'form-control', 'id' => 'email', 'placeholder' => 'name@example.com', 'autocomplete' => 'email']) }}
                    </div>
                    @if($errors->has('email'))<p class="auth-field-error">{{ $errors->first('email') }}</p>@endif
                </div>
                <div class="auth-group">
                    <label class="auth-label" for="password">Mật khẩu</label>
                    <div class="auth-input-wrap @if($errors->has('password')) error-input @endif">
                        <i class="fa fa-lock"></i>
                        {{ Form::password('password', ['class' => 'form-control', 'id' => 'password', 'placeholder' => 'Tối thiểu 6 ký tự', 'autocomplete' => 'new-password']) }}
                    </div>
                    @if($errors->has('password'))<p class="auth-field-error">{{ $errors->first('password') }}</p>@endif
                </div>
                <div class="auth-group">
                    <label class="auth-label" for="password-confirmation">Xác nhận mật khẩu</label>
                    <div class="auth-input-wrap">
                        <i class="fa fa-lock"></i>
                        {{ Form::password('password_confirmation', ['class' => 'form-control', 'id' => 'password-confirmation', 'placeholder' => 'Nhập lại mật khẩu', 'autocomplete' => 'new-password']) }}
                    </div>
                </div>
                {{ Form::submit(__('titles.register'), ['class' => 'auth-submit', 'id' => 'submit']) }}
                {{ Form::close() }}

                <p class="auth-switch">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></p>
            </div>
        </div>
    </section>
@endsection

@section('inline_scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#name').focus(function () {
                $('#name').parent().removeClass('error-input');
                $('#name').closest('.auth-group').find('.auth-field-error').remove();
            });
            $('#email').focus(function () {
                $('#email').parent().removeClass('error-input');
                $('#email').closest('.auth-group').find('.auth-field-error').remove();
            });
            $('#password').focus(function () {
                $('#password').parent().removeClass('error-input');
                $('#password').closest('.auth-group').find('.auth-field-error').remove();
            });
        });
    </script>
@endsection
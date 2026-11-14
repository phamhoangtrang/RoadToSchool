@extends('user.layouts.default')

@section('title')
    {{ $selectedUser->name . ' - ' . __('titles.profile_info') }}
@endsection

@section('inline_styles')
    <link rel="stylesheet" href="{{ asset('assets/css/custom/users_show.css') }}">
    <style>
        /* Modern Profile Edit Page Styling */
        .page-content .inner-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
            padding: 24px 28px;
            margin-bottom: 25px;
        }

        .profile-welcome-header {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .profile-welcome-title {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .profile-welcome-title span.brand-text {
            color: #2563eb;
        }

        .panel-modern {
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-shadow: none !important;
            overflow: hidden;
            margin-bottom: 0 !important;
        }

        .panel-modern .panel-heading {
            background: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 14px 20px !important;
        }

        .panel-modern .panel-title {
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #1e293b !important;
        }

        .panel-modern .panel-title a {
            color: #1e293b !important;
            text-decoration: none !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .panel-modern .panel-body {
            padding: 26px 24px !important;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label.control-label {
            font-size: 13.5px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 7px;
            display: block;
        }

        .form-control {
            height: 44px;
            border-radius: 9px !important;
            border: 1.5px solid #cbd5e1 !important;
            box-shadow: none !important;
            font-size: 14px;
            color: #1e293b;
            padding: 10px 14px;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
        }

        .form-control[readonly] {
            background-color: #f8fafc !important;
            color: #64748b !important;
            border-color: #e2e8f0 !important;
            cursor: not-allowed;
        }

        /* 3-Column Address Selectors */
        .address-selectors-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .address-selectors-row {
                grid-template-columns: 1fr;
            }
        }

        .address-selectors-row select.form-control {
            width: 100% !important;
            background-color: #ffffff;
            cursor: pointer;
        }

        /* Avatar Modern Uploader Card */
        .avatar-uploader-card {
            background: #f8fafc;
            border: 1.5px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            margin-top: 6px;
        }

        .avatar-uploader-layout {
            display: flex;
            align-items: center;
            gap: 28px;
            flex-wrap: wrap;
        }

        .avatar-previews-group {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .avatar-preview-box {
            text-align: center;
        }

        .avatar-preview-box .avatar-box-title {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .avatar-circle-frame {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid #ffffff;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.1);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            position: relative;
        }

        .avatar-circle-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-empty-placeholder {
            font-size: 24px;
            color: #94a3b8;
        }

        .avatar-controls-group {
            flex: 1;
            min-width: 240px;
        }

        .avatar-file-input-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
        }

        .btn-choose-avatar {
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 18px;
            background: #2563eb;
            color: #ffffff !important;
            font-size: 13.5px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
            transition: all 0.2s ease;
            margin: 0;
            white-space: nowrap;
        }

        .btn-choose-avatar:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-choose-avatar input[type=file] {
            position: absolute;
            top: 0;
            right: 0;
            min-width: 100%;
            min-height: 100%;
            font-size: 100px;
            text-align: right;
            filter: alpha(opacity=0);
            opacity: 0;
            outline: none;
            cursor: inherit;
            display: block;
        }

        .avatar-filename-display {
            flex: 1;
            background: #ffffff !important;
            height: 40px;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            font-size: 13px;
            color: #64748b;
        }

        .avatar-actions-btns {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
        }

        .btn-avatar-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 15px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 7px;
            transition: all 0.2s ease;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .btn-delete-avatar {
            background: #ffffff;
            color: #ef4444;
            border-color: #fca5a5;
        }

        .btn-delete-avatar:hover {
            background: #fef2f2;
            color: #dc2626;
            border-color: #ef4444;
        }

        .btn-redo-avatar {
            background: #ffffff;
            color: #2563eb;
            border-color: #bfdbfe;
        }

        .btn-redo-avatar:hover {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #2563eb;
        }

        .avatar-tip-text {
            font-size: 12px;
            color: #64748b;
            margin-top: 8px;
            margin-bottom: 0;
        }

        .btn-submit-profile {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 34px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 600;
            border-radius: 9px;
            border: none !important;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
            transition: all 0.25s ease;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-submit-profile:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
        }
    </style>
@endsection

@section('content')
    {{ Breadcrumbs::render('users_show', $selectedUser) }}
    <div id="content">
        <div class="container">
            <div class="row">
                @include('user.partials.user_management_info')
                <div class="col-sm-9 page-content">
                    @include('flash::message')
                    <div class="inner-box">
                        <div class="profile-welcome-header">
                            <h3 class="profile-welcome-title">
                                <span>{{ __('titles.slogan_p1') }}</span>
                                <span class="brand-text">{{ __('titles.slogan_p2') }}</span>, {{ $selectedUser->name }}
                            </h3>
                        </div>
                        <div id="accordion" class="panel-group">
                            <div class="panel panel-default panel-modern">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a href="#collapseB1" data-toggle="collapse">
                                            <span><i class="fa fa-id-card-o" style="color: #2563eb; margin-right: 6px;"></i> {{ __('titles.profile_info') }}</span>
                                            <i class="fa fa-chevron-down" style="font-size: 13px; color: #94a3b8;"></i>
                                        </a>
                                    </h4>
                                </div>
                                <div class="panel-collapse collapse in" id="collapseB1">
                                    <div class="panel-body">
                                        @if (empty($errors))
                                            <p class="error-input-p-p"> {{ __('titles.update_info_fail') }} </p>
                                        @endif
                                        {{ Form::model($selectedUser, ['route' => ['users.update', $selectedUser->id], 'enctype' => 'multipart/form-data']) }}
                                        {{ Form::hidden('role', $selectedUser->role) }}
                                        <input name="_method" type="hidden" value="PUT">
                                        <div class="form-group">
                                            {{ Form::label('name', __('titles.name') . ' *', ['class' => 'control-label']) }}
                                            <div @if ($errors->has('name')) class="error-input" @endif>
                                                {{ Form::text('name', null, ['class' => 'form-control', 'id' => 'name']) }}
                                            </div>
                                            <p class="error-input-p"> {{ $errors->first('name')}} </p>
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('email', __('titles.email'), ['class' => 'control-label']) }}
                                            {{ Form::text('email', null, ['class' => 'form-control', 'id' => 'email', 'readonly']) }}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('address', __('titles.address') . ' *', ['class' => 'control-label']) }}
                                            {{ Form::text('address', null, ['class' => 'form-control', 'id' => 'address', 'readonly']) }}
                                            <div class="address-selectors-row">
                                                {{ Form::select('province', $selectProvince, null, ['class' => 'form-control', 'id' => 'province', 'placeholder' => __('titles.choose_province')]) }}
                                                {{ Form::select('district', [], null, ['class' => 'form-control', 'id' => 'district', 'placeholder' => __('titles.choose_district')]) }}
                                                {{ Form::select('commune', [], null, ['class' => 'form-control', 'id' => 'commune', 'placeholder' => __('titles.choose_commune')]) }}
                                            </div>
                                            <p class="error-input-p"> {{ $errors->first('address')}} </p>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">{{ __('titles.upload_avatar') }}</label>
                                            <div class="avatar-uploader-card">
                                                <div class="avatar-uploader-layout">
                                                    <div class="avatar-previews-group">
                                                        <div class="avatar-preview-box">
                                                            <span class="avatar-box-title">Ảnh hiện tại</span>
                                                            <div class="avatar-circle-frame">
                                                                <img id="current-img"
                                                                     src="{{ str_replace('public', '', asset($selectedUser->avatar)) }}"
                                                                     onerror="this.src='{{ asset('assets/img/default_avatar.png') }}';"
                                                                     alt="Avatar hiện tại"/>
                                                            </div>
                                                        </div>
                                                        <div class="avatar-preview-box">
                                                            <span class="avatar-box-title">Ảnh mới</span>
                                                            <div class="avatar-circle-frame">
                                                                <img id="img-upload" style="display: none;" alt="Ảnh mới"/>
                                                                <span class="avatar-empty-placeholder" id="new-avatar-placeholder">
                                                                    <i class="fa fa-user-circle-o"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="avatar-controls-group">
                                                        <div class="avatar-file-input-wrapper">
                                                            <span class="btn-choose-avatar" id="upload-avatar-btn">
                                                                <i class="fa fa-camera"></i> Chọn ảnh… 
                                                                <input type="file" id="imgInp" name="avatar" accept="image/*">
                                                            </span>
                                                            <input type="text" class="form-control avatar-filename-display" placeholder="Chưa chọn tệp nào" readonly>
                                                        </div>
                                                        <div class="avatar-actions-btns">
                                                            <button type="button" class="btn-avatar-action btn-redo-avatar" id="button-redo-avatar" style="display: none">
                                                                <i class="fa fa-undo"></i> Khôi phục ảnh
                                                            </button>
                                                            <button type="button" class="btn-avatar-action btn-delete-avatar" id="button-delete-avatar">
                                                                <i class="fa fa-trash-o"></i> {{ __('titles.delete_avatar') }}
                                                            </button>
                                                        </div>
                                                        <p class="avatar-tip-text">
                                                            <i class="fa fa-info-circle"></i> Định dạng: JPG, PNG, GIF. Kích thước tối đa 2MB.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('personal_info', __('titles.detail'), ['class' => 'control-label']) }}
                                            {{ Form::textarea('personal_info', null, ['class' => 'form-control', 'id' => 'personal-info', 'rows' => 4, 'cols' => 30, 'style' => 'height: auto; border-radius: 9px !important;']) }}
                                        </div>
                                        @if ($selectedUser->role == 1)
                                            <div class="form-group">
                                                {{ Form::label('working_place' . ' *', __('titles.working_place'), ['class' => 'control-label']) }}
                                                <div @if ($errors->has('working_place')) class="error-input" @endif>
                                                    {{ Form::text('working_place', null, ['class' => 'form-control', 'id' => 'working_place', 'readonly' => '']) }}
                                                </div>
                                                <div class="address-selectors-row">
                                                    {{ Form::select('province', $selectProvince, null, ['class' => 'form-control', 'id' => 'province', 'placeholder' => __('titles.choose_province')]) }}
                                                    {{ Form::select('district', [], null, ['class' => 'form-control', 'id' => 'district', 'placeholder' => __('titles.choose_district')]) }}
                                                    {{ Form::select('commune', [], null, ['class' => 'form-control', 'id' => 'commune', 'placeholder' => __('titles.choose_commune')]) }}
                                                </div>
                                                <p class="error-input-p"> {{ $errors->first('working_place')}} </p>
                                            </div>
                                        @endif
                                        <div class="form-group">
                                            {{ Form::label('grade', __('titles.grade'), ['class' => 'control-label']) }}
                                            <div @if ($errors->has('grade')) class="error-input" @endif>
                                                {{ Form::text('grade', null, ['class' => 'form-control', 'id' => 'grade', 'readonly' => '']) }}
                                            </div>
                                            {{ Form::select('grade', \App\Models\User::$grades, null, ['class' => 'form-control fix-select', 'id' => 'select-grade', 'placeholder' => __('titles.choose_grade'), 'style' => 'margin-top: 10px;']) }}
                                            <p class="error-input-p"> {{ $errors->first('grade')}} </p>
                                        </div>
                                        <button type="submit" class="btn-submit-profile" name="update_info">
                                            <i class="fa fa-check"></i> {{ __('titles.update') }}
                                        </button>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('inline_scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#name').focus(function () {
                $('#name').parent().removeClass('error-input');
                $('#name').parent().next().remove();
            });
            $('#province').focus(function () {
                $('#working_place').parent().removeClass('error-input');
                $('#working_place').parent().next().next().remove();
            });
            $('#select-grade').focus(function () {
                $('#grade').parent().removeClass('error-input');
                $('#grade').parent().next().next().remove();
            });
            $('#old-password').focus(function () {
                $('#old-password').parent().removeClass('error-input');
                $('#old-password').parent().next().remove();
            });
            $('#new-password').focus(function () {
                $('#new-password').parent().removeClass('error-input');
                $('#new-password').parent().next().remove();
            });
            $('#password-confirmation').focus(function () {
                $('#password-confirmation').parent().removeClass('error-input');
                $('#password-confirmation').parent().next().remove();
            });
            $('#personal-page').addClass('active');
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#current-avatar').attr('src', e.target.result);
                    $('#cancel').fadeIn(1000);
                };
                reader.readAsDataURL(input.files[0]);
                $('#cancel-value').attr('disabled', '');
                $('#delete-value').attr('disabled', '');
            }
        }

        function backAvatar() {
            $('#current-avatar').attr('src', '{{ str_replace("public/", "", asset($selectedUser->avatar)) }}');
            $('#cancel-value').removeAttr('disabled');
            $('#delete-value').attr('disabled', '');
            $('#cancel').fadeOut(1000);
        }

        function deleteAvatar() {
            $('#current-avatar').attr('src', '{{ str_replace('public/', '', asset('assets/img/basic_avatar.png')) }}');
            $('#cancel').fadeIn(1000);
            $('#cancel-value').attr('disabled', '');
            $('#delete-value').removeAttr('disabled');
        }

        $('select#province').change(function () {
            var provinceId = $(this).val();
            $('#district').children().fadeOut();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post(
                "/districts",
                {provinceId: provinceId},
                function (result) {
                    $.each(result, function (index, element) {
                        $("select#district").append('<option value="' + element.id + '">' + element.name + '</option>');
                    });
                },
                "json",
            );
        });
        $('select#district').change(function () {
            var districtId = $(this).val();
            $('#commune').children().fadeOut();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post(
                "/communes",
                {districtId: districtId},
                function (result) {
                    $.each(result, function (index, element) {
                        $("select#commune").append('<option value="' + element.id + '">' + element.name + '</option>');
                    });
                },
                "json",
            );
        });
        $('select#commune').change(function () {
            var province = $('#province option:selected').text();
            var district = $('#district option:selected').text();
            var commune = $('#commune option:selected').text();
            var address = province + ' province, ' + district + ' district, ' + commune + ' commune.';
            $('#address').val(address);
        });
        $('select#select-grade').change(function () {
            $('#grade').val($('#select-grade option:selected').text());
        });

        $(document).on('change', '#upload-avatar-btn :file', function () {
            var input = $(this),
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [label]);
        });

        $('#upload-avatar-btn :file').on('fileselect', function (event, label) {
            var input = $(this).closest('.avatar-file-input-wrapper').find(':text'),
                log = label;

            if (input.length) {
                input.val(log);
            } else {
                if (log) alert(log);
            }
        });

        $('#button-redo-avatar').on('click', function (e) {
            e.preventDefault();
            $('#img-upload').attr('src', '').hide();
            $('#new-avatar-placeholder').show();
            $('.avatar-filename-display').val('');
            $('#imgInp').val('');
            $('#button-redo-avatar').fadeOut(300);
            $('#button-delete-avatar').fadeIn(300);
        });

        $('#button-delete-avatar').on('click', function (e) {
            e.preventDefault();
            $('#img-upload').attr('src', '{{ asset("images/default_avatar/default_avatar.jpg") }}').show();
            $('#new-avatar-placeholder').hide();
            $('#button-delete-avatar').fadeOut(300);
            $('#button-redo-avatar').fadeIn(300);
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#img-upload').attr('src', e.target.result).show();
                    $('#new-avatar-placeholder').hide();
                }

                reader.readAsDataURL(input.files[0]);
                $('#button-redo-avatar').fadeIn(300);
            }
        }

        $("#imgInp").change(function () {
            readURL(this);
        });
    </script>
@endsection

@extends('admin.layouts.default')

@section('title')
    {{ __('titles.permission_management') }}
@endsection

@section('inline_styles')
    <!-- page css -->
    <link href="{{ asset('assets/admin/vendor/selectize/dist/css/selectize.default.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/vendor/summernote/dist/summernote-bs4.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/vendor/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css') }}"
          rel="stylesheet">
    <style>
        .perm-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            background: #fff;
            margin-bottom: 24px;
        }
        .perm-filter-label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .user-profile-banner {
            background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            margin-bottom: 24px;
        }
        .user-avatar-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .floating-permission-bar {
            position: fixed;
            bottom: 24px;
            right: 32px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 12px 24px;
            border-radius: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid #e2e8f0;
            z-index: 999;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn-perm-action {
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            padding: 8px 18px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .btn-perm-primary {
            background-color: #2563eb;
            color: #fff;
            border: none;
        }
        .btn-perm-primary:hover {
            background-color: #1d4ed8;
            color: #fff;
            transform: translateY(-1px);
        }
    </style>
@endsection

@section('content')
    <!-- Content Wrapper START -->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="page-header m-b-20">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="header-title" style="font-weight: 700; color: #1e293b;">{{ __('titles.permission_management') }}</h2>
                        <div class="header-sub-title">
                            <nav class="breadcrumb breadcrumb-dash" style="background: transparent; padding: 0;">
                                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item" style="color: #64748b;">
                                    <i class="ti-home p-r-5"></i>{{ __('titles.r2s_dashboard') }}
                                </a>
                                <span class="breadcrumb-item active" style="color: #334155; font-weight: 500;">
                                    Phân quyền người dùng
                                </span>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Select User -->
            <div class="card perm-card" id="card-select-user">
                <div class="card-header border-bottom p-3 d-flex align-items-center" style="background: #f8fafc;">
                    <div class="m-r-10" style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                        <i class="ti-user"></i>
                    </div>
                    <h5 class="card-title m-b-0" style="font-weight: 700; color: #1e293b; font-size: 15px;">Chọn tài khoản để phân quyền</h5>
                </div>
                <div class="card-body p-4">
                    <div id="error-submit" class="alert alert-danger" style="display: none; border-radius: 8px;">
                        <i class="ti-alert m-r-5"></i> Vui lòng chọn người dùng trước khi thực hiện xem quyền!
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="perm-filter-label">
                                <i class="ti-id-badge text-primary"></i> 1. Chọn nhóm vai trò
                            </label>
                            <select id="selectize-dropdown">
                                <option value="" disabled selected>Tất cả nhóm người dùng...</option>
                                @foreach($userGroupList as $key => $value)
                                    <option value="{{ $key }}" {{ (array_key_exists('permissionGroup', $dataParamList) && $dataParamList['permissionGroup'] == $key) ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="perm-filter-label">
                                <i class="ti-email text-primary"></i> 2. Tìm nhanh theo Email
                            </label>
                            <select id="selectize-dropdown-2">
                                <option value="" disabled selected>Nhập hoặc chọn email...</option>
                                @foreach($emailUserList as $key => $value)
                                    <option value="{{ $key }}" {{ (array_key_exists('userKey', $dataParamList) && $dataParamList['userKey'] == $key) ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="perm-filter-label">
                                <i class="ti-user text-primary"></i> 3. Tìm theo Họ và tên
                            </label>
                            <select id="selectize-group">
                                <option value="">Chọn tên người dùng...</option>
                                @if(!array_key_exists('permissionGroup', $dataParamList) || (array_key_exists('permissionGroup', $dataParamList) && $dataParamList['permissionGroup'] == 2))
                                    <optgroup label="Học viên" id="opt-student">
                                        @foreach($studentList as $student)
                                            <option value="{{ $student->id }}"
                                                    {{ (array_key_exists('userKey', $dataParamList) && $dataParamList['userKey'] == $student->id) ? 'selected' : '' }} id="student-{{ $student->id }}">{{ $student->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if(!array_key_exists('permissionGroup', $dataParamList) || (array_key_exists('permissionGroup', $dataParamList) && $dataParamList['permissionGroup'] == 1))
                                    <optgroup label="Giảng viên" id="opt-instructor">
                                        @foreach($instructorList as $instructor)
                                            <option value="{{ $instructor->id }}" {{ (array_key_exists('userKey', $dataParamList) && $dataParamList['userKey'] == $instructor->id) ? 'selected' : '' }}>{{ $instructor->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if(!array_key_exists('permissionGroup', $dataParamList) || (array_key_exists('permissionGroup', $dataParamList) && $dataParamList['permissionGroup'] == 0))
                                    <optgroup label="Quản trị viên" id="opt-admin">
                                        @foreach($adminList as $admin)
                                            <option value="{{ $admin->id }}" {{ (array_key_exists('userKey', $dataParamList) && $dataParamList['userKey'] == $admin->id) ? 'selected' : '' }}>{{ $admin->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row m-t-20">
                        <div class="col-12 text-right">
                            <button class="btn btn-default btn-perm-action m-r-10" id="btn-reset" style="background: #f1f5f9; border-color: #e2e8f0; color: #475569;">
                                <i class="ti-reload"></i> Nhập lại
                            </button>
                            <button class="btn btn-info btn-perm-action btn-perm-primary" id="btn-get-permission">
                                <i class="ti-key"></i> Xem quyền hạn tài khoản
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card User Info Banner -->
            <div class="user-profile-banner" id="card-user-info" style="display: none">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center m-b-10">
                        <img class="user-avatar-circle m-r-20" id="user-avatar" src="" alt="Avatar"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=User&background=2563eb&color=fff&size=100';">
                        <div>
                            <div class="d-flex align-items-center gap-2 m-b-5">
                                <h4 class="m-b-0" id="user-name" style="font-weight: 700; color: #1e293b;"></h4>
                                <span class="badge badge-primary m-l-10" id="card-title-user-info" style="border-radius: 20px; font-size: 11px; padding: 4px 10px;">Phân quyền</span>
                            </div>
                            <p class="text-muted m-b-0" id="user-email" style="font-size: 14px;"></p>
                        </div>
                    </div>
                    <div class="m-b-10">
                        <a href="#" class="btn btn-perm-action btn-perm-primary m-r-10" id="edit-permission-button">
                            <i class="ti-pencil-alt"></i> Chỉnh sửa phân quyền
                        </a>
                        <a href="#" class="btn btn-default btn-perm-action" id="select-another-user-button" style="background: #fff; border: 1px solid #cbd5e1; color: #475569;">
                            <i class="ti-exchange-vertical"></i> Chọn người dùng khác
                        </a>
                    </div>
                </div>
            </div>

            <!-- Permission Groups List -->
            @include('admin.permissions.partials.permission_list')

            <!-- Floating Save / Reset Controls -->
            <div id="permission-control" class="floating-permission-bar" style="display: none;">
                <span style="font-weight: 600; color: #1e293b; font-size: 14px; margin-right: 8px;">
                    <i class="ti-lock text-primary"></i> Đang chỉnh sửa quyền:
                </span>
                <button class="btn btn-info btn-perm-action btn-perm-primary" id="btn-update-permission">
                    <i class="ti-check"></i> Lưu phân quyền
                </button>
                <button class="btn btn-warning btn-perm-action" id="btn-reset-permission" style="background: #fffbeb; border: 1px solid #fde68a; color: #d97706;">
                    <i class="ti-reload"></i> Khôi phục ban đầu
                </button>
            </div>
        </div>
    </div>
    <!-- Content Wrapper END -->
@endsection

@include('admin.layouts.delete_modal')

@section('inline_scripts')
    <script src="{{ asset('assets/admin/vendor/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/selectize/dist/js/standalone/selectize.min.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/js/forms/form-elements.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#btn-reset').on('click', function (event) {
                event.preventDefault();
                var selectUserGroup = $('#selectize-dropdown').selectize();
                var controlUserGroup = selectUserGroup[0].selectize;
                controlUserGroup.clear();
                var selectUserEmail = $('#selectize-dropdown-2').selectize();
                var controlUserEmail = selectUserEmail[0].selectize;
                controlUserEmail.clear();
                var selectUserName = $('#selectize-group').selectize();
                var controlUserName = selectUserName[0].selectize;
                controlUserName.clear();
                window.location.replace('/admin/permissions');
            });

            // Change select box follow user group
            $('#selectize-dropdown').on('change', function (event) {
                event.preventDefault();
                var groupKey = $(this).val();
                if (groupKey == 0) {
                    window.location.replace('/admin/permissions?permissionGroup=0');
                } else if (groupKey == 1) {
                    window.location.replace('/admin/permissions?permissionGroup=1');
                } else if (groupKey == 2) {
                    window.location.replace('/admin/permissions?permissionGroup=2');
                }
            });

            // Change select box follow user email or name
            $('#selectize-dropdown-2, #selectize-group').on('change', function (event) {
                event.preventDefault();
                var userKey = $(this).val();
                if (userKey) {
                    window.location.replace('/admin/permissions?userKey=' + userKey);
                }
            });

            var selectedUserId = 0;
            var userPermissionList = [];

            // Get permission
            $('#btn-get-permission').on('click', function (event) {
                event.preventDefault();
                var userId = $('#selectize-group').val();
                if (!userId) {
                    $('#error-submit').slideDown(250);
                } else {
                    $('#error-submit').slideUp(200);
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: '/admin/permissions/getPermission/' + userId,
                        type: 'post',
                        success: function (response) {
                            var responseData = jQuery.parseJSON(response);
                            var permissionList = responseData.permissionList;
                            userPermissionList = permissionList;
                            var selectedUser = responseData.selectedUser;

                            $('.permission-checkbox').prop('checked', false);
                            permissionList.forEach(function (element) {
                                $('#switch-' + element).prop('checked', true);
                            });

                            $.notify({
                                message: 'Đã tải thành công danh sách quyền hạn của <b>' + selectedUser.name + '</b>',
                            }, {
                                type: 'info',
                                placement: {
                                    from: "bottom",
                                    align: "right"
                                },
                                delay: 2000,
                            });

                            $('#user-name').text(selectedUser.name);
                            $('#user-email').text(selectedUser.email);

                            var userAvatar = selectedUser.avatar;
                            if (userAvatar) {
                                if (userAvatar.includes("public")) {
                                    userAvatar = userAvatar.replace("public/images/", "/images/");
                                } else if (!userAvatar.startsWith("http")) {
                                    userAvatar = "/" + userAvatar;
                                }
                            } else {
                                userAvatar = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(selectedUser.name) + '&background=2563eb&color=fff&size=100';
                            }
                            $('#user-avatar').attr('src', userAvatar);
                            $('#card-title-user-info').text('Tài khoản #' + selectedUser.id);
                            selectedUserId = selectedUser.id;
                            $('#card-user-info').fadeIn(300);

                            if (responseData.allowUpdate == 0) {
                                $('#edit-permission-button').hide();
                            } else {
                                $('#edit-permission-button').show();
                            }
                            $('#card-select-user').hide();
                        }
                    });
                }
            });

            $('#edit-permission-button').on('click', function (event) {
                event.preventDefault();
                $('#permission-control').fadeIn(300);
                $('.permission-checkbox').removeAttr('disabled');
                $.notify({
                    message: 'Chế độ chỉnh sửa phân quyền đã được kích hoạt.',
                }, {
                    type: 'info',
                    placement: {
                        from: "bottom",
                        align: "right"
                    },
                    delay: 2000,
                });
                $(this).fadeOut(300);
            });

            $('#btn-reset-permission').on('click', function (event) {
                event.preventDefault();
                $('.permission-checkbox').prop("checked", false);
                userPermissionList.forEach(function (element) {
                    $('#switch-' + element).prop("checked", true);
                });
            });

            $('#btn-update-permission').on('click', function (event) {
                event.preventDefault();
                var checkedPermissionList = [];
                $('.permission-checkbox').each(function () {
                    var permissionIdTemp = $(this).attr('id').replace('switch-', '');
                    if ($(this).prop('checked')) {
                        checkedPermissionList.push(permissionIdTemp);
                    }
                });

                $.ajax({
                    url: '/admin/permissions/updatePermission/' + selectedUserId,
                    type: 'post',
                    data: {
                        checkedPermissionList: checkedPermissionList
                    },
                    success: function (response) {
                        $('#permission-control').fadeOut(300);
                        var responseData = jQuery.parseJSON(response);

                        $.notify({
                            message: 'Đã cập nhật phân quyền thành công cho <b>' + $('#user-name').text() + '</b>!',
                        }, {
                            type: 'success',
                            placement: {
                                from: "bottom",
                                align: "right"
                            },
                            delay: 2000,
                        });

                        $('.permission-checkbox').attr('disabled', '');
                        $('#edit-permission-button').fadeIn(300);
                        $('.permission-checkbox').prop("checked", false);
                        if (responseData != 200 && Array.isArray(responseData)) {
                            responseData.forEach(function (element) {
                                $('#switch-' + element).prop("checked", true);
                            });
                        }
                    }
                });
            });

            $('#select-another-user-button').on('click', function (event) {
                event.preventDefault();
                window.location.replace('/admin/permissions');
            });
        });
    </script>
@endsection
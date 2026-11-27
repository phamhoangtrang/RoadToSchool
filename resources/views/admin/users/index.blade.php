@extends('admin.layouts.default')

@section('title')
    {{ __('titles.all_users') }}
@endsection

@section('inline_styles')
    <link rel="stylesheet"
          href="{{ asset('assets/admin/vendor/datatables/media/css/dataTables.bootstrap4.min.css') }}"/>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css"/>
    <style>
        .user-stat-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }
        .user-stat-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }
        .user-stat-num {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
        }
        .user-avatar-img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e2e8f0;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .action-btn-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #475569;
            transition: all 0.2s ease;
            text-decoration: none !important;
            margin: 0 3px;
            font-size: 13px;
        }
        .action-btn-circle:hover {
            transform: translateY(-2px);
        }
        .action-btn-circle.view:hover { background: #eff6ff; color: #2563eb; border-color: #93c5fd; }
        .action-btn-circle.edit:hover { background: #fefce8; color: #ca8a04; border-color: #fde047; }
        .action-btn-circle.delete:hover { background: #fef2f2; color: #dc2626; border-color: #fca5a5; }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }
        .role-badge.admin { background: #fee2e2; color: #b91c1c; }
        .role-badge.instructor { background: #eff6ff; color: #1d4ed8; }
        .role-badge.student { background: #e0f2fe; color: #0369a1; }
    </style>
@endsection

@section('content')
    <div class="message-display">
        @include('flash::message')
    </div>
    <div class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header m-b-20" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div>
                    <h2 class="header-title" style="margin-bottom: 4px;">{{ __('titles.all_users') }}</h2>
                    <div class="header-sub-title">
                        <nav class="breadcrumb breadcrumb-dash">
                            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">
                                <i class="ti-home p-r-5"></i>{{ __('titles.r2s_dashboard') }}
                            </a>
                            <a class="breadcrumb-item active">{{ __('titles.users') }}</a>
                            <span class="breadcrumb-item active">{{ __('titles.all_users') }}</span>
                        </nav>
                    </div>
                </div>
                <div>
                    <a href="{{ route('admin.users.create_instructor') }}" class="btn btn-primary btn-sm" style="border-radius: 8px; font-weight: 600; padding: 8px 16px;">
                        <i class="fa fa-user-plus m-r-5"></i> Thêm giảng viên mới
                    </a>
                </div>
            </div>

            <!-- 4 Quick Stat Counters -->
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="user-stat-card">
                        <div>
                            <div class="user-stat-label">Tổng người dùng</div>
                            <div class="user-stat-num">{{ $users->count() }}</div>
                        </div>
                        <div style="font-size: 24px; color: #2563eb;"><i class="fa fa-users"></i></div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="user-stat-card">
                        <div>
                            <div class="user-stat-label">Quản trị viên</div>
                            <div class="user-stat-num text-danger">{{ $users->where('is_admin', 1)->count() }}</div>
                        </div>
                        <div style="font-size: 24px; color: #dc2626;"><i class="fa fa-shield"></i></div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="user-stat-card">
                        <div>
                            <div class="user-stat-label">Giảng viên</div>
                            <div class="user-stat-num text-primary">{{ $users->where('role', 1)->count() }}</div>
                        </div>
                        <div style="font-size: 24px; color: #1d4ed8;"><i class="fa fa-graduation-cap"></i></div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="user-stat-card">
                        <div>
                            <div class="user-stat-label">Học viên</div>
                            <div class="user-stat-num text-info">{{ $users->where('role', 2)->count() }}</div>
                        </div>
                        <div style="font-size: 24px; color: #0284c7;"><i class="fa fa-book"></i></div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card" style="border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);">
                <div class="card-body">
                    <div class="table-overflow">
                        <table id="dt-opt" class="table table-hover table-xl">
                            <thead>
                            <tr>
                                <th style="width: 8%;">ID</th>
                                <th style="width: 32%;">{{ __('titles.name') }}</th>
                                <th style="width: 28%;">{{ __('titles.email') }}</th>
                                <th style="width: 18%;">{{ __('titles.role') }}</th>
                                <th style="width: 14%; text-align: center;">{{ __('titles.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($users as $user)
                                <tr id="user{{ $user->id }}">
                                    <td><b class="text-muted">#{{ $user->id }}</b></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $avatar = $user->avatar
                                                    ? (strpos($user->avatar, 'http') === 0 ? $user->avatar : asset(str_replace('public/', '', $user->avatar)))
                                                    : asset('images/avatar/basic-avatar.png');
                                            @endphp
                                            <img class="user-avatar-img" src="{{ $avatar }}" alt="" onerror="this.src='{{ asset('images/avatar/basic-avatar.png') }}'">
                                            <div>
                                                <a href="{{ route('admin.users.show', $user->id) }}" class="font-weight-bold text-dark d-block">
                                                    {{ $user->name }}
                                                </a>
                                                <small class="text-muted">{{ $user->phone ?: 'Chưa cập nhật SĐT' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark">{{ $user->email }}</span>
                                    </td>
                                    <td>
                                        @if($user->is_admin == 1)
                                            <span class="role-badge admin"><i class="fa fa-shield"></i> Quản trị viên</span>
                                        @else
                                            @if($user->role === 1)
                                                <span class="role-badge instructor"><i class="fa fa-graduation-cap"></i> Giảng viên</span>
                                            @elseif ($user->role === 2)
                                                <span class="role-badge student"><i class="fa fa-user"></i> Học viên</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center font-size-16" style="white-space: nowrap;">
                                        <a class="action-btn-circle view" data-toggle="modal" title="Xem chi tiết"
                                           data-target="#detail-modal"
                                           data-url="{{ route('admin.users.show', $user->id) }}">
                                            <i class="ti-eye"></i>
                                        </a>
                                        <a class="action-btn-circle edit edit-user" data-user="{{ $user }}" data-toggle="modal" title="Chỉnh sửa"
                                           data-target="#edit-modal">
                                            <i class="ti-pencil"></i>
                                        </a>
                                        @if(Auth::user()->id !== $user->id)
                                            <a class="action-btn-circle delete" data-toggle="modal" title="Xóa người dùng"
                                               data-target="#delete-modal"
                                               data-url="{{ route('admin.users.destroy', $user->id) }}">
                                                <i class="ti-trash"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('admin.users.partials.edit_modal')
@include('admin.layouts.delete_modal')

@section('inline_scripts')
    <script src="{{ asset('assets/admin/vendor/datatables/media/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/datatables/media/js/dataTables.bootstrap4.min.js') }}"></script>
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>
    <script src="{{ asset('assets/admin/js/tables/data-table.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.datepicker').datepicker();

            $('#edit-modal').on('show.bs.modal', function (e) {
                var url = $(e.relatedTarget).data('url');
                $('#form-edit').attr('action', url);
                var user = $(this).data('user')
            });

            $('.edit-user').on('click', function () {
                var user = $(this).data('user')
                $('#m-email').val(user.email);
                $('#m-name').val(user.name);
                $('#m-phone').val(user.phone);
                $('#m-birthday').val(user.birthday);
                $('#m-address').val(user.address);
                $('#m-personal-info').val(user.personal_info);
                $('#m-working-place').val(user.working_place);
                $('#m-grade').val(user.grade);
                $('#m-role').val(user.role);
            });

            $('#update-user-info').on('click', function () {
                var id = $(this).data('id');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: '/admin/users/' + id + '/updateUser',
                    data: {
                        id: id,
                        working_place: $('#m-working-place').val(),
                        phone: $('#m-phone').val(),
                        birthday: $('#m-birthday').val(),
                        address: $('#m-address').val(),
                        role: $('#m-role').val()
                    },
                    success: function (data) {
                        $('#edit-profile-modal').modal('hide');
                        $.notify({
                            // options
                            message: ' {{ __('titles.update_user_success') }} '
                        }, {
                            // settings
                            type: 'success',
                            placement: {
                                from: "bottom",
                                align: "right"
                            },
                        });
                        $('#working-place').next().text($('#m-working-place').val());
                        $('#phone').next().text($('#m-phone').val());
                        $('#birthday').next().text($('#m-birthday').val());
                        $('#address').next().text($('#m-address').val());
                        $('#m-email').val(data.email);
                        $('#m-name').val(data.name);
                        $('#m-working-place').val(data.working_place);
                        $('#m-phone').val(data.phone);
                        $('#m-birthday').val(data.birthday);
                        $('#m-address').val(data.address);
                        $('#m-role').val(data.role);
                        $('#edit-modal').modal('toggle');
                        $("#dt-opt").load(" #dt-opt");
                        flag = 1;
                    },
                    error: function (response) {
                        errors = response.responseJSON.errors;
                        if (errors.working_place != null) {
                            $('#error-m-working-place').removeAttr('hidden');
                            $('#error-m-working-place').text(errors.working_place);
                            $('#m-working-place').addClass('error-input');
                        } else {
                            $('#error-m-working-place').attr('hidden', '');
                            $('#m-working-place').removeClass('error-input');
                        }
                        if (errors.phone != null) {
                            $('#error-m-phone').removeAttr('hidden');
                            $('#error-m-phone').text(errors.phone);
                            $('#m-phone').addClass('error-input');
                        } else {
                            $('#error-m-phone').attr('hidden', '');
                            $('#m-phone').removeClass('error-input');
                        }
                        if (errors.birthday != null) {
                            $('#error-m-birthday').removeAttr('hidden');
                            $('#error-m-birthday').text(errors.birthday);
                            $('#m-birthday').addClass('error-input');
                        } else {
                            $('#error-m-birthday').attr('hidden', '');
                            $('#m-birthday').removeClass('error-input');
                        }
                        if (errors.address != null) {
                            $('#error-m-address').removeAttr('hidden');
                            $('#error-m-working-place').text(errors.address);
                            $('#m-address').addClass('error-input');
                        } else {
                            $('#error-m-address').attr('hidden', '');
                            $('#m-address').removeClass('error-input');
                        }
                    }
                }, 'json');
            })

            $('#delete-modal').on('show.bs.modal', function (e) {
                var url = $(e.relatedTarget).data('url');
                $('#form-delete').attr('action', url);
            });

            $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
        })
    </script>
@endsection

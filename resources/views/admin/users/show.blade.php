@extends('admin.layouts.default')

@section('title')
    {{ $user->name }} - User Detail
@endsection

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <h2 class="header-title">{{ $user->name }}</h2>
                <div class="header-sub-title">
                    <nav class="breadcrumb breadcrumb-dash">
                        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">
                            <i class="ti-home p-r-5"></i>{{ __('titles.r2s_dashboard') }}
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="breadcrumb-item">{{ __('titles.users') }}</a>
                        <span class="breadcrumb-item active">{{ $user->name }}</span>
                    </nav>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <img src="{{ asset($user->avatar ?? 'assets/img/avatar_users/default_avatar.png') }}" 
                                 class="img-fluid rounded-circle m-b-20" style="max-width: 150px; max-height: 150px;" alt="{{ $user->name }}">
                            <h4>{{ $user->name }}</h4>
                            <p class="text-muted">
                                @if($user->is_admin == 1)
                                    <span class="badge badge-danger">Admin</span>
                                @elseif($user->role == 1)
                                    <span class="badge badge-success">Instructor</span>
                                @else
                                    <span class="badge badge-info">Student</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-9">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%;">Email</th>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone</th>
                                            <td>{{ $user->phone ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Birthday</th>
                                            <td>{{ $user->birthday ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td>{{ $user->address ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Working Place</th>
                                            <td>{{ $user->working_place ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Personal Info</th>
                                            <td>{!! $user->personal_info ?? 'N/A' !!}</td>
                                        </tr>
                                        @if($user->role == 1)
                                        <tr>
                                            <th>Total Students</th>
                                            <td>{{ $countStudent }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="m-t-20">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                    <i class="ti-arrow-left"></i> Back to Users
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

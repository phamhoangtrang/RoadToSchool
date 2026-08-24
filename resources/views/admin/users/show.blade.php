@extends('admin.layouts.default')

@section('title')
    User Details
@endsection

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <h2 class="header-title">User Details</h2>
                <div class="header-sub-title">
                    <nav class="breadcrumb breadcrumb-dash">
                        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">
                            <i class="ti-home p-r-5"></i>Dashboard
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="breadcrumb-item">Users</a>
                        <span class="breadcrumb-item active">{{ $user->name }}</span>
                    </nav>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <img src="{{ str_replace('public/', '', asset($user->avatar)) }}"
                                 alt="{{ $user->name }}" class="img-fluid rounded-circle">
                        </div>
                        <div class="col-md-9">
                            <h3>{{ $user->name }}</h3>
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                            <p><strong>Role:</strong> {{ $user->is_admin ? 'Admin' : (\App\Models\User::$roles[$user->role] ?? 'Unknown') }}</p>
                            <p><strong>Phone:</strong> {{ $user->phone ?: 'Not provided' }}</p>
                            <p><strong>Address:</strong> {{ $user->address ?: 'Not provided' }}</p>
                            <p><strong>Working place:</strong> {{ $user->working_place ?: 'Not provided' }}</p>
                            @if((int) $user->role === \App\Models\User::ROLE_TEACHER)
                                <p><strong>Courses:</strong> {{ $user->courses()->count() }}</p>
                                <p><strong>Students:</strong> {{ $countStudent }}</p>
                            @endif
                            <p><strong>About:</strong> {{ $user->personal_info ?: 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

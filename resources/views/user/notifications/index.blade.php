@extends('user.layouts.default')

@section('title')
    {{ __('titles.notification') }}
@endsection

@section('inline_styles')
    <style>
        .notification-table {
            margin-top: 15px;
            background: #fff;
        }
        .notification-table th,
        .notification-table td {
            vertical-align: middle !important;
        }
        .notification-table thead th {
            background-color: #f8fafc;
            color: #31708f;
            font-size: 14px;
            font-weight: 600;
            padding: 12px 10px !important;
            border-bottom: 2px solid #e2e8f0;
        }
        .notification-table tbody td {
            padding: 12px 10px !important;
        }
        .noti-unread {
            background-color: #edf5fd !important;
        }
        .noti-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto;
            border: 1px solid #e2e8f0;
        }
        .noti-content {
            font-size: 14px;
            color: #333;
        }
        .noti-content p {
            margin: 0 0 4px 0;
            line-height: 1.4;
        }
        .noti-time {
            color: #64748b;
            font-size: 12px;
            margin-top: 4px;
        }
    </style>
@endsection

@section('content')
    <div id="content">
        <div class="container">
            <div class="row">
                @include('user.partials.user_management_info')
                <div class="col-sm-9 page-content">
                    <div class="inner-box">
                        <h2 class="title-2"><i class="fa fa-bell"></i> {{ __('titles.notification') }} </h2>
                        @if(!$notificationList->count())
                            <p class="text-center">Bạn chưa có thông báo nào.</p>
                        @else
                            <div class="table-responsive">
                                <div class="table-action">
                                    @php
                                        $currentPage = $notificationList->currentPage();
                                        $lastItemIndex = $notificationList->lastItem();
                                        $startIndex = 12*($currentPage-1) + 1;
                                        if($currentPage == $notificationList->lastPage())
                                        {
                                            $endIndex = $lastItemIndex;
                                        } else {
                                            $endIndex = $startIndex+11;
                                        }
                                    @endphp
                                    <b>Tổng số:</b> {{ $notificationList->total() }} |
                                    <b>Chưa đọc:</b> {{ $unreadNotificationCount }} | <b>Trang hiện
                                        tại: {{ $startIndex . '-' . $endIndex }}</b>
                                </div>
                                <table class="table table-bordered add-manage-table notification-table">
                                    <thead>
                                    <tr>
                                        <th style="width: 80px;" class="text-center">{{ __('titles.user') }}</th>
                                        <th>{{ __('titles.content') }}</th>
                                        <th style="width: 140px;" class="text-center">{{ __('titles.option') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($notificationList as $notification)
                                        <tr class="{{ $notification->status == 0 ? 'noti-unread' : '' }}">
                                            <td style="width: 80px;" class="text-center">
                                                <a href="{{ route('users.show', $notification->user->id) }}">
                                                    <img class="noti-avatar"
                                                         src="{{ str_replace('public/', '', asset($notification->user->avatar)) }}"
                                                         alt="{{ $notification->user->name }}">
                                                </a>
                                            </td>
                                            <td>
                                                <div class="noti-content">
                                                    <div>{!! $notification->content !!}</div>
                                                    <div class="noti-time">
                                                        <i class="fa fa-clock-o"></i> <strong>Thời gian:</strong> {{ $notification->created_at }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="width: 140px;" class="text-center">
                                                <a class="btn btn-info btn-sm change-status"
                                                   style="padding: 5px 14px; border-radius: 4px; font-weight: 500;"
                                                   data-notiId="{{ $notification->id }}"
                                                   href="@if($notification->course_id) {{ '/courses/' . $notification->course_id . '/#li-comment-' . $notification->comment_id }} @elseif($notification->lecture_id) {{ '/courses/' . \App\Models\Lecture::findOrFail($notification->lecture_id)->course->id .'/lectures/' . $notification->lecture_id . '/#li-comment-' . $notification->comment_id }} @endif">
                                                    <i class="fa fa-share-square-o"></i> Chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {{ $notificationList->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('inline_scripts')
    <script type="text/javascript">
        $(document).on('ready', function () {
            $('.change-status').on('click', function (event) {
                event.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: 'notifications/' + $(this).data('notiid') + '/changeStatus',
                    success: function () {
                    }
                }, "json");
                window.location.replace($(this).attr('href'));
            })
        })
    </script>
@endsection

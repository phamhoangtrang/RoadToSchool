@extends('user.layouts.default')

@section('inline_styles')
    <link rel="stylesheet" href="{{ asset('assets/css/custom/lectures_show.css') }}">
@endsection

@section('content')
    <div id="content">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 10px; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 2px solid #e2e8f0;">
                        <h2 style="margin: 0; font-size: 20px; font-weight: 700; color: #1e293b; flex: 1; min-width: 250px; line-height: 1.4;">
                            <span class="text-primary"><i class="fa fa-play-circle"></i> {{ __('titles.lecture') }}:</span> {{ \App\Models\Lecture::findOrFail($lectureId)->title }}
                        </h2>
                        @if(!(\Auth::user() && (\Auth::user()->is_admin || \Auth::user()->role == 1)))
                            <div style="background: #e0f2fe; color: #0284c7; padding: 4px 12px; border-radius: 16px; font-weight: 600; font-size: 13.5px; white-space: nowrap; border: 1px solid #bae6fd; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fa fa-graduation-cap"></i> Tiến độ học tập: {{ $learnedLectureCount }}/{{ $allLectureCount }}
                            </div>
                        @endif
                    </div>
                    <div class="progress" style="height: 16px; margin-bottom: 20px; border-radius: 8px; background: #e2e8f0; overflow: hidden;">
                        @if(!(\Auth::user() && (\Auth::user()->is_admin || \Auth::user()->role == 1)))
                            @php
                                $percent = $allLectureCount > 0 ? round(($learnedLectureCount / $allLectureCount) * 100, 2) : 0;
                            @endphp
                            <div class="progress-bar progress-bar-info" role="progressbar"
                                 aria-valuenow="{{ $percent }}"
                                 aria-valuemin="0" aria-valuemax="100"
                                 style="width:{{ $percent }}%; line-height: 16px; font-size: 11px; font-weight: bold;">
                                {{ $percent }}%
                            </div>
                        @endif
                    </div>
                    {!! $embed->code !!}
                    
                    @if(!(\Auth::user() && (\Auth::user()->is_admin || \Auth::user()->role == 1)))
                        <div class="lecture-action-bar" style="margin-top: 15px; margin-bottom: 20px; padding: 14px 18px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                            <div class="completion-status" id="completion-status-container">
                                @if(isset($isCompleted) && $isCompleted)
                                    <span class="label label-success" style="padding: 9px 16px; font-size: 13.5px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px; background-color: #10b981;">
                                        <i class="fa fa-check-circle"></i> Đã hoàn thành bài học này
                                    </span>
                                @else
                                    <button type="button" id="btn-manual-complete" class="btn btn-success" style="padding: 9px 20px; font-size: 13.5px; font-weight: 600; border-radius: 6px; background-color: #10b981; border: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3); cursor: pointer;">
                                        <i class="fa fa-check-circle"></i> Đánh dấu hoàn thành bài học
                                    </button>
                                @endif
                            </div>
                            <div class="next-lecture-btn">
                                @php
                                    $nextLecture = \App\Models\Lecture::where('course_id', $id)->where('is_accepted', 1)->where('id', '>', $lectureId)->orderBy('id')->first();
                                @endphp
                                @if($nextLecture)
                                    <a href="{{ url('/courses/' . $id . '/lectures/' . $nextLecture->id) }}" class="btn btn-info" style="padding: 9px 18px; font-size: 13.5px; font-weight: 600; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px; background-color: #3498db; border: none;">
                                        Bài tiếp theo <i class="fa fa-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (\App\Models\Lecture::find($lectureId + 1))
                    <div id="timer" style="display: none; margin-bottom: 15px; padding: 10px; background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 6px;">
                        <p class="text-center" style="margin: 0; font-weight: 600; color: #0369a1;">
                            <i class="fa fa-clock-o"></i> Tự động chuyển sang bài tiếp theo: 
                            <i>{{ \App\Models\Lecture::find($lectureId + 1)?->title }}</i> sau <span id="timer-text" style="font-weight: 700; color: #dc2626;">6</span>s
                        </p>
                    </div>
                    @endif
                    <div class="author">
                        <div class="inner-box">
                            <div class="author-img">
                                <img id="teacher-avatar" src="{{ str_replace("public/", "", asset($teacher->avatar)) }}"
                                     alt="">
                            </div>
                            <div class="author-text">
                                <div class="author-title">
                                    <h3 class="pull-left"> {{ $teacher->name }} </h3>
                                    <div class="social-link pull-right">
                                        <a class="twitter" target="_blank" data-original-title="twitter" href="#"
                                           data-toggle="tooltip" data-placement="top"><i class="fa fa-twitter"></i></a>
                                        <a class="facebook" target="_blank" data-original-title="facebook" href="#"
                                           data-toggle="tooltip" data-placement="top"><i class="fa fa-facebook"></i></a>
                                        <a class="google" target="_blank" data-original-title="google-plus" href="#"
                                           data-toggle="tooltip" data-placement="top"><i class="fa fa-google"></i></a>
                                        <a class="linkedin" target="_blank" data-original-title="linkedin" href="#"
                                           data-toggle="tooltip" data-placement="top"><i class="fa fa-linkedin"></i></a>
                                    </div>
                                </div>
                                <br>
                                <p> {{ $description }} </p>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div id="comments">
                        <div class="inner-box">
                            <h3 id="comment-count-title" style="font-size: 30px; font-weight: bold;">Bình luận ({{ $lectureComments->count() }})</h3>
                            <ol class="comments-list" id="comment-list">
                                @foreach($lectureComments as $comment)
                                    <li id="li-comment-{{ $comment->id }}">
                                        <div class="media">
                                            <div class="thumb-left">
                                                <a href="{{ route('users.show', $comment->user->id) }}">
                                                    <img alt="{{ $comment->user->name }}"
                                                         style="height: 75px; width: 75px"
                                                         src="{{ str_replace('public/', '', asset($comment->user->avatar)) }}"/>
                                                </a>
                                            </div>
                                            <div class="info-body">
                                                <div class="media-heading">
                                                    <h4 class="name">{{ $comment->user->name }}</h4> |
                                                    <span class="comment-date">{{ $comment->updated_at }}</span>
                                                    <a class="reply-link" data-id="{{ $comment->id }}"
                                                       data-tagged="{{ $comment->user->id }}"><i
                                                                class="fa fa-reply-all"></i> Reply</a>
                                                </div>
                                                {!!  $comment->content !!}
                                            </div>
                                        </div>
                                        <br>
                                        <div class="reply-comment" style="display: none"
                                             id="reply-comment-{{ $comment->id }}">
                                            <form>
                                                <div class="row">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                                    <div class="row">
                                                        <div class=" col-md-10 pull-right">
                                                            <div class="form-group">
                        <textarea id="content-{{ $comment->id }}" class="form-control" name="content" cols="30"
                                  rows="1"
                                  placeholder="Write a reply...">@if($comment->user->name !== \Auth::user()->name) {{ $comment->user->name }} @endif</textarea>
                                                            </div>
                                                            <input type="hidden" name="taggedUser"
                                                                   id="tagged-user-{{ $comment->id }}">
                                                            <button type="submit" id="submit"
                                                                    class="btn btn-sm btn-common submit-reply-comment"
                                                                    data-parent="{{ $comment->id }}">
                                                                Reply
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <ul id="reply-comment-{{ $comment->id }}-list">
                                            @foreach($comment->child_comments as $child_comment)
                                                <li id="li-reply-{{ $child_comment->id }}">
                                                    <div class="media">
                                                        <div class="thumb-left">
                                                            <a href="{{ route('users.show', $comment->user->id) }}">
                                                                <img alt="{{ $child_comment->user->name }}"
                                                                     style="height: 75px; width: 75px"
                                                                     src="{{ str_replace('public/', '', asset($child_comment->user->avatar)) }}"/>
                                                            </a>
                                                        </div>
                                                        <div class="info-body">
                                                            <div class="media-heading">
                                                                <h4 class="name">{{ $child_comment->user->name }}</h4> |
                                                                <span class="comment-date">{{ $child_comment->updated_at }}</span>
                                                                <a class="reply-link" data-id="{{ $comment->id }}"
                                                                   data-tagged="{{ $child_comment->user->id }}"
                                                                   data-child="{{ $child_comment->id }}"><i
                                                                            class="fa fa-reply-all"></i> Trả lời </a>
                                                            </div>
                                                            <p>{!! $child_comment->content !!} </p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <br>
                                                <div class="reply-comment" style="display: none"
                                                     id="reply-comment-{{ $comment->id }}-from-{{ $child_comment->id }}">
                                                    <form>
                                                        <div class="row">
                                                            @csrf
                                                            <input type="hidden" name="user_id"
                                                                   value="{{ Auth::user()->id }}">
                                                            <div class="row">
                                                                <div class=" col-md-12 pull-right">
                                                                    <div class="form-group">
                        <textarea id="content-{{ $child_comment->id }}" class="form-control" name="content" cols="30"
                                  rows="1"
                                  placeholder="Viết phản hồi...">@if($child_comment->user->name !== \Auth::user()->name) {{ $child_comment->user->name }} @endif</textarea>
                                                                    </div>
                                                                    <input type="hidden" name="taggedUser"
                                                                           id="tagged-user-{{ $child_comment->user->id }}">
                                                                    <button type="submit" id="submit"
                                                                            class="btn btn-sm btn-common submit-reply-child-comment"
                                                                            data-child="{{ $child_comment->id }}"
                                                                            data-parent="{{ $comment->id }}">
                                                                        Gửi trả lời
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endforeach
                            </ol>

                            <div id="respond">
                                <h2 class="respond-title">Viết bình luận cho bài học
                                    <b> {{ \App\Models\Lecture::findOrFail($lectureId)->title }} </b></h2>
                                <form>
                                    <div class="row">
                                        <input type="hidden" id="comment-lecture-id" value="{{ $lectureId }}">
                                        <input type="hidden" id="comment-user-id" value="{{ Auth::user()->id }}">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <textarea id="comment-content" class="form-control" name="content"
                                                              cols="45"
                                                              rows="2" placeholder="Viết bình luận của bạn..."></textarea>
                                                </div>
                                                <button type="submit" id="submit-comment" class="btn btn-common">
                                                    Gửi bình luận
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <aside id="sidebar" class="col-md-4 right-sidebar">

                    <div class="widget">
                        <div class="categories">
                            <div class="widget-title">
                                <button class="btn btn-sm" id="active-all-lecture"
                                        onclick="openTab('all-lecture')"> {{ __('titles.all_lecture') }} </button>
                                <button class="btn btn-sm" id="active-discussion" style="background-color: #7cbde9"
                                        onclick="openTab('discussion')"> {!! __('titles.discussion') . ' (<span id="discussion-count">' . $discussionsList->count() . '</span>)' !!}</button>
                            </div>
                            <div class="categories-list tab" id="all-lecture">
                                @for($i = 0; $i < $maxWeek; $i++)
                                    <br>
                                    <h4>{{ __('titles.week') . ' ' . ($i + 1) }}</h4>
                                    <ul>
                                        @foreach($lectureOutline[$i] as $lecture)
                                            <li>
                                                <a class="@if($lecture->id == $lectureId) selected @endif"
                                                   href="{{ (\App\Models\QuizResult::where('lecture_id', $lecture->id)->where('user_id', \Auth::user()->id)->first()) ? url('/courses/' . $id . '/lectures/' . $lecture->id . '/getResult') : url('/courses/' . $id . '/lectures/' . $lecture->id) }}">
                                                    <i class="fa {{ $lecture->status ? 'fa-star' : 'fa-star-o' }}"></i>
                                                    @if($lecture->id == $lectureId)
                                                        {!! '<span><strong>' . $lecture->title . '</strong></span>' !!}
                                                    @else
                                                        {{ $lecture->title }}
                                                    @endif
                                                    <span class="category-counter"> {{ $lecture->duration }} </span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endfor
                            </div>
                            <div class="categories-list tab" id="discussion" style="display: none">
                                <div class="panel-body-chat" id="discussion-scroll">
                                    <ul class="chat" id="discussions-list">
                                        @if($discussionsList->count() != 0)
                                            @foreach($discussionsList as $discussion)
                                                <li class="left clearfix discussion-item" id="discussion-item-{{ $discussion->id }}" data-id="{{ $discussion->id }}"><span class="chat-img pull-left">
                                                <img style="height: 50px; width: 50px"
                                                     src="{{ str_replace('public/', '', asset($discussion->user->avatar)) }}"
                                                     alt="User Avatar"
                                                     class="img-circle"/>
                                                </span>
                                                    <div class="chat-body clearfix">
                                                        <div class="header">
                                                            <strong class="primary-font">{{ $discussion->user->name }}</strong>
                                                            @php
                                                                $discussionBadge = $discussion->user->is_admin ? 'Quản trị viên' : (($discussion->lecture && $discussion->lecture->course && (int)$discussion->lecture->course->user_id === (int)$discussion->user_id) ? 'Giảng viên' : '');
                                                            @endphp
                                                            @if($discussionBadge)
                                                                <span class="label label-primary" style="margin-left: 6px; font-size: 10px;">{{ $discussionBadge }}</span>
                                                            @endif
                                                            <small class="pull-right text-muted diff-time"
                                                                   id="time-{{ $discussion->id }}"
                                                                   data-time="{{ strtotime($discussion->created_at) }}">
                                                                <span class="glyphicon glyphicon-time"></span> {{ \Carbon\Carbon::parse($discussion->created_at)->locale('vi')->diffForHumans() }}
                                                            </small>
                                                        </div>
                                                        <p>
                                                            {{ $discussion->content }}
                                                        </p>
                                                    </div>
                                                </li>
                                            @endforeach
                                        @else
                                            <p id="no-discussion">{{ __('titles.no_discussion') }}</p>
                                        @endif
                                    </ul>
                                </div>
                                <div class="panel-footer">
                                    <div class="input-group">
                                        <input id="input-content" type="text" class="input-sm pull-right"
                                               style="width: 100%" placeholder="Nhập nội dung thảo luận..." maxlength="191"/>
                                        <span class="input-group-btn">
                                            <input type="hidden" id="current-user" value="{{ Auth::user()->id }}">
                                            <input type="hidden" id="lecture-id" value="{{ $lectureId }}">
                                            <button class="btn btn-warning btn-sm" id="btn-chat"
                                                    style="background-color: #3498db">
                                                Gửi</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
@endsection

@section('inline_scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('iframe').attr('width', 750);
            $('iframe').attr('height', 370);
            // $('iframe').attr('src', $('iframe').attr('src').replace('feature=oembed', 'autoplay=1'));
            $('iframe').attr('src', $('iframe').attr('src').replace('&enablejsapi=1', ''));
            // Thêm một thảo luận vào danh sách (bỏ qua nếu đã có, tránh trùng khi Pusher cũng gửi về)
            function appendDiscussion(d) {
                if ($('#discussion-item-' + d.id).length) { return; }
                $('#no-discussion').hide();
                var $li = $('<li class="left clearfix discussion-item"></li>').attr({id: 'discussion-item-' + d.id, 'data-id': d.id});
                var $img = $('<img style="height: 50px; width: 50px" alt="User Avatar" class="img-circle"/>').attr('src', d.userAvatar);
                var $time = $('<small class="pull-right text-muted diff-time"></small>')
                    .attr({id: 'time-' + d.id, 'data-time': d.time})
                    .html('<span class="glyphicon glyphicon-time"></span> vừa xong');
                var $name = $('<strong class="primary-font"></strong>').text(d.userName);
                var $badge = d.badge ? $('<span class="label label-primary" style="margin-left: 6px; font-size: 10px;"></span>').text(d.badge) : null;
                var $header = $('<div class="header"></div>').append($name, $badge, $time);
                var $body = $('<div class="chat-body clearfix"></div>').append($header, $('<p></p>').text(d.content));
                $li.append($('<span class="chat-img pull-left"></span>').append($img), $body);
                $('#discussions-list').append($li);
                $('#discussion-count').text($('#discussions-list .discussion-item').length);
                $('#discussion-scroll').animate({scrollTop: $('#discussion-scroll').prop('scrollHeight')}, 'slow');
            }

            function lastDiscussionId() {
                var max = 0;
                $('#discussions-list .discussion-item').each(function () { max = Math.max(max, parseInt($(this).attr('data-id')) || 0); });
                return max;
            }

            function sendDiscussion() {
                var content = $.trim($('#input-content').val());
                if (content === '') { return; }
                var $btn = $('#btn-chat').prop('disabled', true);
                $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
                $.ajax({
                    url: '/discussions/pusher/pushNewDiscussion',
                    type: 'post',
                    dataType: 'json',
                    data: {content: content, lectureId: $('#lecture-id').val()},
                    success: function (data) {
                        $('#input-content').val('');
                        appendDiscussion(data);
                    },
                    error: function (xhr) {
                        var msg = 'Không gửi được thảo luận, vui lòng thử lại.';
                        if (xhr.responseJSON) {
                            msg = xhr.responseJSON.message || msg;
                            if (xhr.responseJSON.errors && xhr.responseJSON.errors.content) { msg = xhr.responseJSON.errors.content[0]; }
                        }
                        alert(msg);
                    },
                    complete: function () { $btn.prop('disabled', false); $('#input-content').focus(); }
                });
            }

            $('#btn-chat').on('click', function (event) { event.preventDefault(); sendDiscussion(); });
            $('#input-content').on('keypress', function (event) {
                if (event.which === 13) { event.preventDefault(); sendDiscussion(); }
            });

            // Cập nhật thảo luận của người khác mỗi 5 giây (hoạt động cả khi chưa cấu hình Pusher)
            setInterval(function () {
                $.getJSON('/lectures/' + $('#lecture-id').val() + '/discussions', {after: lastDiscussionId()}, function (items) {
                    $.each(items, function (i, d) { appendDiscussion(d); });
                });
            }, 5000);

            $('#active-all-lecture').on('click', function () {
                $(this).removeAttr('style');
                $('#active-discussion').attr('style', 'background-color: #7cbde9');
            })

            $('#active-discussion').on('click', function () {
                $(this).removeAttr('style');
                $('#active-all-lecture').attr('style', 'background-color: #7cbde9');
                $("#discussion-scroll").animate({scrollTop: $('#discussion-scroll').prop("scrollHeight")}, 'slow');
                $('#input-content').focus();
            })

            // Thay giá trị PUSHER_APP_KEY vào chỗ xxx này nhé
            var pusher = new Pusher('f2b354d9cdae3999c31d', {
                encrypted: true,
                cluster: "ap1"
            });

            // Subscribe to the channel we specified in our Laravel Event
            var channel = pusher.subscribe('discussion');

            // Bind a function to a Event (the full Laravel class)
            channel.bind('App\\Events\\GetDiscussionFromPusherEvent', function (data) {
                appendDiscussion({
                    id: data.createdDiscussionId,
                    content: data.content,
                    userName: data.userName,
                    userAvatar: data.userAvatar,
                    time: new Date().getTime() / 1000
                });
            });

            //test area
            $('.reply-link').css({'cursor': 'pointer'});

            replyJavascript();

            // Pusher area
            var channelComment = pusher.subscribe('lecture-comment');
            channelComment.bind('App\\Events\\GetLectureCommentFromPusherEvent', function (data) {
                var commentedUser = (typeof data.user === 'string') ? jQuery.parseJSON(data.user) : data.user;
                var createdComment = (typeof data.createdLectureComment === 'string') ? jQuery.parseJSON(data.createdLectureComment) : data.createdLectureComment;
                var userAvatar = commentedUser.avatar || '';
                userAvatar = userAvatar.replace("images/", "http://127.0.0.1:8000/images/");
                commentedUser.avatar = userAvatar;
                appendNewComment(createdComment, commentedUser, createdComment.updated_at || 'Vừa xong');
            });

            var channelReplyLectureComment = pusher.subscribe('replyLectureComment');
            channelReplyLectureComment.bind('App\\Events\\GetReplyLectureCommentFromPusherEvent', function (data) {
                var commentedUser = (typeof data.user === 'string') ? jQuery.parseJSON(data.user) : data.user;
                var createdComment = (typeof data.createdComment === 'string') ? jQuery.parseJSON(data.createdComment) : data.createdComment;
                var userAvatar = commentedUser.avatar || '';
                userAvatar = userAvatar.replace("images/", "http://127.0.0.1:8000/images/");
                commentedUser.avatar = userAvatar;
                appendNewReply(createdComment, commentedUser, createdComment.updated_at || 'Vừa xong', data.parentCommentId);
            });

            // GỬI BÌNH LUẬN BÀI GIẢNG
            $('#comments').off('click', '#submit-comment').on('click', '#submit-comment', function (event) {
                event.preventDefault();
                var $btn = $(this);
                if ($btn.prop('disabled')) return;

                var contentVal = $('#comment-content').val().trim();
                if (!contentVal) {
                    alert('Vui lòng nhập nội dung bình luận.');
                    return;
                }
                var lectureId = $('#comment-lecture-id').val();
                var userId = $('#comment-user-id').val();

                $btn.prop('disabled', true).text('Đang gửi...');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/lectures/' + lectureId + '/pusher/postComment',
                    type: 'post',
                    data: {
                        content: contentVal,
                        userId: userId,
                    },
                    success: function (res) {
                        $('#comment-content').val('');
                        var resData = (typeof res === 'string') ? JSON.parse(res) : res;
                        if (resData && resData.comment && resData.user) {
                            appendNewComment(resData.comment, resData.user, resData.created_at || 'Vừa xong');
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi gửi bình luận, vui lòng thử lại.');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Gửi bình luận');
                    }
                });
            });
        });

        // --- HÀM THÊM BÌNH LUẬN TRỰC TIẾP VÀO BÀI HỌC KHÔNG CẦN F5 ---
        function appendNewComment(comment, user, createdAt) {
            if ($('#li-comment-' + comment.id).length > 0) return;
            var userAvatar = user.avatar || '/images/default_avatar/default_avatar.jpg';
            if (!userAvatar.startsWith('http') && !userAvatar.startsWith('/')) {
                userAvatar = '/' + userAvatar;
            }
            var currentUserId = '{{ \Auth::id() }}';
            var newCommentHtml =
                '<li id="li-comment-' + comment.id + '">' +
                    '<div class="media">' +
                        '<div class="thumb-left">' +
                            '<a href="/users/' + user.id + '">' +
                                '<img alt="' + user.name + '" style="height: 75px; width: 75px; border-radius: 50%; object-fit: cover;" src="' + userAvatar + '"/>' +
                            '</a>' +
                        '</div>' +
                        '<div class="info-body">' +
                            '<div class="media-heading">' +
                                '<h4 class="name">' + user.name + '</h4> | ' +
                                '<span class="comment-date">' + createdAt + '</span> ' +
                                '<a class="reply-link" data-id="' + comment.id + '" data-tagged="' + user.id + '" style="cursor: pointer;"><i class="fa fa-reply-all"></i> Trả lời</a>' +
                            '</div>' +
                            '<p>' + comment.content + '</p>' +
                        '</div>' +
                    '</div>' +
                    '<br>' +
                    '<div class="reply-comment" style="display: none" id="reply-comment-' + comment.id + '">' +
                        '<form>' +
                            '<div class="row">' +
                                '<input type="hidden" name="user_id" value="' + currentUserId + '">' +
                                '<div class="row">' +
                                    '<div class="col-md-10 pull-right">' +
                                        '<div class="form-group">' +
                                            '<textarea id="content-' + comment.id + '" class="form-control content-reply" name="content" cols="30" rows="1" placeholder="Viết phản hồi..."></textarea>' +
                                        '</div>' +
                                        '<input type="hidden" name="taggedUser" id="tagged-user-' + user.id + '">' +
                                        '<button type="submit" class="btn btn-sm btn-common submit-reply-comment" data-parent="' + comment.id + '">Gửi trả lời</button>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</form>' +
                    '</div>' +
                    '<ul id="reply-comment-' + comment.id + '-list"></ul>' +
                '</li>';

            $('#comment-list').append(newCommentHtml);
            var count = $('#comment-list > li').length;
            $('#comment-count-title').text('Bình luận (' + count + ')');
            replyJavascript();
        }

        function appendNewReply(comment, user, createdAt, parentCommentId) {
            if ($('#li-reply-' + comment.id).length > 0) return;
            var userAvatar = user.avatar || '/images/default_avatar/default_avatar.jpg';
            if (!userAvatar.startsWith('http') && !userAvatar.startsWith('/')) {
                userAvatar = '/' + userAvatar;
            }
            var currentUserId = '{{ \Auth::id() }}';
            var newReplyHtml =
                '<li id="li-reply-' + comment.id + '">' +
                    '<div class="media">' +
                        '<div class="thumb-left">' +
                            '<a href="/users/' + user.id + '">' +
                                '<img alt="' + user.name + '" style="height: 75px; width: 75px; border-radius: 50%; object-fit: cover;" src="' + userAvatar + '"/>' +
                            '</a>' +
                        '</div>' +
                        '<div class="info-body">' +
                            '<div class="media-heading">' +
                                '<h4 class="name">' + user.name + '</h4> | ' +
                                '<span class="comment-date">' + createdAt + '</span> ' +
                                '<a class="reply-link" data-id="' + parentCommentId + '" data-child="' + comment.id + '" data-tagged="' + user.id + '" style="cursor: pointer;"><i class="fa fa-reply-all"></i> Trả lời</a>' +
                            '</div>' +
                            '<p>' + comment.content + '</p>' +
                        '</div>' +
                    '</div>' +
                    '<br>' +
                    '<div class="reply-comment" style="display: none" id="reply-comment-' + parentCommentId + '-from-' + comment.id + '">' +
                        '<form>' +
                            '<div class="row">' +
                                '<input type="hidden" name="user_id" value="' + currentUserId + '">' +
                                '<div class="row">' +
                                    '<div class="col-md-12 pull-right">' +
                                        '<div class="form-group">' +
                                            '<textarea id="content-' + comment.id + '" class="form-control content-reply" name="content" cols="30" rows="1" placeholder="Viết phản hồi..."></textarea>' +
                                        '</div>' +
                                        '<input type="hidden" name="taggedUser" id="tagged-user-' + user.id + '">' +
                                        '<button type="submit" class="btn btn-sm btn-common submit-reply-child-comment" data-child="' + comment.id + '" data-parent="' + parentCommentId + '">Gửi trả lời</button>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</form>' +
                    '</div>' +
                '</li>';

            var targetList = $('#reply-comment-' + parentCommentId + '-list');
            if (targetList.length === 0) {
                $('#li-comment-' + parentCommentId).append('<ul id="reply-comment-' + parentCommentId + '-list"></ul>');
                targetList = $('#reply-comment-' + parentCommentId + '-list');
            }
            targetList.append(newReplyHtml);
            replyJavascript();
        }

        function openTab(tab) {
            $('.tab').attr('style', 'display:none');
            $('#' + tab).attr('style', 'display:block');
        }

        // test area
        function replyJavascript() {
            $('#comments').off('click', '.reply-link').on('click', '.reply-link', function (event) {
                event.preventDefault();
                if ($(this).data('child')) {
                    if ($('#reply-comment-' + $(this).data('id') + '-from-' + $(this).data('child')).css('display') == 'none') {
                        $('#reply-comment-' + $(this).data('id') + '-from-' + $(this).data('child')).show();
                        $('#content-' + $(this).data('child')).focus();
                        $('#content-' + $(this).data('child')).putCursorAtEnd();
                        $('#tagged-user-' + $(this).data('tagged')).val($(this).data('tagged'));
                    } else {
                        $('#reply-comment-' + $(this).data('id') + '-from-' + $(this).data('child')).hide();
                    }
                } else {
                    if ($('#reply-comment-' + $(this).data('id')).css('display') == 'none') {
                        $('#reply-comment-' + $(this).data('id')).show();
                        $('#content-' + $(this).data('id')).focus();
                        $('#content-' + $(this).data('id')).putCursorAtEnd();
                        $('#tagged-user-' + $(this).data('id')).val($(this).data('tagged') + ' ');
                    } else {
                        $('#reply-comment-' + $(this).data('id')).hide();
                    }
                }
            });

            // GỬI PHẢN HỒI (REPLY) BÀI GIẢNG
            $('#comments').off('click', '.submit-reply-comment, .submit-reply-child-comment').on('click', '.submit-reply-comment, .submit-reply-child-comment', function (event) {
                event.preventDefault();
                var $btn = $(this);
                if ($btn.prop('disabled')) return;

                var contentComment = '';
                var firstChildComment = true;
                var prevCommentId = 0;
                var parentId = $btn.data('parent');

                if ($btn.data('id') != undefined) {
                    contentComment = $('#content-' + $btn.data('id')).val();
                    firstChildComment = false;
                    prevCommentId = $btn.data('id');
                } else if ($btn.data('child') != undefined) {
                    contentComment = $('#content-' + $btn.data('child')).val();
                    firstChildComment = false;
                    prevCommentId = $btn.data('child');
                } else {
                    contentComment = $('#content-' + parentId).val();
                }

                if (!contentComment || !contentComment.trim()) {
                    alert('Vui lòng nhập nội dung phản hồi.');
                    return;
                }

                $btn.prop('disabled', true).text('Đang gửi...');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/lectures/' + $('#comment-lecture-id').val() + '/pusher/replyLectureComment/' + parentId,
                    type: 'post',
                    data: {
                        content: contentComment,
                        userId: $('#comment-user-id').val(),
                        firstChildComment: firstChildComment,
                        prevCommentId: prevCommentId,
                    },
                    success: function (data) {
                        var responseData = (typeof data === 'string') ? JSON.parse(data) : data;
                        if (prevCommentId) {
                            $('#content-' + prevCommentId).val('');
                            $('#reply-comment-' + parentId + '-from-' + prevCommentId).hide();
                        } else {
                            $('#content-' + parentId).val('');
                            $('#reply-comment-' + parentId).hide();
                        }
                        if (responseData && responseData.comment && responseData.user) {
                            appendNewReply(responseData.comment, responseData.user, responseData.created_at || 'Vừa xong', parentId);
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi gửi phản hồi, vui lòng thử lại.');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Gửi trả lời');
                    }
                });
            });
        }

        // https://css-tricks.com/snippets/jquery/move-cursor-to-end-of-textarea-or-input/
        jQuery.fn.putCursorAtEnd = function () {

            return this.each(function () {

                // Cache references
                var $el = $(this),
                    el = this;

                // Only focus if input isn't already
                if (!$el.is(":focus")) {
                    $el.focus();
                }

                // If this function exists... (IE 9+)
                if (el.setSelectionRange) {

                    // Double the length because Opera is inconsistent about whether a carriage return is one character or two.
                    var len = $el.val().length * 2;

                    // Timeout seems to be required for Blink
                    setTimeout(function () {
                        el.setSelectionRange(len, len);
                    }, 1);

                } else {

                    // As a fallback, replace the contents with itself
                    // Doesn't work in Chrome, but Chrome supports setSelectionRange
                    $el.val($el.val());

                }

                // Scroll to the bottom, in case we're in a tall textarea
                // (Necessary for Firefox and Chrome)
                this.scrollTop = 999999;

            });
        };


        // Setup YouTube iframe API and Completion tracking
        var $iframe = $('iframe');
        if ($iframe.length > 0) {
            $iframe.attr('id', 'player');
            var oldSrc = $iframe.attr('src') || '';
            var delim = oldSrc.indexOf('?') !== -1 ? '&' : '?';
            if (oldSrc.indexOf('enablejsapi=1') === -1) {
                var origin = encodeURIComponent(window.location.origin);
                $iframe.attr('src', oldSrc + delim + 'enablejsapi=1&origin=' + origin);
            }

            var tag = document.createElement('script');
            tag.src = "https://www.youtube.com/iframe_api";
            var firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        }

        var player;
        function onYouTubeIframeAPIReady() {
            try {
                player = new YT.Player('player', {
                    events: {
                        'onStateChange': onPlayerStateChange
                    }
                });
            } catch (e) {
                console.warn('YouTube Iframe Player API notice:', e);
            }
        }

        var isCompletedRequestSending = false;
        function triggerCompleteLecture() {
            if (isCompletedRequestSending) return;
            isCompletedRequestSending = true;

            var lectureId = '{{ $lectureId }}';
            var userId = $('#comment-user-id').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/lectures/' + lectureId + '/user/' + userId + '/changeProcessStatus',
                type: 'post',
                success: function (data) {
                    var responseData = typeof data === 'string' ? JSON.parse(data) : data;

                    // Update UI immediately to completed
                    $('#completion-status-container').html(
                        '<span class="label label-success" style="padding: 9px 16px; font-size: 13.5px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px; background-color: #10b981;">' +
                        '<i class="fa fa-check-circle"></i> Đã hoàn thành bài học này</span>'
                    );

                    if (responseData.isLastLecture == 1) {
                        $.notify({
                            message: 'Chúc mừng! Bạn đã hoàn thành toàn bộ khóa học này.'
                        }, {
                            type: 'success',
                            placement: {
                                from: "bottom",
                                align: "right"
                            }
                        });
                    } else if (responseData.isLastLecture == 0 && responseData.nextLecture) {
                        var redirectUrl = '/courses/' + responseData.inCourseId + '/lectures/' + responseData.nextLecture.id;
                        $.notify({
                            message: 'Bạn đã hoàn thành bài học này! Chuẩn bị chuyển sang: <b>' + responseData.nextLecture.title + '</b>',
                            url: redirectUrl,
                            target: "_self"
                        }, {
                            type: 'success',
                            placement: {
                                from: "bottom",
                                align: "right"
                            },
                            delay: 6000
                        });
                        $('#timer').show();
                        var timer = 6;
                        $('#timer-text').text(timer);
                        var interval = setInterval(function () {
                            timer = timer - 1;
                            $('#timer-text').text(timer);
                            if (timer <= 0) {
                                clearInterval(interval);
                                window.location.replace(redirectUrl);
                            }
                        }, 1000);
                    }
                },
                error: function (err) {
                    isCompletedRequestSending = false;
                    console.error('Lỗi cập nhật tiến độ:', err);
                }
            });
        }

        function onPlayerStateChange(event) {
            if (event.data === YT.PlayerState.ENDED) {
                triggerCompleteLecture();
            }
        }

        $(document).on('click', '#btn-manual-complete', function (e) {
            e.preventDefault();
            $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang cập nhật...');
            triggerCompleteLecture();
        });
    </script>
@endsection

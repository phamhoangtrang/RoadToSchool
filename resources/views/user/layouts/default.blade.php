<!DOCTYPE html>
<html lang="en">
@include('user.layouts.head')
@yield('inline_styles')
<body>
@include('user.layouts.header')
<!--Content-->
@yield('content')
<!--./Content -->

@if(\Auth::user())
<div class="popup-box chat-popup" id="qnimate">
    <div class="popup-head" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; padding: 12px 16px; border-radius: 12px 12px 0 0;">
        <div class="popup-head-left pull-left" style="display: flex; align-items: center; gap: 8px;">
            <img src="{{ asset('assets/img/logo-short.png') }}" alt="R2S" style="width: 24px; height: 24px; border-radius: 50%; background: #fff; padding: 2px;">
            <div>
                <span style="font-weight: 700; font-size: 13px; display: block; line-height: 1.2;">TRỢ LÝ AI R2S</span>
                <span style="font-size: 10px; color: #2ecc71; display: block;"><i class="fa fa-circle" style="font-size: 8px;"></i> Sẵn sàng hỗ trợ 24/7</span>
            </div>
        </div>
        <div class="popup-head-right pull-right">
            <button data-widget="remove" id="removeClass" class="chat-header-button pull-right" type="button" style="color: #fff; opacity: 0.8; font-size: 16px;"><i
                        class="glyphicon glyphicon-minus"></i></button>
        </div>
    </div>
    <div class="popup-messages" id="popup-messages">
        <div class="direct-chat-messages" id="conversation-list" style="overflow: auto">
            @php
                $conversation = \App\Models\Conversation::where('user_sender_id', \Auth::user()->id)->first();
                $messageList = $conversation ? $conversation->conversation_messages : collect();
            @endphp
            @if(isset($messageList) && $messageList->count() > 0)
                @php $lastDateLabel = null; @endphp
                @foreach($messageList->sortBy('id')->slice(-50) as $message)
                    @php
                        $msgDate = $message->created_at ? $message->created_at->copy()->startOfDay() : null;
                        $dateLabel = null;
                        if ($msgDate) {
                            if ($msgDate->isToday()) {
                                $dateLabel = 'Hôm nay';
                            } elseif ($msgDate->isYesterday()) {
                                $dateLabel = 'Hôm qua';
                            } else {
                                $dateLabel = $msgDate->format('d/m/Y');
                            }
                        }
                    @endphp
                    @if($dateLabel && $dateLabel !== $lastDateLabel)
                        <div class="chat-box-single-line">
                            <abbr class="timestamp">{{ $dateLabel }}</abbr>
                        </div>
                        @php $lastDateLabel = $dateLabel; @endphp
                    @endif
                    @php
                        $isCurrentUser = ($message->from_id == \Auth::user()->id);
                        $sender = \App\Models\User::find($message->from_id);
                        $senderName = $isCurrentUser ? 'Bạn' : ($sender ? $sender->name : 'Trợ lý R2S');
                        if (!$isCurrentUser && ($sender && ($sender->is_admin == 1 || $sender->role == 1))) {
                            $senderName = 'Trợ lý AI RoadToSchool';
                        }
                    @endphp
                    <div class="direct-chat-msg doted-border {{ $isCurrentUser ? 'text-right' : '' }}" style="margin-bottom: 14px;">
                        <div class="direct-chat-info clearfix">
                            <span class="direct-chat-name {{ $isCurrentUser ? 'pull-right' : 'pull-left' }}" style="font-weight: 600; color: {{ $isCurrentUser ? '#34495e' : '#e67e22' }};">
                                @if(!$isCurrentUser) <i class="fa fa-android"></i> @endif
                                {{ $senderName }}
                            </span>
                        </div>
                        @if(!$isCurrentUser)
                            <img alt="avatar"
                                 src="{{ asset('assets/img/logo-short.png') }}"
                                 class="direct-chat-img" style="border-radius: 50%; width: 32px; height: 32px;"><!-- /.direct-chat-img -->
                        @endif
                        <div class="direct-chat-text" style="{{ $isCurrentUser ? 'background-color: #3498db; color: #fff; border-color: #2980b9; display: inline-block; text-align: left; max-width: 85%; border-radius: 12px 12px 2px 12px; margin: 4px 0;' : 'background-color: #f1f8fe; color: #2c3e50; border-color: #d6eaf8; display: inline-block; max-width: 85%; border-radius: 12px 12px 12px 2px; margin: 4px 0; line-height: 1.5;' }}">
                            {!! nl2br(e($message->content)) !!}
                        </div>
                        <div class="direct-chat-info clearfix">
                            <span class="direct-chat-timestamp {{ $isCurrentUser ? 'pull-right' : 'pull-left' }}" style="font-size: 11px; color: #95a5a6; {{ !$isCurrentUser ? 'margin-left: 42px;' : '' }}">{{ $message->created_at ? $message->created_at->toTimeString() : '' }}</span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center p-3" style="color: #7f8c8d; font-size: 13px;">
                    <i class="fa fa-comments-o" style="font-size: 32px; color: #bdc3c7; margin-bottom: 8px; display: block;"></i>
                    Chào mừng bạn đến với RoadToSchool!<br>Hãy nhập câu hỏi để được <b>Trợ lý AI</b> giải đáp ngay tức thì nhé.
                </div>
            @endif
        </div>
    </div>
    <div class="popup-messages-footer">
        <textarea id="status_message" placeholder="Hỏi về khóa học, học phí, thanh toán... (Nhấn Enter để gửi)" rows="2" cols="40" name="message" style="border-radius: 8px; font-size: 13px;"></textarea>
        <input type="hidden" value="{{ \Auth::user()->id }}" id="sender-id">
    </div>
</div>
@endif
@include('user.layouts.footer')
@yield('inline_scripts')
</body>
</html>

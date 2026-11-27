@extends('admin.layouts.default')

@section('title')
    Hỏi đáp học viên
@endsection

@section('inline_styles')
    <style>
        .qa-kpi { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 18px; }
        .qa-kpi-card { flex: 1 1 180px; background: #fff; border: 1px solid #e8edf3; border-radius: 14px; padding: 16px 20px; box-shadow: 0 4px 16px rgba(15,23,42,.04); }
        .qa-kpi-card .num { font-size: 26px; font-weight: 800; line-height: 1.2; }
        .qa-kpi-card .lbl { font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600; letter-spacing: .4px; }
        .qa-filter { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; background: #fff; border: 1px solid #e8edf3; border-radius: 14px; padding: 12px 16px; margin-bottom: 18px; }
        .qa-pills a { display: inline-block; padding: 6px 14px; border-radius: 999px; font-size: 13px; font-weight: 600; color: #475569; background: #f1f5f9; margin-right: 6px; text-decoration: none; }
        .qa-pills a.active { background: #2563eb; color: #fff; }
        .qa-filter select { border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 10px; font-size: 13px; background: #fff; max-width: 260px; }
        .qa-card { background: #fff; border: 1px solid #e8edf3; border-radius: 14px; padding: 18px 20px; margin-bottom: 14px; box-shadow: 0 4px 16px rgba(15,23,42,.04); border-left: 4px solid #f59e0b; }
        .qa-card.answered { border-left-color: #10b981; }
        .qa-head { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
        .qa-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; flex-shrink: 0; }
        .qa-name { font-weight: 700; color: #0f172a; }
        .qa-time { font-size: 12px; color: #94a3b8; }
        .qa-type { margin-left: auto; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 999px; background: #eff6ff; color: #1d4ed8; white-space: nowrap; }
        .qa-type.lecture { background: #f5f3ff; color: #6d28d9; }
        .qa-type.discussion { background: #ecfeff; color: #0e7490; }
        .qa-context { font-size: 12.5px; color: #64748b; margin-bottom: 8px; }
        .qa-content { font-size: 14.5px; color: #1e293b; line-height: 1.55; white-space: pre-wrap; word-break: break-word; }
        .qa-status { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 999px; }
        .qa-status.wait { background: #fef3c7; color: #b45309; }
        .qa-status.done { background: #dcfce7; color: #166534; }
        .qa-replies { margin: 12px 0 0 52px; padding-left: 14px; border-left: 2px solid #e2e8f0; }
        .qa-reply { margin-bottom: 8px; font-size: 13.5px; color: #334155; }
        .qa-reply .who { font-weight: 700; }
        .qa-reply .staff { color: #2563eb; }
        .qa-form { margin: 12px 0 0 52px; display: flex; gap: 8px; align-items: flex-start; }
        .qa-form textarea { flex: 1; border: 1px solid #e2e8f0; border-radius: 10px; padding: 8px 12px; font-size: 14px; resize: vertical; min-height: 40px; }
        .qa-form textarea:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.12); }
        .qa-empty { text-align: center; color: #94a3b8; padding: 50px 0; background: #fff; border-radius: 14px; border: 1px dashed #e2e8f0; }
        .qa-error { color: #dc2626; font-size: 12.5px; margin: 6px 0 0 52px; }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <h2 class="header-title">Hỏi đáp học viên</h2>
                <div class="header-sub-title">
                    <nav class="breadcrumb breadcrumb-dash">
                        <a href="{{ route('instructor.dashboard') }}" class="breadcrumb-item"><i class="ti-home p-r-5"></i>Bảng điều khiển</a>
                        <span class="breadcrumb-item active">Hỏi đáp</span>
                    </nav>
                </div>
            </div>

            <div class="qa-kpi">
                <div class="qa-kpi-card"><div class="lbl">Chưa trả lời</div><div class="num" style="color:#d97706;" id="kpi-unanswered">{{ $stats['unanswered'] }}</div></div>
                <div class="qa-kpi-card"><div class="lbl">Đã trả lời</div><div class="num" style="color:#059669;" id="kpi-answered">{{ $stats['answered'] }}</div></div>
                <div class="qa-kpi-card"><div class="lbl">Tổng câu hỏi</div><div class="num" style="color:#1e293b;">{{ $stats['total'] }}</div></div>
            </div>

            <form method="get" class="qa-filter" id="qa-filter-form">
                <div class="qa-pills">
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'unanswered', 'page' => 1]) }}" class="{{ $status === 'unanswered' ? 'active' : '' }}">Chưa trả lời</a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'answered', 'page' => 1]) }}" class="{{ $status === 'answered' ? 'active' : '' }}">Đã trả lời</a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'all', 'page' => 1]) }}" class="{{ $status === 'all' ? 'active' : '' }}">Tất cả</a>
                </div>
                <input type="hidden" name="status" value="{{ $status }}">
                <select name="kind" onchange="this.form.submit()">
                    <option value="all" {{ $kind === 'all' ? 'selected' : '' }}>Mọi loại</option>
                    <option value="course" {{ $kind === 'course' ? 'selected' : '' }}>Bình luận khóa học</option>
                    <option value="lecture" {{ $kind === 'lecture' ? 'selected' : '' }}>Bình luận bài giảng</option>
                    <option value="discussion" {{ $kind === 'discussion' ? 'selected' : '' }}>Thảo luận</option>
                </select>
                <select name="course_id" onchange="this.form.submit()">
                    <option value="0">Tất cả khóa học</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $courseId === $course->id ? 'selected' : '' }}>{{ \Illuminate\Support\Str::limit($course->title, 60) }}</option>
                    @endforeach
                </select>
            </form>

            @forelse($items as $item)
                @php
                    $student = $item['student'];
                    $avatar = $student->avatar_url;
                    $typeLabel = ['course' => 'Bình luận khóa học', 'lecture' => 'Bình luận bài giảng', 'discussion' => 'Thảo luận'][$item['kind']];
                @endphp
                <div class="qa-card {{ $item['answered'] ? 'answered' : '' }}" id="qa-{{ $item['key'] }}"
                     data-kind="{{ $item['kind'] }}" data-id="{{ $item['id'] }}">
                    <div class="qa-head">
                        <img class="qa-avatar" src="{{ $avatar }}" alt="" onerror="this.onerror=null;this.src='{{ asset('assets/img/avatar_users/default_avatar.png') }}'">
                        <div>
                            <div class="qa-name">{{ $student->name }}</div>
                            <div class="qa-time">{{ $item['created_at']->locale('vi')->diffForHumans() }}</div>
                        </div>
                        <span class="qa-status {{ $item['answered'] ? 'done' : 'wait' }}">{{ $item['answered'] ? 'Đã trả lời' : 'Chưa trả lời' }}</span>
                        <span class="qa-type {{ $item['kind'] }}">{{ $typeLabel }}</span>
                    </div>
                    <div class="qa-context"><i class="fa fa-book"></i> {{ $item['context'] }}</div>
                    <div class="qa-content" style="margin-left:52px;">{{ html_entity_decode($item['content']) }}</div>

                    @if($item['replies']->isNotEmpty())
                        <div class="qa-replies">
                            @foreach($item['replies'] as $reply)
                                <div class="qa-reply">
                                    <span class="who {{ $reply['staff'] ? 'staff' : '' }}">{{ $reply['name'] }}{{ $reply['staff'] ? ' (Giảng viên)' : '' }}:</span>
                                    {{ html_entity_decode($reply['content']) }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="qa-replies" id="new-replies-{{ $item['key'] }}" style="display:none;"></div>

                    <div class="qa-form">
                        <textarea rows="1" maxlength="191" placeholder="Nhập câu trả lời cho học viên..."></textarea>
                        <button type="button" class="btn btn-gradient-success btn-qa-send">Gửi</button>
                    </div>
                    <div class="qa-error" style="display:none;"></div>
                </div>
            @empty
                <div class="qa-empty">
                    <i class="ti-comments" style="font-size:36px;"></i>
                    <div style="margin-top:8px;">
                        @if($status === 'unanswered') Tuyệt vời, không còn câu hỏi nào chưa trả lời. @else Không có mục nào phù hợp bộ lọc. @endif
                    </div>
                </div>
            @endforelse

            <div class="text-center">{{ $items->links() }}</div>
        </div>
    </div>
@endsection

@section('inline_scripts')
    <script>
        $(function () {
            $(document).on('click', '.btn-qa-send', function () {
                var $card = $(this).closest('.qa-card');
                var $ta = $card.find('textarea');
                var $err = $card.find('.qa-error').hide();
                var content = $.trim($ta.val());
                if (!content) { $err.text('Vui lòng nhập nội dung trả lời.').show(); return; }
                var $btn = $(this).prop('disabled', true);

                $.ajax({
                    url: "{{ route('instructor.qa.reply') }}",
                    type: 'POST',
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {kind: $card.data('kind'), id: $card.data('id'), content: content},
                    success: function (res) {
                        var $box = $card.find('[id^="new-replies-"]').show();
                        $box.append($('<div class="qa-reply"></div>')
                            .append($('<span class="who staff"></span>').text(res.reply.name + ' (Giảng viên):'))
                            .append(document.createTextNode(' ' + res.reply.content)));
                        $ta.val('');
                        if (!$card.hasClass('answered')) {
                            $card.addClass('answered');
                            $card.find('.qa-status').removeClass('wait').addClass('done').text('Đã trả lời');
                            $('#kpi-unanswered').text(Math.max(0, parseInt($('#kpi-unanswered').text()) - 1));
                            $('#kpi-answered').text(parseInt($('#kpi-answered').text()) + 1);
                        }
                    },
                    error: function (xhr) {
                        var msg = 'Không gửi được, vui lòng thử lại.';
                        if (xhr.responseJSON) {
                            msg = xhr.responseJSON.message || msg;
                            if (xhr.responseJSON.errors && xhr.responseJSON.errors.content) { msg = xhr.responseJSON.errors.content[0]; }
                        }
                        $err.text(msg).show();
                    },
                    complete: function () { $btn.prop('disabled', false); }
                });
            });
        });
    </script>
@endsection

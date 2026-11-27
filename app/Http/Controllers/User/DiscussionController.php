<?php

namespace App\Http\Controllers\User;

use App\Events\GetDiscussionFromPusherEvent;
use App\Http\Controllers\Controller;
use App\Models\CourseUser;
use App\Models\Discussion;
use App\Models\Lecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public $modelDiscussion;

    public function __construct(Discussion $discussion)
    {
        $this->modelDiscussion = $discussion;
    }

    /**
     * Học viên của khóa học (hoặc giảng viên/admin) mới được xem và gửi thảo luận.
     */
    protected function canAccessLecture($lectureId)
    {
        $user = Auth::user();
        if ($user->is_admin || $user->role == 1) {
            return true;
        }
        $lecture = Lecture::find($lectureId);

        return $lecture && CourseUser::where('course_id', $lecture->course_id)->where('user_id', $user->id)->exists();
    }

    protected function toPayload(Discussion $discussion)
    {
        $user = $discussion->user;

        $course = optional($discussion->lecture)->course;
        $badge = '';
        if ($user && $user->is_admin) {
            $badge = 'Quản trị viên';
        } elseif ($user && $course && (int)$user->id === (int)$course->user_id) {
            $badge = 'Giảng viên';
        }

        return [
            'id' => $discussion->id,
            'badge' => $badge,
            'content' => $discussion->content,
            'userName' => $user ? $user->name : 'Ẩn danh',
            'userAvatar' => $user ? $user->avatar_url : asset('assets/img/avatar_users/default_avatar.png'),
            'time' => strtotime($discussion->created_at),
        ];
    }

    public function createNewDiscussion(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:191',
            'lectureId' => 'required|integer|exists:lectures,id',
        ], [
            'content.required' => 'Vui lòng nhập nội dung thảo luận.',
            'content.max' => 'Nội dung thảo luận tối đa 191 ký tự.',
        ]);

        if (!$this->canAccessLecture($request->lectureId)) {
            return response()->json(['message' => 'Bạn chưa tham gia khóa học này.'], 403);
        }

        $discussion = $this->modelDiscussion->create([
            'content' => trim($request->content),
            'lecture_id' => $request->lectureId,
            'user_id' => Auth::id(),
        ]);

        // Phát thời gian thực qua Pusher (nếu đã cấu hình); lỗi Pusher không làm hỏng việc gửi thảo luận
        try {
            event(new GetDiscussionFromPusherEvent($discussion->content, Auth::id(), $discussion->id));
        } catch (\Throwable $e) {
            \Log::warning('Pusher discussion broadcast failed: ' . $e->getMessage());
        }

        return response()->json($this->toPayload($discussion->load(['user', 'lecture.course'])));
    }

    /**
     * Lấy các thảo luận mới hơn mã `after` (trình duyệt hỏi định kỳ, không phụ thuộc Pusher).
     */
    public function index(Request $request, $lectureId)
    {
        if (!$this->canAccessLecture($lectureId)) {
            return response()->json(['message' => 'Bạn chưa tham gia khóa học này.'], 403);
        }

        $items = $this->modelDiscussion->with(['user', 'lecture.course'])
            ->where('lecture_id', $lectureId)
            ->where('id', '>', (int)$request->query('after', 0))
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->map(function ($discussion) {
                return $this->toPayload($discussion);
            });

        return response()->json($items);
    }
}

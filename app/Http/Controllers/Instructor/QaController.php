<?php

namespace App\Http\Controllers\Instructor;

use App\Events\GetDiscussionFromPusherEvent;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Discussion;
use App\Models\Lecture;
use App\Models\LectureComment;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Hộp thư Hỏi đáp của giảng viên: gom bình luận khóa học, bình luận bài giảng và thảo luận
 * của học viên, cho biết câu nào chưa trả lời và cho trả lời ngay tại chỗ.
 */
class QaController extends Controller
{
    const PER_PAGE = 15;

    protected function ensureStaff()
    {
        $user = Auth::user();
        abort_unless($user && ($user->is_admin || $user->role == 1), 403, 'Chỉ giảng viên hoặc quản trị viên mới được truy cập.');

        return $user;
    }

    /** Giảng viên chỉ được động vào khóa học của mình; quản trị viên được tất cả. */
    protected static function courseIdsFor($user)
    {
        return $user->is_admin ? Course::pluck('id') : Course::where('user_id', $user->id)->pluck('id');
    }

    protected function assertOwnsCourse($user, $courseId)
    {
        abort_unless($user->is_admin || Course::where('id', $courseId)->where('user_id', $user->id)->exists(), 403, 'Bạn không phải giảng viên của khóa học này.');
    }

    /** Người viết có phải "phía giảng dạy" của khóa học này (giảng viên của khóa hoặc quản trị viên) không. */
    protected static function isStaffFor($author, $course)
    {
        return $author && $course && ($author->is_admin || (int)$author->id === (int)$course->user_id);
    }

    /**
     * Danh sách tất cả mục hỏi đáp (đã gồm trạng thái trả lời), mới nhất trước.
     */
    public static function buildItems($user)
    {
        $courseIds = static::courseIdsFor($user);
        $items = collect();

        // 1) Bình luận khóa học
        $roots = Comment::with(['user', 'course'])->whereIn('course_id', $courseIds)->whereNull('parent_comment')->get();
        $children = Comment::with('user')->whereIn('parent_comment', $roots->pluck('id'))->orderBy('id')->get()->groupBy('parent_comment');
        foreach ($roots as $comment) {
            if (!$comment->course || !$comment->user || static::isStaffFor($comment->user, $comment->course)) {
                continue;
            }
            $replies = $children->get($comment->id, collect());
            $items->push([
                'key' => 'course-' . $comment->id,
                'kind' => 'course',
                'id' => $comment->id,
                'course_id' => $comment->course_id,
                'student' => $comment->user,
                'content' => $comment->content,
                'created_at' => $comment->created_at ?: Carbon::now(),
                'context' => 'Khóa học: ' . $comment->course->title,
                'link' => url('/courses/' . $comment->course_id . '#li-comment-' . $comment->id),
                'replies' => $replies->map(function ($r) use ($comment) {
                    return ['name' => $r->user ? $r->user->name : 'Ẩn danh', 'content' => $r->content, 'at' => $r->created_at, 'staff' => static::isStaffFor($r->user, $comment->course)];
                })->values(),
                'answered' => $replies->contains(function ($r) use ($comment) {
                    return static::isStaffFor($r->user, $comment->course);
                }),
            ]);
        }

        // 2) Bình luận bài giảng
        $lectureIds = Lecture::whereIn('course_id', $courseIds)->pluck('id');
        $lRoots = LectureComment::with(['user', 'lecture.course'])->whereIn('lecture_id', $lectureIds)->whereNull('parent_comment')->get();
        $lChildren = LectureComment::with('user')->whereIn('parent_comment', $lRoots->pluck('id'))->orderBy('id')->get()->groupBy('parent_comment');
        foreach ($lRoots as $comment) {
            $course = $comment->lecture ? $comment->lecture->course : null;
            if (!$course || !$comment->user || static::isStaffFor($comment->user, $course)) {
                continue;
            }
            $replies = $lChildren->get($comment->id, collect());
            $items->push([
                'key' => 'lecture-' . $comment->id,
                'kind' => 'lecture',
                'id' => $comment->id,
                'course_id' => $course->id,
                'student' => $comment->user,
                'content' => $comment->content,
                'created_at' => $comment->created_at ?: Carbon::now(),
                'context' => 'Bài giảng: ' . $comment->lecture->title . ' (' . $course->title . ')',
                'link' => url('/courses/' . $course->id . '/lectures/' . $comment->lecture_id),
                'replies' => $replies->map(function ($r) use ($course) {
                    return ['name' => $r->user ? $r->user->name : 'Ẩn danh', 'content' => $r->content, 'at' => $r->created_at, 'staff' => static::isStaffFor($r->user, $course)];
                })->values(),
                'answered' => $replies->contains(function ($r) use ($course) {
                    return static::isStaffFor($r->user, $course);
                }),
            ]);
        }

        // 3) Thảo luận trong bài giảng: tin của học viên sau tin cuối của giảng viên là "chưa trả lời"
        $discussions = Discussion::with(['user', 'lecture.course'])->whereIn('lecture_id', $lectureIds)->orderBy('id')->get()->groupBy('lecture_id');
        foreach ($discussions as $lectureId => $messages) {
            $lecture = $messages->first()->lecture;
            $course = $lecture ? $lecture->course : null;
            if (!$course) {
                continue;
            }
            $lastStaffId = $messages->filter(function ($m) use ($course) {
                return static::isStaffFor($m->user, $course);
            })->max('id') ?: 0;
            foreach ($messages as $m) {
                if (!$m->user || static::isStaffFor($m->user, $course)) {
                    continue;
                }
                $items->push([
                    'key' => 'discussion-' . $m->id,
                    'kind' => 'discussion',
                    'id' => $lectureId,
                    'course_id' => $course->id,
                    'student' => $m->user,
                    'content' => $m->content,
                    'created_at' => $m->created_at ?: Carbon::now(),
                    'context' => 'Thảo luận bài giảng: ' . $lecture->title . ' (' . $course->title . ')',
                    'link' => url('/courses/' . $course->id . '/lectures/' . $lectureId),
                    'replies' => collect(),
                    'answered' => $m->id < $lastStaffId,
                ]);
            }
        }

        return $items->sortByDesc(function ($i) {
            return $i['created_at'];
        })->values();
    }

    /** Số mục chưa trả lời (hiển thị ở menu bên). Lưu đệm ngắn để không truy vấn nặng mỗi trang. */
    public static function unansweredCount($user)
    {
        return Cache::remember('qa_unanswered_' . $user->id, 30, function () use ($user) {
            return static::buildItems($user)->where('answered', false)->count();
        });
    }

    public function index(Request $request)
    {
        $user = $this->ensureStaff();
        $all = static::buildItems($user);

        $status = $request->query('status', 'unanswered');
        $kind = $request->query('kind', 'all');
        $courseId = (int)$request->query('course_id', 0);

        $filtered = $all->filter(function ($i) use ($status, $kind, $courseId) {
            if ($status === 'unanswered' && $i['answered']) return false;
            if ($status === 'answered' && !$i['answered']) return false;
            if ($kind !== 'all' && $i['kind'] !== $kind) return false;
            if ($courseId && (int)$i['course_id'] !== $courseId) return false;

            return true;
        })->values();

        $page = max(1, (int)$request->query('page', 1));
        $items = new LengthAwarePaginator(
            $filtered->forPage($page, self::PER_PAGE)->values(),
            $filtered->count(),
            self::PER_PAGE,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $stats = [
            'unanswered' => $all->where('answered', false)->count(),
            'answered' => $all->where('answered', true)->count(),
            'total' => $all->count(),
        ];
        $courses = Course::whereIn('id', static::courseIdsFor($user))->orderBy('title')->get(['id', 'title']);

        return view('instructor.qa.index', compact('items', 'stats', 'courses', 'status', 'kind', 'courseId'));
    }

    public function reply(Request $request)
    {
        $user = $this->ensureStaff();
        $request->validate([
            'kind' => 'required|in:course,lecture,discussion',
            'id' => 'required|integer',
            'content' => 'required|string|max:191',
        ], [
            'content.required' => 'Vui lòng nhập nội dung trả lời.',
            'content.max' => 'Nội dung trả lời tối đa 191 ký tự.',
        ]);
        $content = trim($request->content);

        if ($request->kind === 'course') {
            $target = Comment::findOrFail($request->id);
            $root = $target->parent_comment ? Comment::findOrFail($target->parent_comment) : $target;
            $course = Course::findOrFail($root->course_id);
            $this->assertOwnsCourse($user, $course->id);
            // Trang công khai in nội dung bình luận không lọc, nên lưu bản đã mã hóa
            $reply = Comment::create(['content' => e($content), 'parent_comment' => $root->id, 'course_id' => $course->id, 'user_id' => $user->id]);
            if ($root->user_id !== $user->id) {
                Notification::create([
                    'type' => Notification::COMMENT,
                    'content' => '<b>' . e($user->name) . '</b> đã trả lời bình luận của bạn trong khóa học <b>' . e($course->title) . '</b>: ' . e($content),
                    'status' => Notification::NOT_SEEN,
                    'course_id' => $course->id,
                    'comment_id' => $reply->id,
                    'user_id' => $root->user_id,
                ]);
            }
        } elseif ($request->kind === 'lecture') {
            $target = LectureComment::findOrFail($request->id);
            $root = $target->parent_comment ? LectureComment::findOrFail($target->parent_comment) : $target;
            $lecture = Lecture::findOrFail($root->lecture_id);
            $this->assertOwnsCourse($user, $lecture->course_id);
            $reply = LectureComment::create(['content' => e($content), 'parent_comment' => $root->id, 'lecture_id' => $lecture->id, 'user_id' => $user->id]);
            if ($root->user_id !== $user->id) {
                Notification::create([
                    'type' => Notification::LECTURE_COMMENT,
                    'content' => '<b>' . e($user->name) . '</b> đã trả lời bình luận của bạn trong bài giảng <b>' . e($lecture->title) . '</b>: ' . e($content),
                    'status' => Notification::NOT_SEEN,
                    'lecture_id' => $lecture->id,
                    'comment_id' => $reply->id,
                    'user_id' => $root->user_id,
                ]);
            }
        } else {
            $lecture = Lecture::findOrFail($request->id);
            $this->assertOwnsCourse($user, $lecture->course_id);
            $reply = Discussion::create(['content' => $content, 'lecture_id' => $lecture->id, 'user_id' => $user->id]);
            try {
                event(new GetDiscussionFromPusherEvent($reply->content, $user->id, $reply->id));
            } catch (\Throwable $e) {
                \Log::warning('Pusher discussion broadcast failed: ' . $e->getMessage());
            }
        }

        Cache::forget('qa_unanswered_' . $user->id);

        return response()->json(['success' => true, 'reply' => ['name' => $user->name, 'content' => $content, 'time' => 'vừa xong']]);
    }
}

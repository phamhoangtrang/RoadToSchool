<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Lecture;
use App\Models\LectureComment;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $modelNotification;

    public function __construct(Notification $notification)
    {
        $this->modelNotification = $notification;
    }

    public function changeStatus(Request $request, $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)->findOrFail($id);
        $notification->update(['status' => Notification::SEEN]);
        $redirectUrl = route('notifications.index');

        if ($notification->course_id && $notification->comment_id) {
            $comment = Comment::where('course_id', $notification->course_id)
                ->findOrFail($notification->comment_id);
            $anchorId = $comment->parent_comment ?: $comment->id;
            $redirectUrl = route('courses.show', $notification->course_id).'#li-comment-'.$anchorId;
        } elseif ($notification->lecture_id && $notification->comment_id) {
            $lecture = Lecture::findOrFail($notification->lecture_id);
            $comment = LectureComment::where('lecture_id', $lecture->id)
                ->findOrFail($notification->comment_id);
            $anchorId = $comment->parent_comment ?: $comment->id;
            $redirectUrl = url("/courses/{$lecture->course_id}/lectures/{$lecture->id}").'#li-comment-'.$anchorId;
        }

        return response()->json(['redirect_url' => $redirectUrl]);
    }

    public function index()
    {
        $notificationQuery = $this->modelNotification->where('user_id', \Auth::user()->id)->orderBy('created_at', 'desc');
        $notificationList = $notificationQuery->paginate(12);
        $unreadNotificationCount = $notificationQuery->where('status', 0)->count();

        return view('user.notifications.index', compact(
            'notificationList',
            'unreadNotificationCount'
        ));
    }
}

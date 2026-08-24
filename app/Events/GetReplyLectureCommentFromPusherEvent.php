<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class GetReplyLectureCommentFromPusherEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $content;

    public $user;

    public $createdComment;

    public $parentCommentId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request, $createdComment, $parentCommentId)
    {
        $this->content = $createdComment->content;
        $this->user = json_encode(Auth::user());
        $this->createdComment = json_encode($createdComment);
        $this->parentCommentId = $parentCommentId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['replyLectureComment'];
    }
}

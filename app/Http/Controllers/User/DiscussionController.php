<?php

namespace App\Http\Controllers\User;

use App\Events\GetDiscussionFromPusherEvent;
use App\Http\Controllers\Controller;
use App\Models\Discussion;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    public $modelDiscussion;

    public function __construct(Discussion $discussion)
    {
        $this->modelDiscussion = $discussion;
    }

    public function createNewDiscussion(Request $request)
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:255'],
            'lectureId' => ['required', 'integer', 'exists:lectures,id'],
        ]);
        $data['content'] = e($data['content']);
        $data['userId'] = $request->user()->id;
        $createdDiscussion = $this->modelDiscussion->createNewDiscussion($data);

        if ($createdDiscussion) {
            event(new GetDiscussionFromPusherEvent($createdDiscussion->content, $request->user()->id, $createdDiscussion->id));

            return response()->noContent(201);

        }

        return response()->noContent(500);
    }
}

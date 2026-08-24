<?php

namespace App\Http\Controllers\User;

use App\Events\GetConversationMessageFromPusherEvent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    protected $modelConversation;

    protected $modelConversationMessage;

    public function __construct(Conversation $conversation, ConversationMessage $conversationMessage)
    {
        $this->modelConversation = $conversation;
        $this->modelConversationMessage = $conversationMessage;
    }

    public function store(Request $request)
    {
        $data = $request->validate(['content' => ['required', 'string', 'max:255']]);
        $data['content'] = e($data['content']);
        $data['user_sender_id'] = $request->user()->id;
        $findConversation = $this->modelConversation->where('user_sender_id', $request->user()->id)->first();
        $data['status'] = $this->modelConversation::WAITING;
        $is_in_progress = 0;
        if ($findConversation) {
            $createdMessage = $this->modelConversationMessage->create([
                'content' => $data['content'],
                'from_id' => \Auth::user()->id,
                'conversation_id' => $findConversation->id,
            ]);
            $responseData['createdConversation'] = $findConversation;
            if ($findConversation->status == Conversation::DONE) {
                $is_in_progress = 0;
            } elseif ($findConversation->status == Conversation::IN_PROGRESS) {
                $is_in_progress = 1;
            }
        } else {
            $createdConversation = $this->modelConversation->create($data);
            $createdMessage = $this->modelConversationMessage->create([
                'content' => $data['content'],
                'from_id' => \Auth::user()->id,
                'conversation_id' => $createdConversation->id,
            ]);
            $responseData['createdConversation'] = $createdConversation;
            $is_in_progress = 0;
        }
        event(new GetConversationMessageFromPusherEvent($createdMessage, $is_in_progress));
        $responseData['message'] = $createdMessage;
        $responseData['created_time'] = $createdMessage->created_at->toTimeString();
        $responseData['sender'] = \Auth::user();

        return json_encode($responseData);
    }

    // For admin
    public function storeNewMessage(Request $request, $conversationId)
    {
        $data = $request->validate(['content' => ['required', 'string', 'max:255']]);
        $conversation = $this->modelConversation->findOrFail($conversationId);
        abort_unless($request->user()->is_admin || $conversation->user_sender_id === $request->user()->id, 403);

        $createdMessage = $this->modelConversationMessage->create([
            'content' => e($data['content']),
            'from_id' => \Auth::user()->id,
            'conversation_id' => $conversationId,
        ]);

        event(new GetConversationMessageFromPusherEvent($createdMessage, 1));

        return 201;
    }

    public function changeConversationStatus(Request $request, $conversationId)
    {
        abort_unless($request->user()->is_admin, 403);
        $data = $request->validate(['status' => ['required', 'integer', 'in:0,1,2']]);
        $findConversation = $this->modelConversation->findOrFail($conversationId);
        $findConversation->update(['status' => $data['status'], 'admin_receiver_id' => \Auth::user()->id]);
        $responseData['moveStatus'] = $data['status'];

        return 201;
    }
}

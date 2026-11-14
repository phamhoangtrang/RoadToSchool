<?php

namespace App\Http\Controllers\User;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\User;
use App\Services\OpenAiChatService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\GetConversationMessageFromPusherEvent;

class ConversationController extends Controller
{
    protected $modelConversation;
    protected $modelConversationMessage;
    protected $aiChatService;

    public function __construct(Conversation $conversation, ConversationMessage $conversationMessage, OpenAiChatService $aiChatService)
    {
        $this->modelConversation = $conversation;
        $this->modelConversationMessage = $conversationMessage;
        $this->aiChatService = $aiChatService;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $currentUser = \Auth::user();
        $userMessageContent = trim($data['content'] ?? '');

        if ($userMessageContent === '') {
            return response()->json(['error' => 'Tin nhắn không được để trống'], 422);
        }

        $findConversation = $this->modelConversation->where('user_sender_id', $currentUser->id)->first();
        $data['status'] = $this->modelConversation::WAITING;
        $is_in_progress = 0;
        $conversation = null;

        if ($findConversation) {
            $conversation = $findConversation;
            $createdMessage = $this->modelConversationMessage->create([
                'content' => $userMessageContent,
                'from_id' => $currentUser->id,
                'conversation_id' => $conversation->id
            ]);
            $responseData['createdConversation'] = $conversation;
            if ($conversation->status == Conversation::DONE) {
                $is_in_progress = 0;
            } else if ($conversation->status == Conversation::IN_PROGRESS) {
                $is_in_progress = 1;
            }
        } else {
            $createdConversation = $this->modelConversation->create([
                'user_sender_id' => $currentUser->id,
                'status' => $this->modelConversation::WAITING,
            ]);
            $conversation = $createdConversation;
            $createdMessage = $this->modelConversationMessage->create([
                'content' => $userMessageContent,
                'from_id' => $currentUser->id,
                'conversation_id' => $conversation->id
            ]);
            $responseData['createdConversation'] = $conversation;
            $is_in_progress = 0;
        }

        try {
            event(new GetConversationMessageFromPusherEvent($createdMessage, $is_in_progress));
        } catch (\Exception $e) {
            // Bỏ qua lỗi Pusher nếu chưa cấu hình Pusher key
        }

        $responseData['message'] = $createdMessage;
        $responseData['created_time'] = $createdMessage->created_at->toTimeString();
        $responseData['sender'] = $currentUser;

        // Xử lý AI tự động phản hồi cho học viên
        $aiReplyMessage = null;
        try {
            // Lấy ID tài khoản Admin đại diện để trả lời
            $adminUser = User::where('is_admin', 1)->orWhere('role', 1)->first();
            $adminSenderId = $adminUser ? $adminUser->id : 1;

            // Lấy 6 tin nhắn gần nhất làm ngữ cảnh
            $recentMessages = $this->modelConversationMessage
                ->where('conversation_id', $conversation->id)
                ->latest()
                ->take(6)
                ->get()
                ->reverse()
                ->map(function ($m) use ($currentUser) {
                    return [
                        'content' => $m->content,
                        'is_admin' => ($m->from_id != $currentUser->id),
                    ];
                })->toArray();

            // Gọi AI sinh câu trả lời
            $aiText = $this->aiChatService->reply($userMessageContent, $recentMessages, $currentUser);

            if (!empty($aiText)) {
                $aiReplyMessage = $this->modelConversationMessage->create([
                    'content' => $aiText,
                    'from_id' => $adminSenderId,
                    'conversation_id' => $conversation->id
                ]);

                try {
                    event(new GetConversationMessageFromPusherEvent($aiReplyMessage, 1));
                } catch (\Exception $e) {
                    // Ignore pusher error
                }

                $responseData['ai_reply'] = [
                    'id' => $aiReplyMessage->id,
                    'content' => $aiReplyMessage->content,
                    'created_time' => $aiReplyMessage->created_at->toTimeString(),
                    'sender_name' => 'Trợ lý AI RoadToSchool',
                    'sender_avatar' => asset('assets/img/logo-short.png'),
                ];
            }
        } catch (\Exception $e) {
            \Log::error('AI reply error: ' . $e->getMessage());
        }

        return response()->json($responseData);
    }

    public function storeNewMessage(Request $request, $conversationId)
    {
        $data = $request->all();
        $createdMessage = $this->modelConversationMessage->create([
            'content' => $data['content'],
            'from_id' => \Auth::user()->id,
            'conversation_id' => $conversationId
        ]);
        $conversation = $this->modelConversation->findOrFail($conversationId);
        if ($conversation->status == Conversation::WAITING) {
            $is_in_progress = 0;
        } else {
            $is_in_progress = 1;
        }
        event(new GetConversationMessageFromPusherEvent($createdMessage, $is_in_progress));
        $responseData['message'] = $createdMessage;
        $responseData['created_time'] = $createdMessage->created_at->toTimeString();
        $responseData['sender'] = \Auth::user();

        return json_encode($responseData);
    }

    public function changeConversationStatus(Request $request, $conversationId)
    {
        $data = $request->all();
        $findConversation = $this->modelConversation->findOrFail($conversationId);
        $findConversation->update(['status' => $data['status'], 'admin_receiver_id' => \Auth::user()->id]);
        $responseData['moveStatus'] = $data['status'];

        return 201;
    }
}

<?php

namespace App\Services\Communication;

use App\Http\Requests\VerificationCodeRequest;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Subscription;
use App\Repositories\ChatRepository;
use App\Repositories\SubscriptionRepository;
use App\Services\Real_Time\PusherServices;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class ChatServices
{
    protected ChatRepository $chatRepository;
    protected SubscriptionRepository $subscriptionRepository;
    protected PusherServices $pusherServices;
    public function __construct(ChatRepository $chatRepository, SubscriptionRepository $subscriptionRepository, PusherServices $pusherServices)
    {
        $this->chatRepository = $chatRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->pusherServices = $pusherServices;
    }

    public function getChatForSubscription(int $subscriptionId): array
    {
        if (!$this->subscriptionRepository->ifSubscriptionActive($subscriptionId)) {
            return [
                'success' => false,
                'message' => "Subscription not active",
            ];
        }
        $chat = $this->chatRepository->getChatBySubscriptionId($subscriptionId);
        if (!$chat) {
            if ($this->subscriptionRepository->ifSubscriptionActive($subscriptionId))
                $chat = $this->chatRepository->createChatForSubscription($subscriptionId);

            else return [
                'success' => false,
                'message' => "Subscription not active",
            ];
        }
        return [
            'success' => true,
            'message' => "Chat created",
            'chat' => $chat,
        ];
    }

    public function sendMessage(array $message): array
    {
        $chat = $this->chatRepository->findChatById($message['chat_id']);
        if (!$chat) {
            return [
                'success' => false,
                'message' => "Chat was not found",
            ];
        }
//        if (!$this->hasAccessToChat($chat, $message['sender_id']))
//        {
//            return [
//                'success' => false,
//                'message' => "Access denied",
//            ];
//        }

        if (!$this->subscriptionRepository->ifSubscriptionActive($chat->subscription_id)){
            return [
                'success' => false,
                'message' => "Subscription not active",
            ];
        }

        $message['encrypted_message'] = $this->encryptMessage($message['message'], $chat->encrypted_key);
        $sendMessage = $this->chatRepository->sendMessage($message);

        $this->pusherServices->sendMessage($sendMessage, $chat);
        $this->sendPusherNotification($sendMessage, $chat);

        return [
            'success' => true,
            'message' => "Message sent",
            'message_sent' => $sendMessage,
        ];
    }

    public function markMessageAsRead(int $chatId, int $userId): array
    {
        $chat = $this->chatRepository->findChatById($chatId);
//        if (!$chat || !$this->hasAccessToChat($chat, $userId))
//            return [
//                'success' => false,
//                'message' => "Access denied",
//            ];

        $result = $this->chatRepository->markMessageAsRead($chatId, $userId);
        if ($result) {
            $this->pusherServices->markMessageAsRead($chatId, $userId);
        }
        return [
            'success' => true,
            'message' => "Message marked",
            'message_read' => $result,
        ];
    }

    public function getUserActiveChats(int $userId): array
    {
        $subscriptions = $this->subscriptionRepository->getUserActiveSubscriptions($userId);

        $chats = [];

        foreach ($subscriptions as $subscription) {
            $chat = $this->chatRepository->getChatBySubscriptionId($subscription->id);

            if ($chat) {
                $otherParticipant = $this->getOtherParticipant($chat, $userId);
                $lastMessage = $this->getLastMessage($chat);

                $chats[] = [
                    'id' => $chat->id,
                    'subscription_id' => $subscription->id,
                    'participant' => [
                        'id' => $otherParticipant->id,
                        'name' => $otherParticipant->name,
                        'avatar' => $otherParticipant->avatar_url,
                        'is_online' => $otherParticipant->isOnline(),
                        'last_seen' => $otherParticipant->last_seen_at
                    ],
                    'last_message' => $lastMessage ? [
                        'id' => $lastMessage->id,
                        'message' => $this->decryptMessage($lastMessage->encrypted_message, $chat->encrypted_key),
                        'sender_id' => $lastMessage->sender_id,
                        'created_at' => $lastMessage->created_at
                    ] : null,
                    'unread_count' => $this->chatRepository->getUnreadCount($chat->id, $userId),
                    'last_message_at' => $chat->last_message_at,
                    'subscription_status' => $subscription->status,
                    'subscription_expire_date' => $subscription->expire_date
                ];
            }
        }

        return [
            'success' => true,
            'chats' => $chats,
        ];
    }

    public function getChatMessages(int $chatId, int $userId, int $page = 1): array
    {
        $chat = $this->chatRepository->findChatById($chatId);
//        if (!$chat || !$this->hasAccessToChat($chat, $userId)){
//            return [
//                'success' => false,
//                'message' => "Access denied",
//            ];
//        }
        $messages = $this->chatRepository->getChatMessages($chatId, 50);
        $decryptedMessages = collect($messages->items())->map(function ($message) use ($chat) {
            return [
                'id' => $message->id,
                'message' => $this->decryptMessage($message->encrypted_message, $chat->encrypted_key),
                'sender' => $message->sender,
                'created_at' => $message->created_at,
                'is_read' => $message->is_read,
                'read_at' => $message->read_at,
            ];
        });
        $markAsRead = $this->chatRepository->markMessageAsRead($chatId, $userId);
        return [
            'messages' => $decryptedMessages,
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ],
            'chat_info' => [
                'id' => $chat->id,
                'subscription_id' => $chat->subscription_id,
                'participant' => $chat->isActive,
                'subscription_status' => $chat->subscription->status ?? 'Expired'
            ],
            'mark_as_read' => $markAsRead,
        ];
    }

//    public function deleteMessage(int $userId, int $messageId): array
//    {
//        $message = $this->messageRe
//    }

//    public function setTyp()
//    {
//
//    }

    private function hasAccessToChat(Chat $chat, int $userId): bool
    {
        return $chat->trainer_id === $userId || $chat->trainee_id === $userId;
    }

    private function getOtherParticipant(Chat $chat, int $userId)
    {
        if ($chat->trainer_id === $userId) {
            return $chat->subscription->trainee;
        }
        return $chat->subscription->trainer;
    }

    private function encryptMessage(string $message, string $key): string
    {
        return Crypt::encryptString($message);
    }

    private function decryptMessage(?string $encryptedMessage, string $key): ?string
    {
        if (!$encryptedMessage) return null;

        try {
            return Crypt::decryptString($encryptedMessage);
        } catch (Exception $e) {
            Log::error('Message decryption failed: ' . $e->getMessage());
            return '[Message could not be decrypted]';
        }
    }

    private function sendPusherNotification(ChatMessage $message, Chat $chat): void
    {
        $otherParticipant = $this->getOtherParticipant($chat, $message->sender_id);
        $notificationDate = [
            'title' => 'New Message from' . $message->sender->first_name,
            'body' => substr($message->message, 0, 100),
            'chat_id' => $chat->id,
            'sender_id' => $message->sender_id
        ];
    }

    private function getLastMessage(Chat $chat): ?ChatMessage
    {
        return ChatMessage::where('chat_id', $chat->id)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}

<?php

namespace App\Services\Communication;

use App\Repositories\ChatRepository;
use App\Repositories\SubscriptionRepository;
use App\Services\Real_Time\PusherServices;

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
        $chat = $this->chatRepository->findChatById($message['chatId']);
        if (!$chat) {
            return [
                'success' => false,
                'message' => "Chat was not found",
            ];
        }
        if (!$this->hasAccessToChat($chat, $message['sender_id']))
        {
            return [
                'success' => false,
                'message' => "Access denied",
            ];
        }

        if (!$this->subscriptionRepository->ifSubscriptionActive($chat->subscriptionId)){
            return [
                'success' => false,
                'message' => "Subscription not active",
            ];
        }

        $message['encrypted_message'] = $this->encryptMessage($message['message']);
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
        if (!$chat || !$this->hasAccessToChat($chat, $userId))
            return [
                'success' => false,
                'message' => "Access denied",
            ];

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

//    public function getActiveChats(int $userId): array
//    {
//        $subscription = $this->getUserActiveSubscriptions($userId);
//
//        $chats = [];
//
//        foreach ($subscription as $subscription) {
//            $chats = $this->chatRepository->f
//        }
//    }
}

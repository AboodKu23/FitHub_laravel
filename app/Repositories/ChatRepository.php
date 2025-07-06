<?php

namespace App\Repositories;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Subscription;
use Illuminate\Pagination\LengthAwarePaginator;

class ChatRepository
{
    public function createChatForSubscription(int $subscriptionId)
    {
        $subscription = Subscription::with(['trainer', 'trainee'])->find($subscriptionId);

        if (!$subscription) {
            throw new \Exception('Subscription not found');
        }

        return Chat::create([
            'trainer_id' => $subscription->trainer_id,
            'trainee_id' => $subscription->trainee_id,
            'subscription_id' => $subscriptionId,
            'is_active' => true,
            'encrypted_key' => $this->generateEncryptionKey(),
            'last_message_at' => now()
        ]);
    }

    public function generateEncryptionKey(): string
    {
        return base64_encode(random_bytes(32));
    }

    public function findChatById(int $chatId)
    {
        return Chat::with(['trainer', 'trainee', 'subscription'])
            ->where('is_active', true)
            ->find($chatId);
    }

    public function updateChat(int $chatId, array $data)
    {
        return Chat::where('id', $chatId)
            ->update($data);
    }

    public function getChatBySubscriptionId(int $subscriptionId): ?Chat
    {
        return Chat::where('subscription_id', $subscriptionId)
            ->where('is_active', true)
            ->with(['subscription.trainer', 'subscription.trainee'])
            ->first();
    }

    public function getChatMessages(int $chatId, int $perPage = 50): LengthAwarePaginator
    {
        return ChatMessage::where('chat_id', $chatId)
            ->with(['sender'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function sendMessage(array $data): ChatMessage
    {
        $message = ChatMessage::create([
            'chat_id' => $data['chat_id'],
            'sender_id' => $data['sender_id'],
            'message' => $data['message'] ?? null,
            'encrypted_message' => $data['encrypted_message'] ?? null,
            'is_read' => false
        ]);

        $this->updateChat($data['chat_id'], [
            'last_message_at' => now()
        ]);

        return $message->load('sender');
    }

    public function markMessageAsRead(int $chatId, int $userId): ChatMessage
    {
        return ChatMessage::where('chat_id', $chatId)
            ->where('sender_id', '!=' ,$userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    public function getUnreadCount(int $chatId, int $userId): int
    {
        return ChatMessage::where('chat_id', $chatId)
            ->where('sender_id', '!=' ,$userId)
            ->where('is_read', false)
            ->count();
    }

}

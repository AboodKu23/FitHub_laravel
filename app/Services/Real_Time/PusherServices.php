<?php

namespace App\Services\Real_Time;

use App\Models\Chat;
use App\Models\ChatMessage;
use Exception;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;
use Throwable;

class PusherServices
{
    private Pusher $pusher;

    public function __construct()
    {
        $this->pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'useTLS' => config('broadcasting.connections.pusher.options.useTls', true),
                'encrypted' => true
            ]
        );
    }

    public function sendMessage(ChatMessage $message, Chat $chat):bool
    {
        try {
            $channelName = $this->getChatChannelName($chat->id);
            $data = [
                'id' => $message->id,
                'chat_id' => $message->chat_id,
                'sender_id' => $message->sender_id,
                'sender' => [
                    'id' => $message->sender->id,
                    'first_name' => $message->sender->first_name,
                    'profile_image' => $message->sender()->profile_image
                    ],
                'message' => $message->message,
                'created_at' => $message->created_at->toISOString(),
                'is_read' => false
            ];

            $this->pusher->trigger($channelName, 'message.sent', $data);

            $this->sendToUserChannel($chat->trainee_id, 'new.message', [
                'chat_id' => $chat->id,
                'from_user_id' => $message->sender_id,
                'created_at' => $message->created_at->toISOString()
            ]);
            return true;
        }
        catch (Throwable $e) {
            Log::error('Failed to send message via Pusher: ' . $e->getMessage(), [
                'message_id' => $message->id,
                'chat_id' => $chat->id
            ]);
            return false;
        }
    }

    public function markMessageAsRead(int $chatId, int $userId): bool
    {
        try {
            $channelName = $this->getChatChannelName($chatId);

            $data = [
                'chat_id' => $chatId,
                'user_id' => $userId,
                'read_at' => now()->toISOString()
            ];

            $this->pusher->trigger($channelName, 'message.read', $data);
            return true;
        }
        catch (Throwable $e) {
            Log::error('Failed to send message via Pusher: ' . $e->getMessage(), [
                'chat_id' => $chatId,
                'user_id' => $userId
            ]);
            return false;
        }
    }

    public function userOnline(int $userId): bool
    {
        try {
            $channelName = $this->getUserPresenceChannelName($userId);

            $data = [
                'user_id' => $userId,
                'status' => 'online',
                'timestamp' => now()->toISOString()
            ];

            $this->pusher->trigger($channelName, 'user.online', $data);
            return true;

        }
        catch (Throwable $e) {
            Log::error('Failed to send message via Pusher: ' . $e->getMessage(), [
                'user_id' => $userId,

            ]);
            return false;
        }
    }

    public function userOffline(int $userId): bool
    {
        try {
            $channelName = $this->getUserPresenceChannelName($userId);

            $data = [
                'user_id' => $userId,
                'status' => 'offline',
                'last_seen' => now()->toISOString()
            ];

            $this->pusher->trigger($channelName, 'user.offline', $data);
            return true;

        } catch (Throwable $e) {
            Log::error('Failed to set user offline via Pusher: ' . $e->getMessage(), [
                'user_id' => $userId
            ]);
            return false;
        }
    }

    public function sendToUserChannel(int $userId, string $event, array $data): bool
    {
        try {
            $channelName = $this->getUserChannelName($userId);
            $this->pusher->trigger($channelName, $event, $data);
            return true;

        } catch (Throwable $e) {
            Log::error('Failed to send to user channel via Pusher: ' . $e->getMessage(), [
                'user_id' => $userId,
                'event' => $event
            ]);
            return false;
        }
    }

    public function getChatPresence(int $chatId): array
    {
        try {
            $channelName = $this->getChatChannelName($chatId);
            $response = $this->pusher->getPresenceUsers($channelName);

            return $response->users ?? [];

        } catch (Throwable $e) {
            Log::error('Failed to get chat presence via Pusher: ' . $e->getMessage(), [
                'chat_id' => $chatId
            ]);
            return [];
        }
    }

    public function authenticateUser(string $socketId, string $channelName, int $userId): bool
    {
        try {
            if (!$this->userHasChannelAccess($channelName, $userId)) {
                throw new Exception('User does not have access to this channel');
            }

            $userData = [
                'id' => $userId,
                'info' => [
                    'name' => auth()->user()->name ?? 'User',
                    'avatar' => auth()->user()->avatar_url ?? null
                ]
            ];

            return $this->pusher->presence_auth($channelName, $socketId, $userId, $userData);

        } catch (Throwable $e) {
            Log::error('Failed to authenticate user for Pusher channel: ' . $e->getMessage(), [
                'socket_id' => $socketId,
                'channel' => $channelName,
                'user_id' => $userId
            ]);
            return false;
        }
    }

    private function userHasChannelAccess(string $channelName, int $userId): bool
    {
        if (str_starts_with($channelName, 'presence-chat.')) {
            $chatId = (int) str_replace('presence-chat.', '', $channelName);
            $chat = Chat::find($chatId);
            return $chat && $chat->hasAccess($userId);
        }

        if (str_starts_with($channelName, 'private-user.')) {
            $channelUserId = (int) str_replace('private-user.', '', $channelName);
            return $channelUserId === $userId;
        }

        return false;
    }

    private function getChatChannelName(int $chatId): string
    {
        return "presence-chat.{$chatId}";
    }

    private function getUserChannelName(int $userId): string
    {
        return "private-user.{$userId}";
    }

    private function getUserPresenceChannelName(int $userId): string
    {
        return "presence-user.{$userId}";
    }

}

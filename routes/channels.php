<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('presence-chat.{chatId}', function ($user, $chatId) {
    $chat = Chat::find($chatId);

    if ($chat && $chat->hasAccess($user->id)) {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'profile_image' => $user->profile_image,
            'user_type' => $chat->trainer_id === $user->id ? 'trainer' : 'trainee'
        ];
    }

    return false;
});

Broadcast::channel('presence-user.{userId}', function ($user, $userId) {
    return $user->id === (int) $userId;
});

Broadcast::channel('presence-user.{userId}', function ($user, $userId) {
    if ((int) $user->id === (int) $userId) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar_url,
            'status' => 'online'
        ];
    }

    return false;
});


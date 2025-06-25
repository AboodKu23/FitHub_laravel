<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostDisLiked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $postId;
    public int $dislikesCount;
    public function __construct(int $postId, int $dislikesCount)
    {
        $this->postId = $postId;
        $this->dislikesCount = $dislikesCount;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('posts');
    }

    public function broadcastAs(): string
    {
        return 'post.disliked';
    }
}

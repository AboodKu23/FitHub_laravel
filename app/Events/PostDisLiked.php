<?php

namespace App\Events;

use App\Models\Post;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostDisLiked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Post $post;
    public User $user;

    public function __construct(Post $post, User $user)
    {
        $this->post = $post;
        $this->user = $user;
    }
    public function broadcastOn(): Channel
    {
        return new Channel('posts');
    }

    public function broadcastAs(): string
    {
        return 'post.disliked';
    }

    public function broadcastWith(): array
    {
        return [
            'post_id' => $this->post->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->first_name . ' ' . $this->user->last_name,
                'profile_image' => $this->user->profile_image
            ],
            'dislikes_count' => $this->post->dislikes()->count()
        ];
    }
}

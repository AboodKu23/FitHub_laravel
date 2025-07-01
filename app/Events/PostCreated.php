<?php

namespace App\Events;

use App\Models\Post;
use Exception;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PostCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $postId;
    public $postData;

    public function __construct(Post $post)
    {
        $this->postId = $post->id;

        $this->postData = [
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
            'imageUrl' => $post->imageUrl,
            'published_at' => $post->published_at,
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('posts'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'post.created';
    }

    public function broadcastWith(): array
    {
        try {
            $post = Post::with(['publisher.user', 'likes'])
                ->find($this->postId);

            if (!$post) {
                throw new Exception('Post not found');
            }

            return [
                'post_id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'imageUrl' => $post->imageUrl,
                'published_at' => $post->published_at->toISOString(),
                'publisher' => [
                    'id' => $post->publisher->id,
                    'name' => ($post->publisher->user->first_name ?? '') . ' ' . ($post->publisher->user->last_name ?? ''),
                    'profile_image' => $post->publisher->user->profile_image ?? null,
                ],
                'likes_count' => $post->likes->count(),
            ];

        } catch (Exception $e) {
            Log::error('Error in PostCreated broadcastWith: ' . $e->getMessage());

            return [
                'post_id' => $this->postData['id'],
                'title' => $this->postData['title'],
                'content' => $this->postData['content'],
                'imageUrl' => $this->postData['imageUrl'],
                'published_at' => $this->postData['published_at'],
                'publisher' => [
                    'id' => null,
                    'name' => 'Unknown User',
                    'profile_image' => null,
                ],
                'likes_count' => 0,
            ];
        }
    }
}

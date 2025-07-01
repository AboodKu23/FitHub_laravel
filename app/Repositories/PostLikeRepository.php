<?php

namespace App\Repositories;

use App\Models\PostDislike;
use App\Models\PostLike;

class PostLikeRepository
{
    public function create(array $postLike)
    {
        return PostLike::create($postLike);
    }

    public function delete(PostLike $postLike)
    {
        return $postLike->delete();
    }

    public function getUserPostLikes(int $postId, int $userId): ?PostLike
    {
        return PostLike::where([
            'post_id' => $postId,
            'user_id' => $userId
        ])->first();
    }

    public function isPostLikedByUser(int $postId, int $userId): bool
    {
        return PostLike::where([
            'post_id' => $postId,
            'user_id' => $userId
        ])->exists();
    }

    public function getPostLikes(int $postId): array
    {
        return PostLike::with('user:id,first_name,last_name,profile_image')
            ->where('post_id', $postId)
            ->get()
            ->map(function ($like) {
                return [
                    'id' => $like->user->id,
                    'name' => $like->user->first_name . ' ' . $like->user->last_name,
                    'profile_image' => $like->user->profile_image
                ];
            })
            ->toArray();
    }
}

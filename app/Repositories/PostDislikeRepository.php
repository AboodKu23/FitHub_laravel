<?php

namespace App\Repositories;

use App\Models\PostDislike;

class PostDislikeRepository
{
    public function create(array $data)
    {
        return PostDislike::create($data);
    }

    public function delete(PostDislike $postDislike)
    {
        return $postDislike->delete();
    }

    public function getUserPostDislikes(int $postId, int $userId): ?PostDisLike
    {
        return PostDisLike::where([
            'post_id' => $postId,
            'user_id' => $userId
        ])->first();
    }

    public function isPostDislikedByUser(int $postId, int $userId): bool
    {
        return PostDislike::where([
            'post_id' => $postId,
            'user_id' => $userId
        ])->exists();
    }

    public function getPostDislikes(int $postId): array
    {
        return PostDislike::with('user:id,first_name,last_name,profile_image')
            ->where('post_id', $postId)
            ->get()
            ->map(function ($Dislike) {
                return [
                    'id' => $Dislike->user->id,
                    'name' => $Dislike->user->first_name . ' ' . $Dislike->user->last_name,
                    'profile_image' => $Dislike->user->profile_image
                ];
            })
            ->toArray();
    }
}

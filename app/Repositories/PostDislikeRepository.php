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

    public function getUserPostDislikes(int $postId, int $userId): PostDisLike
    {
        return PostDisLike::where([
            'post_id' => $postId,
            'user_id' => $userId
        ])->first();
    }
}

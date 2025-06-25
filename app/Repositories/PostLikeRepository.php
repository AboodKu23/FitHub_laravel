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

    public function getUserPostLikes(int $postId, int $userId): PostLike
    {
        return PostLike::where([
            'post_id' => $postId,
            'user_id' => $userId
        ])->first();
    }


}

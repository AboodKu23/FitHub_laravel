<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\PostDislike;
use App\Models\PostLike;
use App\Models\Trainer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostRepository
{
    public function create(array $post)
    {
        return Post::create($post);
    }

    public function update(Post $post ,array $data): bool
    {
        return $post->update($data);
    }

    public function delete(Post $post): bool
    {
        return $post->delete();
    }

    public function getPostById(int $postId): Post
    {
        return Post::where('id', $postId)->firstOrFail();
    }

    public function getAllPosts(int $perPage = 10): LengthAwarePaginator
    {
        return Post::with([
            'publisher.user:id,first_name,last_name,profile_image',
        ])->withCount([
            'likes as likes_count',
            'dislikes as dislikes_count',
        ])->when(Auth::check(), function (Builder $builder) {
            $userId = Auth::id();

            $builder->addSelect([
                'is_liked' => PostLike::select(DB::raw('1'))
                ->whereColumn('post_id', 'posts.id')
                ->where('user_id', $userId)
                ->limit(1),

                'is_disliked' => PostDislike::select(DB::raw('1'))
                ->whereColumn('post_id', 'posts.id')
                ->where('user_id', $userId)
                ->limit(1),
            ]);
        })->select('posts.*')
            ->latest()
            ->paginate($perPage);
    }

    public function isPostPublisher(Post $post, int $trainerId): bool
    {
        if ($post->publisher_id === $trainerId) {
            return true;
        }
        return false;
    }

    public function getTrainerPosts(Trainer $trainer, int $perPage = 10): LengthAwarePaginator
    {
        return Post::with([
            'publisher.user:id,first_name,last_name,profile_image',
        ])->where('publisher_id', $trainer->id)
            ->withCount([
                'likes as likes_count',
                'dislikes as dislikes_count',
            ])->when(Auth::check(), function (Builder $builder) {
                $userId = Auth::id();
                $builder->addSelect([
                    'is_liked' => PostLike::select(DB::raw('1'))
                    ->whereColumn('post_id', 'posts.id')
                    ->where('user_id', $userId)
                    ->limit(1),

                    'is_disliked' => DB::table('post_dislikes')
                    ->select(DB::raw('1'))
                    ->whereColumn('post_id', 'posts.id')
                    ->where('user_id', $userId)
                    ->limit(1),
                ]);
            })->select('posts.*')
            ->latest()
            ->paginate($perPage);
    }
}

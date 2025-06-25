<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\PostDislike;
use App\Models\PostLike;
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

//    public function toggleLike(Post $post, int $userId): array
//    {
//
//
//        if($like){
//            $like->delete();
//            $isLiked = false;
//        }
//        else{
//            PostDislike::create([
//                'post_id' => $post->id,
//                'user_id' => $userId,
//            ]);
//            $isLiked = true;
//            $post->post_likes_count	+= 1;
//        }
//        return [
//            'isLiked' => $isLiked,
//            'likes_count' => $post->likes()->count()
//        ];
//    }

//    public function toggleDislike(Post $post, int $userId): array
//    {
//        $dislikes = PostDislike::where([
//            'post_id' => $post->id,
//            'user_id' => $userId,
//        ])->first();
//
//        if($dislikes){
//            $dislikes->delete();
//            $isDisliked = false;
//        }
//        else{
//            PostDislike::create([
//                'post_id' => $post->id,
//                'disli'
//            ]);
//        }
//    }

}

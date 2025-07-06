<?php

namespace App\Services\Community;

use App\Events\PostDisLiked;
use App\Events\PostLiked;
use App\Models\User;
use App\Repositories\PostDislikeRepository;
use App\Repositories\PostLikeRepository;
use App\Repositories\PostRepository;
use App\Repositories\TrainerRepository;

class PostServices
{
    protected PostRepository $postRepository;
    protected PostLikeRepository $postLikeRepository;
    protected PostDislikeRepository $postDislikeRepository;

    public function __construct(PostRepository $postRepository, PostLikeRepository $postLikeRepository, PostDislikeRepository $postDislikeRepository)
    {
        $this->postRepository = $postRepository;
        $this->postLikeRepository = $postLikeRepository;
        $this->postDislikeRepository = $postDislikeRepository;
    }

    public function likePost(int $postId, int $userId):array
    {
        $post = $this->postRepository->getPostById($postId);
        $dislikeUser = $this->postDislikeRepository->getUserPostDislikes($postId,$userId);
        if ($dislikeUser->exists)
        {
            $this->postDislikeRepository->delete($dislikeUser);
        }

        $isLikedByUser = $this->postLikeRepository->isPostLikedByUser($postId, $userId);
        if(!$isLikedByUser){
            $this->postLikeRepository->create([
                'post_id' => $postId,
                'user_id' => $userId,
            ]);
            $user = User::where('id', $userId)->first();
            broadcast(new PostLiked($post,$user));
        }
        return [
            'success' => true,
            'message' => 'Post has been liked',
            'isLiked' => true,
            'likes_count' => $post->likes()->count(),
        ];
    }

    public function unlikePost(int $postId, int $userId):array
    {
        $post = $this->postRepository->getPostById($postId);
        $postLike = $this->postLikeRepository->getUserPostLikes($postId, $userId);
        $this->postLikeRepository->delete($postLike);

        return [
            'success' => true,
            'message' => 'Post has been unliked',
            'isLiked' => false,
            'likes_count' => $post->likes()->count(),
        ];
    }

    public function dislikePost(int $postId, int $userId):array
    {
        $post = $this->postRepository->getPostById($postId);
        $likeUser = $this->postLikeRepository->getUserPostLikes($postId,$userId);
        if ($likeUser->exists)
        {
            $this->postLikeRepository->delete($likeUser);
        }

        $isDislikedByUser = $this->postDislikeRepository->isPostDislikedByUser($postId,$userId);
        if(!$isDislikedByUser){
            $this->postDislikeRepository->create([
                'post_id' => $postId,
                'user_id' => $userId,
            ]);
            $user = User::where('id', $userId)->first();
            broadcast(new PostDisliked($post,$user));
        }
        return [
            'success' => true,
            'message' => 'Post has been disliked',
            'isDisliked' => true,
            'Dislikes_count' => $post->dislikes()->count(),
        ];
    }

    public function unDislikePost(int $postId, int $userId):array
    {
        $post = $this->postRepository->getPostById($postId);
        $postDislike = $this->postDislikeRepository->getUserPostDislikes($postId, $userId);
        $this->postDislikeRepository->delete($postDislike);

        return [
            'success' => true,
            'message' => 'Post has been unliked',
            'isDisliked' => false,
            'Dislikes_count' => $post->dislikes()->count(),
        ];
    }

    public function getAllPosts():array
    {
        $posts = $this->postRepository->getAllPosts();
        if (!$posts) {
            return [
                'success' => false,
                'message' => 'No posts found'
            ];
        }

        $formattedPosts = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'image' => $post->image ? asset($post->image) : null,
                'likes_count' => $post->likes_count ?? $post->likes()->count(),
                'dislikes_count' => $post->dislikes_count ?? $post->dislikes()->count(),
                'is_liked' => $post->is_liked ?? false,
                'is_disliked' => $post->is_disliked ?? false,
                'published_at' => $post->published_at,
                'publisher' => [
                    'id' => $post->publisher->id,
                    'name' => $post->publisher->user->first_name . ' ' . $post->publisher->user->last_name,
                    'profile_image' => $post->publisher->user->profile_image,
                ],
            ];
        });

        return [
            'success' => true,
            'posts' => $formattedPosts
        ];
    }

}

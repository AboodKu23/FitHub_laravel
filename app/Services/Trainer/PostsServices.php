<?php

namespace App\Services\Trainer;

use App\Events\PostCreated;
use App\Events\PostDisLiked;
use App\Events\PostLiked;
use App\Models\Post;
use App\Models\User;
use App\Repositories\PostDislikeRepository;
use App\Repositories\PostLikeRepository;
use App\Repositories\PostRepository;
use App\Repositories\TrainerRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\isNull;

class PostsServices
{
    protected PostRepository $postRepository;
    protected PostLikeRepository $postLikeRepository;
    protected PostDislikeRepository $postDislikeRepository;
    protected TrainerRepository $trainerRepository;

    public function __construct(PostRepository $postRepository, PostLikeRepository $postLikeRepository, PostDislikeRepository $postDislikeRepository, TrainerRepository $trainerRepository)
    {
        $this->postRepository = $postRepository;
        $this->postLikeRepository = $postLikeRepository;
        $this->postDislikeRepository = $postDislikeRepository;
        $this->trainerRepository = $trainerRepository;
    }

    public function createNewPost(array $postData, int $trainerId):array
    {
        if (isset($postData['image']))
        {
            $path = $postData['image']->store('posts', 'public');
            $postData['image'] = Storage::url($path);
        }

        $postData['publisher_id'] = $trainerId;
        $postData['published_at'] = now();
        $post = $this->postRepository->create($postData);
        if (!$post) {
            return [
                'success' => false,
                'message' => 'Post not created'
            ];
        }
        broadcast(new PostCreated($post));
        return [
            'success' => true,
            'message' => 'Post has been created'
        ];
    }

    public function deleteTrainerPost(int $postId, int $trainerId):array
    {
        $post = $this->postRepository->getPostById($postId);
        if(!$this->postRepository->isPostPublisher($post,$trainerId)){
            throw new AuthorizationException('You are not allowed to delete this post');
        }

        if ($post->image)
        {
            $relativePath = str_replace('/storage/', '', $post->image);
            Storage::disk('public')->delete($relativePath);
        }

        $this->postRepository->delete($post);
        return [
            'success' => true,
            'message' => 'Post has been deleted'
        ];
    }

    public function updateTrainerPost(int $postId,  int $trainerId, array $postData):array
    {
        $post = $this->postRepository->getPostById($postId);
        if (!$this->postRepository->isPostPublisher($post,$trainerId)) {
            throw new AuthorizationException('You are not allowed to update this post');
        }

        if (isset($postData['image'])) {
            if ($post->image)
            {
                $relativePath = str_replace('/storage/', '', $post->imageUrl);
                Storage::disk('public')->delete($relativePath);
            }
            $path = $postData['image']->store('posts', 'public');
            $postData['image'] = Storage::url($path);
        }

        $this->postRepository->update($post,$postData);
        return [
            'success' => true,
            'message' => 'Post has been updated'
        ];
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

    public function getTrainerPosts(int $trainerId):array
    {
        $trainer = $this->trainerRepository->getTrainerById($trainerId);
        if (!$trainer) {
            return [
                'success' => false,
                'message' => 'Trainer not found'
            ];
        }
        $trainerPosts = $this->postRepository->getTrainerPosts($trainer);
        if (!$trainerPosts) {
            return [
                'success' => false,
                'message' => 'No posts found'
            ];
        }
        $formattedPosts = $trainerPosts->map(function ($post) {
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
            'posts' => $trainerPosts
        ];
    }

    public function getPostLikeDetails(int $postId):array
    {
        $likes = $this->postLikeRepository->getPostLikes($postId);
        if (!$likes) {
            return [
                'success' => false,
                'message' => 'no post like'
            ];
        }
        return [
            'success' => true,
            'postLike' => $likes
        ];
    }

    public function getPostDislikeDetails(int $postId):array
    {
        $dislikes = $this->postDislikeRepository->getPostDislikes($postId);
        if (!$dislikes) {
            return [
                'success' => false,
                'message' => 'no post dislike'
            ];
        }
        return [
            'success' => true,
            'postDislike' => $dislikes
        ];
    }
}

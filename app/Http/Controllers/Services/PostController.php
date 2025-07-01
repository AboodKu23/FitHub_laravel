<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Services\Trainer\PostsServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class PostController extends Controller
{
    protected PostsServices $postsServices;
    public function __construct(PostsServices $postsServices)
    {
        $this->postsServices = $postsServices;
    }

    public function likePost(int $postId): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user)
            {
                return response()->json([
                    'success' => false,
                    'message' => 'User not logged in.',
                ]);
            }
            $response = $this->postsServices->likePost($postId, $user->id);
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            Log::error('Failed to get requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get requests.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function unlikePost(int $postId): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user)
            {
                return response()->json([
                    'success' => false,
                    'message' => 'User not logged in.',
                ]);
            }
            $response = $this->postsServices->unlikePost($postId, $user->id);
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            Log::error('Failed to get requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get requests.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function dislikePost(int $postId): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user)
            {
                return response()->json([
                    'success' => false,
                    'message' => 'User not logged in.',
                ]);
            }
            $response = $this->postsServices->dislikePost($postId, $user->id);
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            Log::error('Failed to get requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get requests.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function unDislikePost(int $postId): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user)
            {
                return response()->json([
                    'success' => false,
                    'message' => 'User not logged in.',
                ]);
            }
            $response = $this->postsServices->unDislikePost($postId, $user->id);
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            Log::error('Failed to get requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get requests.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getAllPosts(): JsonResponse
    {
        try {
            $response = $this->postsServices->getAllPosts();
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            Log::error('Failed to get requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get requests.',
                'error' => $e->getMessage(),
            ]);
        }
    }

}

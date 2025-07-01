<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostsRequest;
use App\Http\Requests\UpdatePostsRequest;
use App\Models\Post;
use App\Services\Trainer\PostsServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class ManagePostController extends Controller
{
    protected PostsServices $postsServices;

    public function __construct(PostsServices $postsServices)
    {
        $this->postsServices = $postsServices;
    }

    public function createPost(StorePostsRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $trainer = $user->trainer()->first();
            $response = $this->postsServices->createNewPost($request->validated(), $trainer->id);

            return response()->json($response, 201);
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

    public function deleteTrainerPost(int $postId): JsonResponse
    {
        try {
            $user = Auth::user();
            $trainer = $user->trainer()->first();

            $response = $this->postsServices->deleteTrainerPost($postId, $trainer->id);

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

    public function UpdateTrainerPost(UpdatePostsRequest $request, int $postId): JsonResponse
    {
        try {
            $user = Auth::user();
            $trainer = $user->trainer()->first();

            $response = $this->postsServices->updateTrainerPost($postId, $trainer->id, $request->validated());
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

    public function getTrainerPosts(): JsonResponse
    {
        try {
            $user = Auth::user();
            $trainer = $user->trainer()->first();

            $response = $this->postsServices->getTrainerPosts($trainer->id);
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

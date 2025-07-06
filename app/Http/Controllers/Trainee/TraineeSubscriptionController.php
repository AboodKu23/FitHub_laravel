<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Services\Trainee\TraineeSubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class TraineeSubscriptionController extends Controller
{
    protected TraineeSubscriptionService $traineeSubscriptionService;

    public function __construct(TraineeSubscriptionService $traineeSubscriptionService)
    {
        $this->traineeSubscriptionService = $traineeSubscriptionService;
    }

    public function getTraineeSubscription(): JsonResponse
    {
        try {
            $user = Auth::user();
            $trainee = $user->trainee()->first();
            $response = $this->traineeSubscriptionService->getActiveSubscription($trainee->id);

            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            Log::error('Failed to cancel subscription requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel subscription requests.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getTodayTraineeExercises(): JsonResponse
    {
        try {
            $user = Auth::user();
            $trainee = $user->trainee()->first();

            $response = $this->traineeSubscriptionService->getTodayExercisesForTrainee($trainee->id);
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            Log::error('Failed to cancel subscription requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel subscription requests.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getCustomizedExercise(int $customizeExerciseId): JsonResponse
    {
        try {
            $response = $this->traineeSubscriptionService->getCustomizedTrainingExercises($customizeExerciseId);
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            Log::error('Failed to cancel subscription requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel subscription requests.',
                'error' => $e->getMessage(),
            ]);
        }
    }
}

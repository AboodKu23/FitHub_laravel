<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveDayExercisesRequest;
use App\Http\Requests\StoreTrainingPlanRequest;
use App\Services\Trainer\TrainingPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class TrainingPlanController extends Controller
{
    protected TrainingPlanService $trainingPlanService;

    public function __construct(TrainingPlanService $trainingPlanService)
    {
        $this->trainingPlanService = $trainingPlanService;
    }

    public function createNewTrainingPlan(StoreTrainingPlanRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $trainer = $user->trainer()->first();

            if (!$trainer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trainer not found for this user.',
                ], 404);
            }

            $validated = $request->validated();
            $validated['trainer_id'] = $trainer->id;

            $trainingPlan = $this->trainingPlanService->createNewPlan($validated);
            return response()->json([
                'data' => $trainingPlan,
            ]);
        }
        catch (Throwable $e) {
            Log::error('Failed to get requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get requests.',
                'error' => $e->getMessage(),
            ]);
        }
    } //Done

    public function deleteTrainingPlan(int $trainingPlanId): JsonResponse
    {
        try {
            $response = $this->trainingPlanService->delete($trainingPlanId);
            return response()->json([
                'data' => $response,
            ]);
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

    public function getAllTrainingPlans(): JsonResponse
    {
        try {
            $user = Auth::user();
            $trainer = $user->trainer()->first();

            $response = $this->trainingPlanService->getAllTrainingPlans($trainer->id);
            return response()->json([
                'data' => $response,
            ]);
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

    public function getTrainingPlan(int $trainingPlanId): JsonResponse
    {
        try {
            $trainingPlan = $this->trainingPlanService->getTrainingPlanById($trainingPlanId);
            return response()->json([
                'data' => $trainingPlan,
            ]);
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

<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetDayExercisesRequest;
use App\Http\Requests\SaveDayExercisesRequest;
use App\Http\Requests\UpdateDayExerciseOrderRequest;
use App\Http\Requests\UpdateExerciseDetailsRequest;
use App\Services\Trainer\TrainingPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class TrainingPlanExerciseController extends Controller
{
    protected TrainingPlanService $trainingPlanService;

    public function __construct(TrainingPlanService $trainingPlanService)
    {
        $this->trainingPlanService = $trainingPlanService;
    }

    public function saveTrainingPlan(int $trainingPlanId, SaveDayExercisesRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $response = $this->trainingPlanService->saveSelectedExercise($trainingPlanId, $validated['days']);
            return response()->json([
                $response
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

    public function getPlanExercises(int $trainingPlanId): JsonResponse
    {
        try {
            $response = $this->trainingPlanService->getPlanExercise($trainingPlanId);
            return response()->json($response);
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

    public function getPlanExerciseById(int $trainingPlanId): JsonResponse
    {
        try {
            $response = $this->trainingPlanService->getPlanExerciseById($trainingPlanId);
            return response()->json($response);
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

    public function addExerciseDetails(int $trainingPlanId, UpdateExerciseDetailsRequest $request): JsonResponse
    {
        try {
            $response = $this->trainingPlanService->addSelectedExerciseDetails($trainingPlanId, $request->validated());
            return response()->json($response);
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

    public function updateExerciseDayOrder(int $planId, UpdateDayExerciseOrderRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $response = $this->trainingPlanService->updateExerciseOrder($planId, $validated['dayNumber'], $validated['exercises']);
            return response()->json($response);
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

    public function getDayExercises(int $trainingPlanId, GetDayExercisesRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $dayExercise = $this->trainingPlanService->getExerciseByDay($trainingPlanId, $validated['dayNumber']);
            return response()->json($dayExercise);
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

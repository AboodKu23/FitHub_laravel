<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomizedExerciseRequest;
use App\Http\Requests\UpdateCustomizedExerciseRequest;
use App\Services\Trainer\CustomizedTrainingExercisesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class CustomizedTrainingExerciseController extends Controller
{
    protected CustomizedTrainingExercisesService $customizedTrainingExercisesService;

    public function __construct(CustomizedTrainingExercisesService $customizedTrainingExercisesService)
    {
        $this->customizedTrainingExercisesService = $customizedTrainingExercisesService;
    }

    public function customizeTrainingExercises(UpdateCustomizedExerciseRequest $request, int $customizedTrainingExerciseId): JsonResponse
    {
        try {
            $response = $this->customizedTrainingExercisesService->customizedTrainingExercises($customizedTrainingExerciseId, $request->validated());
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

//    public function addNewCustomizedExercise(StoreCustomizedExerciseRequest $request, int $subscriptionTrainingPlan): JsonResponse
//    {
//        try {
//            $response = $this->customizedTrainingExercisesService->addNewCustomizedTrainingExercise($subscriptionTrainingPlan,)
//        }
//    }
}

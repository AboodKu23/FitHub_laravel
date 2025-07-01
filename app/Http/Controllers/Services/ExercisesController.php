<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExercisesPageRequest;
use App\Services\Exercise\ExerciseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExercisesController extends Controller
{
    protected ExerciseService $exerciseService;

    public function __construct(ExerciseService $exerciseService)
    {
        $this->exerciseService = $exerciseService;
    }

    public function getAllExercises(ExercisesPageRequest $request): JsonResponse
    {
        try {
            $page = (int) $request->get('page', 1);
            $perPage = 50;

            if ($page < 1) {
                $page = 1;
            }

            $exercises = $this->exerciseService->getExercises($page, $perPage);
            return response()->json([
                'success' => true,
                'message' => 'Exercises list',
                'exercises' => $exercises
            ]);
        }
        catch (Throwable $e) {
            Log::error('Failed to fetch exercises: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching exercises.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSelectedExercise(int $exerciseId): JsonResponse
    {
        try {
            $exercise = $this->exerciseService->getExerciseById($exerciseId);
            if (!$exercise) {
                return response()->json([
                    'status' => false,
                    'message' => 'Exercise not found.'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Exercise fetched successfully.',
                'data' => $exercise
            ]);
        } catch (Throwable $e) {
            Log::error('Error fetching exercise: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the exercise.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

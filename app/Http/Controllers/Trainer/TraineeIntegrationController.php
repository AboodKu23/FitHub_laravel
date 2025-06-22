<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Services\Trainer\TraineeIntegrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class TraineeIntegrationController extends Controller
{
    protected  TraineeIntegrationService $traineeIntegrationService;
    public function __construct(TraineeIntegrationService $traineeIntegrationService)
    {
        $this->traineeIntegrationService = $traineeIntegrationService;
    }

    public function getTraineeInfo(int $traineeId): JsonResponse
    {
        try {
            $user = Auth::user();
            $trainer = $user->trainer()->first();

            $response = $this->traineeIntegrationService->getTraineeInfo($trainer->id , $traineeId);
            return response()->json([
                'success' => true,
                'message' => "Trainee info retrieved successfully",
                'data' => $response
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

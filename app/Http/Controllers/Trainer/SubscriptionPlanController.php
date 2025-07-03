<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignPlan;
use App\Services\Trainer\SubscriptionPlanServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class SubscriptionPlanController extends Controller
{
    protected SubscriptionPlanServices $subscriptionPlanServices;
    public function __construct(SubscriptionPlanServices $subscriptionPlanServices)
    {
        $this->subscriptionPlanServices = $subscriptionPlanServices;
    }

    public function assignPlan(AssignPlan $request, int $subscriptionId): JsonResponse
    {
        try {
            $validated = $request->validated();
            $response = $this->subscriptionPlanServices->assignPlan($subscriptionId, $validated['plan_id']);
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

    public function unassignPlan(int $subscriptionId): JsonResponse
    {
        try {
            $response = $this->subscriptionPlanServices->cancelPlan($subscriptionId);
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

    public function getSubscriptionPlan(int $subscriptionId): JsonResponse
    {
        try {
            $response = $this->subscriptionPlanServices->getSubscriptionPlan($subscriptionId);
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

    public function getSubscriptionPlanExercises(int $subscriptionId): JsonResponse
    {
        try {
            $response = $this->subscriptionPlanServices->getSubscribedTrainingExercises($subscriptionId);
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

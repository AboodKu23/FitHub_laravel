<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Services\Trainee\TraineeSubscriptionServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class TraineeSubscriptionRequestController extends Controller
{
    protected TraineeSubscriptionServices $traineeSubscriptionServices;

    public function __construct(TraineeSubscriptionServices $traineeSubscriptionServices)
    {
        $this->traineeSubscriptionServices = $traineeSubscriptionServices;
    }

    public function sendSubscriptionRequest(int $trainerId) : JsonResponse
    {
        try {
            $user = Auth::user();
            $trainee = $user->trainee()->first();
            $request = $this->traineeSubscriptionServices->sendSubscriptionRequest($trainee->id, $trainerId);

            return response()->json([
                'success' => true,
                'requests' => $request,
                'message' => 'Subscription request sent successfully.',
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to send subscription request: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send subscription request.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getTraineeSubscriptionRequests() : JsonResponse
    {
        try {
            $user = Auth::user();
            $trainee = $user->trainee()->first();
            $requests = $this->traineeSubscriptionServices->getAllRequests($trainee);

            return response()->json([
                'success' => true,
                'requests' => $requests,
                'message' => 'Subscription requests fetched successfully.',
            ]);
        }
        catch (Throwable $e) {
            Log::error('Failed to fetch subscription requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription requests.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getTraineeAcceptedSubscriptionRequests() : JsonResponse
    {
        try {
            $user = Auth::user();
            $trainee = $user->trainee()->first();
            $requests = $this->traineeSubscriptionServices->getAcceptedRequests($trainee);

            return response()->json([
                'success' => true,
                'requests' => $requests,
                'message' => 'Subscription requests fetched successfully.',
            ]);
        }

        catch (Throwable $e) {
            Log::error('Failed to fetch subscription requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription requests.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getTraineeRejectedSubscriptionRequests() : JsonResponse
    {
        try {
            $user = Auth::user();
            $trainee = $user->trainee()->first();
            $requests = $this->traineeSubscriptionServices->getRejectedRequests($trainee);

            return response()->json([
                'success' => true,
                'requests' => $requests,
                'message' => 'Subscription requests fetched successfully.',
            ]);
        }
        catch (Throwable $e) {
            Log::error('Failed to fetch subscription requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription requests.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getTrainerCancelledSubscriptionRequests() : JsonResponse
    {
        try {
            $user = Auth::user();
            $trainee = $user->trainee()->first();
            $requests = $this->traineeSubscriptionServices->getCancelledRequests($trainee);
            return response()->json([
                'success' => true,
                'requests' => $requests,
                'message' => 'Subscription requests fetched successfully.',
            ]);
        }
        catch (Throwable $e) {
            Log::error('Failed to fetch subscription requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription requests.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getActiveSubscriptionRequests() : JsonResponse
    {
        try {
            $user = Auth::user();
            $trainee = $user->trainee()->first();
            $requests = $this->traineeSubscriptionServices->getActiveRequests($trainee);

            return response()->json([
                'success' => true,
                'requests' => $requests,
                'message' => 'Subscription requests fetched successfully.',
            ]);
        }
        catch (Throwable $e) {
            Log::error('Failed to fetch subscription requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription requests.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function cancelSubscriptionRequest(int $subscriptionId) : JsonResponse
    {
        try {
            $request = $this->traineeSubscriptionServices->getSubscriptionRequestById($subscriptionId);
            $response = $this->traineeSubscriptionServices->cancelRequest($request);

            return response()->json([
                'success' => true,
                'response' => $response,
                'message' => 'Subscription cancelled successfully.',
            ]);
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

<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Services\Trainer\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function getActiveSubscriptions(): JsonResponse
    {
        try {
            $user = Auth::user();
            $trainer = $user->trainer()->first();

            $subscriptions = $this->subscriptionService->getActiveSubscriptions($trainer);
            if ($subscriptions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'There are no active subscriptions yet',
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => 'Get active subscriptions by trainer',
                'data' => $subscriptions
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

    public function getSubscription(int $subscriptionId): JsonResponse
    {
        try {
            $subscription = $this->subscriptionService->getSubscriptionsById($subscriptionId);
            if ($subscription === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription not found',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Get subscription by id',
                'data' => $subscription
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

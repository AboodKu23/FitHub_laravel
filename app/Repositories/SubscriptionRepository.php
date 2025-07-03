<?php

namespace App\Repositories;

use App\Models\Subscription;
use App\Models\Trainer;
use Illuminate\Support\Collection;

class SubscriptionRepository
{
    public function create(array $data) : Subscription
    {
        return Subscription::create($data);
    }

    public function update(Subscription $subscription, array $data) : bool
    {
        return $subscription->update($data);
    }

    public function delete(Subscription $subscription) : bool
    {
        return $subscription->delete();
    }

    public function ifHasActiveSubscription(int $traineeId, int $trainerId): bool
    {
        return Subscription::where('trainer_id', $trainerId)
            ->where('trainee_id', $traineeId)
            ->where('status', 'accepted')
            ->where('expire_date', '>=', now()->subDays(2))
            ->exists();
    }

//    public function getActiveSubscriptionBetweenTrainerAndTrainer(int $traineeId, int $trainerId): ?Subscription
//    {
//        return Subscription::where('trainer_id', $trainerId)
//            ->where('trainee_id', $traineeId)
//            ->where('status', 'accepted')
//            ->where('expire_date', '>=', now()->subDays(2))
//            ->first();
//    }

    public function getActiveSubscriptionsForTrainer(Trainer $trainer): Collection
    {
        return $trainer->subscriptions()
            ->where('status', 'Active')
            ->where('expire_date', '>=' , now())
            ->with(['trainee.user','trainingPlan'])
            ->orderBy('subscription_date', 'desc')
            ->get();
    }

    public function getSubscriptionForTrainerById(int $SubscriptionId): Subscription
    {
        return Subscription::with(['trainee.user','trainingPlan'])
            ->where('id', $SubscriptionId)
            ->first();
    }
}

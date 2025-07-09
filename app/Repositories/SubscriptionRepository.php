<?php

namespace App\Repositories;

use App\Models\Subscription;
use App\Models\Trainee;
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
            ->where('status', 'Active')
            ->where('expire_date', '>=', now()->subDays(2))
            ->exists();
    }

    public function ifSubscriptionActive(int $subscriptionId): bool
    {
        return Subscription::where('id', $subscriptionId)
            ->where('status', 'Active')
            ->where('expire_date', '>=', now())
            ->exists();
    }

    public function getUserActiveSubscriptions(int $userId)
    {
        return Subscription::where(function($query) use ($userId) {
            $query->where('trainer_id', $userId)
                ->orWhere('trainee_id', $userId);
        })
            ->where('status', 'Active')
            ->where('expire_date', '>=', now())
            ->with(['trainer', 'trainee'])
            ->get();
    }
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

    public function getActiveSubscriptionForTrainee(Trainee $trainee): ?Subscription
    {
        return $trainee->subscriptions()
            ->where('status', 'Active')
            ->where('expire_date', '>=', now())
            ->with([
                'trainer.user:id,id,first_name,last_name,email',
                'trainingPlan.trainingPlan'
            ])
            ->latest('expire_date')
            ->first();
    }

    public function getSubscriptionForTraineeWithPlanAndExercise(int $subscriptionId): Subscription
    {
        return Subscription::with('trainingPlan.customizedExercises.exercise')
            ->find($subscriptionId);
    }
}

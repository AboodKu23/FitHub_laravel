<?php

namespace App\Repositories;

use App\Models\CustomizedTrainingExercise;
use App\Models\SubscriptionTrainingPlan;
use App\Models\TrainingPlan;

class SubscriptionPlanRepository
{

    protected SubscriptionRepository $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function create(array $data): SubscriptionTrainingPlan
    {
        return SubscriptionTrainingPlan::create($data);
    }

    public function update(array $data, SubscriptionTrainingPlan $subscriptionPlan): bool
    {
        return $subscriptionPlan->update($data);
    }

    public function delete(SubscriptionTrainingPlan $subscriptionPlan): bool
    {
        return $subscriptionPlan->delete();
    }

    public function ifSubscriptionHasPlan(int $subscriptionPlanId): bool
    {
        return SubscriptionTrainingPlan::where('id', $subscriptionPlanId)
            ->whereNotNull('training_plan_id')
            ->exists();
    }

    public function assignPlanToSubscription(SubscriptionTrainingPlan $subscriptionPlan, int $planId): bool
    {
        return $subscriptionPlan->update(['training_plan_id' => $planId]);
    }

    public function cancelAssignPlanToSubscription(SubscriptionTrainingPlan $subscriptionPlan): bool
    {
        return $subscriptionPlan->update(['training_plan_id' => null]);
    }

    public function getSubscriptionPlan(int $subscriptionId): ?SubscriptionTrainingPlan
    {
        return SubscriptionTrainingPlan::with('trainingPlan')
            ->where('subscription_id', $subscriptionId)
            ->first();
    }
    public function deleteSubscriptionPlanExercise(int $subscriptionPlanId): bool
    {
        return CustomizedTrainingExercise::where('subscription_plan_id', $subscriptionPlanId)
            ->delete();
    }

    public function getSubscriptionPlanById(int $subscriptionPlanId): ?TrainingPlan
    {
        return SubscriptionTrainingPlan::where('id', $subscriptionPlanId)->first();
    }
}

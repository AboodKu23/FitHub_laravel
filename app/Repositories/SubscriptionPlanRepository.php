<?php

namespace App\Repositories;

use App\Models\SubscriptionTrainingPlan;

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

    public function assignPlanToSubscription(SubscriptionTrainingPlan $subscriptionPlan, int $planId): bool
    {
        return $subscriptionPlan->update(['training_plan_id' => $planId]);
    }

    public function cancelAssignPlanToSubscription(SubscriptionTrainingPlan $subscriptionPlan): bool
    {
        return $subscriptionPlan->update(['training_plan_id' => null]);
    }

    public function getSubscriptionPlan(SubscriptionTrainingPlan $subscriptionPlan): ?SubscriptionTrainingPlan
    {
        return $subscriptionPlan->trainingPlan()->first();
    }
}

<?php

namespace App\Services\Trainer;

use App\Models\Subscription;
use App\Repositories\CustomizedTrainingExercisesRepository;
use App\Repositories\SubscriptionPlanRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\TrainingPlanExerciseRepository;

class SubscriptionPlanServices
{
    protected SubscriptionRepository $subscriptionRepository;
    protected SubscriptionPlanRepository $subscriptionPlanRepository;
    protected TrainingPlanExerciseRepository $trainingPlanExerciseRepository;
    protected CustomizedTrainingExercisesRepository $customizedTrainingExercisesRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository, SubscriptionPlanRepository $subscriptionPlanRepository, TrainingPlanExerciseRepository $trainingPlanExerciseRepository,CustomizedTrainingExercisesRepository $customizedTrainingExercisesRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->trainingPlanExerciseRepository = $trainingPlanExerciseRepository;
        $this->customizedTrainingExercisesRepository = $customizedTrainingExercisesRepository;
    }

    public function assignPlan(int $subscriptionId, int $planId): array
    {
       $subscription = $this->subscriptionRepository->getSubscriptionForTrainerById($subscriptionId);
       if (!$subscription) {
           return [
               'success' => false,
               'message' => 'Subscription not found'
           ];
       }
       if ($this->subscriptionPlanRepository->ifSubscriptionHasPlan($subscriptionId))
       {
           return [
               'success' => false,
               'message' => 'Subscription already assigned'
           ];
       }
       $subscriptionPlan = $subscription->trainingPlan()->first();
       if (!$this->subscriptionPlanRepository->assignPlanToSubscription($subscriptionPlan, $planId))
       {
           return [
               'success' => false,
               'message' => 'Failed to assign subscription plan'
           ];
       }
       $isDeleted = $this->subscriptionPlanRepository->deleteSubscriptionPlanExercise($subscriptionPlan->id);

       $planExercise = $this->trainingPlanExerciseRepository->getAllExercisesForPlan($subscriptionPlan->training_plan_id);
        foreach ($planExercise as $exercise) {
            $this->customizedTrainingExercisesRepository->create([
                'subscription_plan_id' => $subscriptionPlan->id,
                'dayNumber' => $exercise->dayNumber,
                'exercise_id' => $exercise->exercise_id,
                'setNumber' => $exercise->setNumber,
                'resp' => $exercise->resp,
                'weightKg' => $exercise->weightKg,
                'duration' => $exercise->duration,
                'reset_duration' => $exercise->reset_duration,
                'notes' => $exercise->notes,
                'order_in_day' => $exercise->orderInDay,
            ]);
        }
       return [
           'success' => true,
           'message' => 'Subscription assigned successfully'
       ];
    }

    public function cancelPlan(int $subscriptionId): array
    {
        $subscription = $this->subscriptionRepository->getSubscriptionForTrainerById($subscriptionId);
        if (!$subscription) {
            return [
                'success' => false,
                'message' => 'Subscription not found'
            ];
        }
        if (!$this->subscriptionPlanRepository->ifSubscriptionHasPlan($subscriptionId))
        {
            return [
                'success' => false,
                'message' => 'No plan found for subscription'
            ];
        }
        $subscriptionPlan = $subscription->trainingPlan()->first();
        if (!$this->subscriptionPlanRepository->cancelAssignPlanToSubscription($subscriptionPlan))
        {
            return [
                'success' => false,
                'message' => 'Failed to cancel subscription plan'
            ];
        }
        return [
            'success' => true,
            'message' => 'Subscription cancelled successfully'
        ];
    }

    public function getSubscriptionPlan(int $subscriptionId): array
    {
        $subscription = $this->subscriptionRepository->getSubscriptionForTrainerById($subscriptionId);
        if (!$subscription) {
            return [
                'success' => false,
                'message' => 'Subscription not found'
            ];
        }

        $subscriptionPlan = $this->subscriptionPlanRepository->getSubscriptionPlan($subscriptionId);
        if (!$subscriptionPlan) {
            return [
                'success' => false,
                'message' => 'Subscription plan not found'
            ];
        }
        return [
            'success' => true,
            'plan' => $subscriptionPlan
        ];
    }

    public function getSubscribedTrainingExercises(int $subscriptionPlanId): array
    {
        $planExercise = $this->customizedTrainingExercisesRepository->getCustomizedSubscriptionPlan($subscriptionPlanId);
        if (!$planExercise) {
            return [
                'success' => false,
                'message' => 'No plan Exercise found for subscription'
            ];
        }
        return [
            'success' => true,
            'planExercise' => $planExercise
        ];
    }
}

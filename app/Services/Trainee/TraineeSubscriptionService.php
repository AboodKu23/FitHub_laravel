<?php

namespace App\Services\Trainee;

use App\Repositories\CustomizedTrainingExercisesRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\TraineeRepository;
use App\Services\Trainer\SubscriptionService;
use Carbon\Carbon;

class TraineeSubscriptionService
{
    protected TraineeRepository $traineeRepository;
    protected SubscriptionRepository $subscriptionRepository;
    protected CustomizedTrainingExercisesRepository $customizedTrainingExercisesRepository;

    public function __construct(TraineeRepository $traineeRepository, SubscriptionRepository $subscriptionRepository, CustomizedTrainingExercisesRepository $customizedTrainingExercisesRepository)
    {
        $this->traineeRepository = $traineeRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->customizedTrainingExercisesRepository = $customizedTrainingExercisesRepository;
    }

    public function getActiveSubscription(int $traineeId): array
    {
        $trainee = $this->traineeRepository->getTraineeById($traineeId);
        $subscription = $this->subscriptionRepository->getActiveSubscriptionForTrainee($trainee);
        if (!$subscription) {
            return [
                'success' => false,
                'message' => 'There is no subscription for this trainee'
            ];
        }
        return [
            'success' => true,
            'subscription' => $subscription
        ];
    }

    public function getTodayExercisesForTrainee(int $traineeId): array
    {
        $trainee = $this->traineeRepository->getTraineeById($traineeId);
        if (!$trainee) {
            return ['success' => false, 'message' => 'Trainee not found'];
        }

        $subscription = $this->subscriptionRepository->getActiveSubscriptionForTrainee($trainee);
        if (!$subscription) {
            return ['success' => false, 'message' => 'No active subscription found'];
        }

        $subscription = $this->subscriptionRepository->getSubscriptionForTraineeWithPlanAndExercise($subscription->id);
        $trainingPlan = $subscription->trainingPlan;

        if (!$trainingPlan || !$trainingPlan->start_date) {
            return ['success' => false, 'message' => 'Training plan or start date is missing'];
        }

        $startDate = Carbon::parse($trainingPlan->start_date)->startOfDay();
        $today = Carbon::today()->startOfDay();

        if ($today->lt($startDate)) {
            $daysLeft = $today->diffInDays($startDate);
            return [
                'success' => false,
                'message' => "Training plan has not started yet. Starts in {$daysLeft} day(s).",
                'days_remaining' => $daysLeft,
                'start_date' => $startDate->toDateString(),
            ];
        }

        $currentDay = $startDate->diffInDays($today) + 1;

        $maxDay = $trainingPlan->customizedExercises->max('dayNumber');
        if ($currentDay > $maxDay) {
            return ['success' => false, 'message' => "No exercises for day {$currentDay}"];
        }

        $exercisesToday = $trainingPlan->customizedExercises
            ->where('dayNumber', $currentDay)
            ->sortBy('order_in_day')
            ->values();

        return [
            'success' => true,
            'day' => $currentDay,
            'exercises' => $exercisesToday,
        ];
    }

    public function getCustomizedTrainingExercises(int $customisedTrainingExerciseId): array
    {
        $customisedTrainingExercise = $this->customizedTrainingExercisesRepository->getCustomizedTrainingExercise($customisedTrainingExerciseId);
        if (!$customisedTrainingExercise) {
            return [
                'success' => false,
                'message' => 'There is no customized exercise for this trainee'
            ];
        }
        return [
            'success' => true,
            'exercises' => $customisedTrainingExercise
        ];
    }

}

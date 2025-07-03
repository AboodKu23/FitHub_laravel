<?php

namespace App\Services\Trainer;

use App\Repositories\CustomizedTrainingExercisesRepository;
use App\Repositories\SubscriptionPlanRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\TrainingPlanExerciseRepository;

class CustomizedTrainingExercisesService
{
    protected CustomizedTrainingExercisesRepository $customizedTrainingExercisesRepository;
    protected SubscriptionPlanRepository $subscriptionPlanRepository;
    protected SubscriptionRepository $subscriptionRepository;

    public function __construct(CustomizedTrainingExercisesRepository $customizedTrainingExercisesRepository, SubscriptionPlanRepository $subscriptionPlanRepository, SubscriptionRepository $subscriptionRepository)
    {
        $this->customizedTrainingExercisesRepository = $customizedTrainingExercisesRepository;
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function customizedTrainingExercises(int $customizedTrainingExerciseId, array $data): array
    {
        $isCustomized = $this->customizedTrainingExercisesRepository->customizePlanExercise($customizedTrainingExerciseId, $data);
        if (!$isCustomized) {
            return [
                'success' => false,
                'message' => 'Customized Training Exercise not found'
            ];
        }
        return [
            'success' => true,
            'message' => 'Customized Training Exercise found',
        ];
    }

//    public function addNewCustomizedTrainingExercise(int $subscriptionPlanId,array $data, int $dayNumber): array
//    {
//        $result = $this->customizedTrainingExercisesRepository->addCustomizedTrainingExercise($subscriptionPlanId, $data, $dayNumber);
//        if (!$result) {
//            return [
//                'success' => false,
//                'message' => 'Customized Training Exercise not found'
//            ];
//        }
//        return [
//            'success' => true,
//            'message' => 'Customized Training Exercise added',
//        ];
//    }
//
//    public function deleteCustomizedTrainingExercise(int $customizedTrainingExerciseId): array
//    {
//        $isDeleted = $this->customizedTrainingExercisesRepository->deleteCustomizedTrainingExercise($customizedTrainingExerciseId);
//        if (!$isDeleted) {
//            return [
//                'success' => false,
//                'message' => 'Customized Training Exercise not deleted'
//            ];
//        }
//        return [
//            'success' => true,
//            'message' => 'Customized Training Exercise deleted successfully',
//        ];
//    }
}

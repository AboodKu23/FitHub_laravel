<?php

namespace App\Services\Trainer;

use App\Models\Trainer;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanExercise;
use App\Repositories\ExerciseRepository;
use App\Repositories\TrainerRepository;
use App\Repositories\TrainingPlanExerciseRepository;
use App\Repositories\TrainingPlanRepository;
use Illuminate\Support\Collection;

class TrainingPlanService
{
    protected  TrainingPlanExerciseRepository $trainingPlanExerciseRepository;
    protected  TrainingPlanRepository $trainingPlanRepository;
    protected ExerciseRepository $exerciseRepository;
    protected TrainerRepository $trainerRepository;

    public function __construct(TrainingPlanExerciseRepository $trainingPlanExerciseRepository, TrainingPlanRepository $trainingPlanRepository, ExerciseRepository $exerciseRepository,TrainerRepository $trainerRepository)
    {
        $this->trainingPlanExerciseRepository = $trainingPlanExerciseRepository;
        $this->trainingPlanRepository = $trainingPlanRepository;
        $this->exerciseRepository = $exerciseRepository;
        $this->trainerRepository = $trainerRepository;
    }

    public function createNewPlan(array $data): array
    {
        $trainingPlan = $this->trainingPlanRepository->create($data);
        if (!$trainingPlan) {
            return [
                'success' => false,
                'massage' => 'failed to create training plan',
            ];
        }
        return [
            'success' => true,
            'message' => 'Training plan successfully created.',
        ];
    } //Done

    public function delete(int $trainingPlanId): array
    {
        $trainingPlan = $this->trainingPlanRepository->getTrainerTrainingPlanById($trainingPlanId);
        if (!$trainingPlan) {
            return [
                'success' => false,
                'message' => 'Training plan not found'
            ];
        }
        $isDeleted = $this->trainingPlanRepository->delete($trainingPlan);
        if (!$isDeleted) {
            return [
                'success' => false,
                'message' => 'Training plan not deleted'
            ];
        }
        return [
            'success' => true,
            'message' => 'Training plan deleted'
        ];
    } //Done

    public function getAllTrainingPlans(int $trainerId): array
    {
        $trainer = $this->trainerRepository->getTrainerById($trainerId);
        if (!$trainer) {
            return [
                'success' => false,
                'message' => 'Trainer not found'
            ];
        }
        $trainerPlans = $this->trainingPlanRepository->getAllTrainerTrainingPlans($trainer);
        if (!$trainerPlans) {
            return [
                'success' => false,
                'message' => 'No training plans found'
            ];
        }
        return [
            'success' => true,
            'trainerPlans' => $trainerPlans,
        ];
    } //Done

    public function getTrainingPlanById(int $trainingPlanId): array
    {
        $plan = $this->trainingPlanRepository->getTrainerTrainingPlanById($trainingPlanId);
        if (!$plan) {
            return [
                'success' => false,
                'message' => 'Training plan not found'
            ];
        }
        return [
            'success' => true,
            'plan' => $plan,
        ];
    } //Done



    public function saveSelectedExercise(int $trainingPlanId ,array $days): array
    {
         $this->trainingPlanExerciseRepository->saveExercise($trainingPlanId, $days);

         return [
             'success' => true,
             'message' => 'Exercise added successfully',
         ];
    } //Done

    public function getPlanExercise(int $trainingPlanId): array
    {
        $trainingPlanExercises = $this->trainingPlanExerciseRepository->getAllExercisesForPlan($trainingPlanId);
        if (!$trainingPlanExercises) {
            return [
                'success' => false,
                'message' => 'Exercise not found'
            ];
        }

        return [
            'success' => true,
            'exercises' => $trainingPlanExercises,
        ];
    } //Done

    public function getPlanExerciseById(int $trainingPlanId): array
    {
        $planExercise = $this->trainingPlanExerciseRepository->getPlanExerciseById($trainingPlanId);
        if (!$planExercise) {
            return [
                'success' => false,
                'message' => 'Exercise not found'
            ];
        }
        return [
            'success' => true,
            'exercise' => $planExercise,
        ];
    } //Done

    public function addSelectedExerciseDetails(int $planExerciseId, array $data): array
    {
        $isAdded = $this->trainingPlanExerciseRepository->addExerciseDetailsInTheTrainingPlan($planExerciseId, $data);
        if (!$isAdded) {
            return [
                'success' => false,
                'message' => 'Exercise details not added'
            ];
        }
        return [
            'success' => true,
            'message' => 'Exercise details added successfully',
        ];
    } //Done

    public function updateExerciseOrder(int $planId, int $dayNumber, array $exercises): array
    {
        $this->trainingPlanExerciseRepository->updateExercisesOrderInDay($planId, $dayNumber, $exercises);
        return [
            'success' => true,
            'message' => 'Exercise order updated successfully',
        ];
    }
    public function getExerciseByDay(int $planExerciseId, int $dayNumber): array
    {
        $exercise = $this->trainingPlanExerciseRepository->getExerciseByDayNumber($planExerciseId, $dayNumber);
        if (!$exercise) {
            return [
                'success' => false,
                'message' => 'Exercise not found'
            ];
        }
        return [
            'success' => true,
            'exercise' => $exercise,
        ];
    } //Done
}

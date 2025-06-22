<?php

namespace App\Repositories;

use App\Models\TrainingPlanExercise;
use Illuminate\Support\Collection;

class TrainingPlanExerciseRepository
{
    public function create(array $data)
    {
        return TrainingPlanExercise::create($data);
    }

    public function saveExercise(int $planId, array $days): void
    {
        foreach ($days as $day) {
            $dayNumber = $day['dayNumber'];

            TrainingPlanExercise::where('training_plan_id', $planId)
                ->where('dayNumber', $dayNumber)
                ->delete();

            foreach ($day['exercises'] as $exercise) {
                $this->create([
                    'training_plan_id' => $planId,
                    'exercise_id' => $exercise['exercise_id'],
                    'dayNumber' => $dayNumber,
                ]);
            }
        }
    }

    public function addExerciseDetailsInTheTrainingPlan(int $trainingPlanExerciseId, array $data): bool
    {
        $PlanExercise = TrainingPlanExercise::where('id', $trainingPlanExerciseId)->first();
        return $this->update($PlanExercise, ['setNumber' => $data['setNumber'] ?? null,
        'reps' => $data['reps'] ?? null,
        'weightKg' => $data['weightKg'] ?? null,
        'duration' => $data['duration'] ?? null,
        'reset_duration' => $data['reset_duration'] ?? null,
        'notes' => $data['notes'] ?? null,
            ]
        );

    }

    public function updateExercisesOrderInDay(int $planId, int $dayNumber, array $exercises): void
    {
        foreach ($exercises as $exercise) {
            TrainingPlanExercise::where('id', $exercise['id'])
                ->where('training_plan_id', $planId)
                ->where('dayNumber', $dayNumber)
                ->update(['orderInDay' => $exercise['orderInDay']]);
        }
    }

    public function update(TrainingPlanExercise $trainingPlanExercise, array $data): bool
    {
        return $trainingPlanExercise->update($data);
    }

    public function getAllExercisesForPlan(int $planId): Collection
    {
        return TrainingPlanExercise::where('training_plan_id', $planId)
            ->with('exercise')
            ->orderBy('dayNumber')
            ->orderBy('orderInDay')
            ->get();
    }

    public function getPlanExerciseById(int $trainingPlanExerciseId): TrainingPlanExercise
    {
        return TrainingPlanExercise::with('exercise')
            ->where('id', $trainingPlanExerciseId)
            ->first();
    }

    public function getExerciseByDayNumber(int $planId ,int $dayNumber): Collection
    {
        return TrainingPlanExercise::where('training_plan_id', $planId)
            ->where('dayNumber', $dayNumber)
            ->with('exercise')
            ->get();
    }

    public function existsInDay(int $planId, int $dayNumber, int $exerciseId): bool
    {
        return TrainingPlanExercise::where('training_plan_id', $planId)
            ->where('dayNumber', $dayNumber)
            ->where('exercise_id', $exerciseId)
            ->exists();
    }
}

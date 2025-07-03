<?php

namespace App\Repositories;

use App\Models\CustomizedTrainingExercise;
use App\Models\Subscription;
use App\Models\SubscriptionTrainingPlan;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\Cast\Bool_;

class CustomizedTrainingExercisesRepository
{
    public function create(array $data)
    {
       return CustomizedTrainingExercise::create($data);
    }

    public function update(CustomizedTrainingExercise $customizedTrainingExercise, array $data)
    {
        return $customizedTrainingExercise->update($data);
    }

    public function delete(CustomizedTrainingExercise $customizedTrainingExercise)
    {
        return $customizedTrainingExercise->delete();
    }

    public function customizePlanExercise(int $customizedTrainingExerciseId, array $data): bool
    {
        $customizedTrainingExercise = CustomizedTrainingExercise::find($customizedTrainingExerciseId);

        if (!$customizedTrainingExercise) {
            return false;
        }

        $fieldsToUpdate = array_intersect_key($data, array_flip([
            'setNumber',
            'resp',
            'weightKg',
            'duration',
            'reset_duration',
            'notes'
        ]));

        return $customizedTrainingExercise->update($fieldsToUpdate);
    }

    public function getCustomizedSubscriptionPlan(int $subscriptionPlanId): Collection
    {
        $subscriptionPlan = SubscriptionTrainingPlan::where('subscription_id', $subscriptionPlanId)->first();

        if (!$subscriptionPlan) {
            return collect();
        }

        return CustomizedTrainingExercise::with('exercise')
            ->where('subscription_plan_id', $subscriptionPlan->id)
            ->orderBy('dayNumber')
            ->orderBy('order_in_day')
            ->get()
            ->groupBy('dayNumber');
    }

//    public function addCustomizedTrainingExercise(int $subscriptionPlanId,array $data,int $dayNumber)
//    {
//        $subscriptionPlan = SubscriptionTrainingPlan::where('subscription_id', $subscriptionPlanId)->first();
//
//        if (!$subscriptionPlan) {
//            return null;
//        }
//        return $this->create([
//            'subscription_plan_id' => $subscriptionPlan->id,
//            'dayNumber'            => $dayNumber,
//            'exercise_id'          => $data['exercise_id'],
//            'setNumber'            => $data['setNumber'] ?? null,
//            'resp'                 => $data['resp'] ?? null,
//            'weightKg'             => $data['weightKg'] ?? null,
//            'duration'             => $data['duration'] ?? null,
//            'reset_duration'       => $data['reset_duration'] ?? null,
//            'notes'                => $data['notes'] ?? null,
//            'order_in_day'         => $data['order_in_day'] ?? null,
//        ]);
//    }
//
//    public function deleteCustomizedTrainingExercise(int $customizedTrainingExerciseId): bool
//    {
//        $customizedTrainingExercise = CustomizedTrainingExercise::where('id', $customizedTrainingExerciseId)->first();
//        if (!$customizedTrainingExercise) {
//            return false;
//        }
//        return $customizedTrainingExercise->delete();
//    }

    public function getCustomizedTrainingExercise(int $customizedTrainingExerciseId)
    {
        return CustomizedTrainingExercise::with('exercise')
            ->where('id', $customizedTrainingExerciseId)
            ->first();
    }

    public function getCustomizedTrainingExerciseByDay(int $subscriptionPlanId, int $day): Collection
    {
        $subscriptionPlan = SubscriptionTrainingPlan::where('subscription_id', $subscriptionPlanId)->first();
        if (!$subscriptionPlan) {
            return collect();
        }
        return CustomizedTrainingExercise::with('exercise')
            ->where('subscription_plan_id', $subscriptionPlan->id)
            ->where('dayNumber', $day)
            ->orderBy('order_in_day')
            ->get();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomizedTrainingExercise extends Model
{
    protected $fillable = [
        'subscription_plan_id',
        'dayNumber',
        'exercise_id',
        'setNumber',
        'resp',
        'weightKg',
        'duration',
        'reset_duration',
        'notes',
        'order_in_day',
    ];

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionTrainingPlan::class, 'subscription_plan_id');
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }
}

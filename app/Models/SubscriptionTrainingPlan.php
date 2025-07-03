<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionTrainingPlan extends Model
{
    protected $fillable = [
        'subscription_id',
        'training_plan_id',
        'start_date',
        'end_date',
        'notes'
    ];

    public function trainingPlan(): BelongsTo
    {
        return $this->belongsTo(TrainingPlan::class, 'training_plan_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function customizedExercises(): HasMany
    {
        return $this->hasMany(CustomizedTrainingExercise::class, 'subscription_plan_id');
    }

}

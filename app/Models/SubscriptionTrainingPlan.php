<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}

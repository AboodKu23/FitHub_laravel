<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionTrainingPlan extends Model
{
    protected $fillable = [
        'subscription_id',
        'training_plan_id',
        'start_date',
        'end_date',
        'notes'
    ];


}

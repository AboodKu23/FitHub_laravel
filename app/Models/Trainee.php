<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Trainee extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainee_id',
        'height',
        'weight',
        'activity_level',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function subscriptions() : HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function subscriptionRequests() : HasMany
    {
        return $this->hasMany(SubscriptionRequests::class, 'trainee_id');
    }
}

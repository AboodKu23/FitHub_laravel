<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use softDeletes;

    protected $fillable = [
        'trainer_id',
        'trainee_id',
        'subscription_id',
        'isActive',
        'last_message_at',
        'encrypted_key'
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainee_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at', 'desc');
    }

    public function unreadMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->where('is_read', false);
    }

    public function getOtherParticipant($userId)
    {
        return $this->trainer_id == $userId ? $this->trainee : $this->trainer;
    }

    public function hasAccess($userId): bool
    {
        return $this->trainer_id == $userId || $this->trainee_id == $userId;
    }
}

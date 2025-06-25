<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens,HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'profile_image',
        'user_type',
        'gender',
        'phone_number',
        'country',
        'city',
        'region',
        'address',
        'password',
        'email_verified_at',
        'verification_code',
        'email_verification_sent_at',
        'code_expires_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'email_verification_sent_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function trainer(): HasOne
    {
        return $this->hasOne(Trainer::class, 'trainer_id');
    }

    public function trainee(): HasOne
    {
        return $this->hasOne(Trainee::class, 'trainee_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class, 'user_id');
    }

    public function dislikes(): HasMany
    {
        return $this->hasMany(PostDislike::class, 'user_id');
    }

    public function messagesSent(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function messagesReceived(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function hasVerifiedEmail() : bool
    {
        return ! is_null($this->email_verified_at);
    }

    public function markEmailAsVerified() : bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
            'verification_code' => null,
            'code_expires_at' => null,
        ])->save();
    }

}

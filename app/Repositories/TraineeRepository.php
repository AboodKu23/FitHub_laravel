<?php

namespace App\Repositories;

use App\Models\Subscription;
use App\Models\Trainee;
use Illuminate\Support\Carbon;

class TraineeRepository
{
    public function create(array $data) : Trainee
    {
        return Trainee::create($data);
    }

    public function getTraineeProfileIfSubscription(int $trainerId, int $traineeId): ?Trainee
    {
        $now = Carbon::now();

        $subscription = Subscription::with(['trainee.user'])
            ->where('trainee_id', $traineeId)
            ->where('trainer_id', $trainerId)
            ->where(function ($query) use ($now) {
                $query->where('expire_date', '>=', $now)
                    ->orWhere('expire_date', '>=', $now->copy()->subDays(2));
            })
            ->latest('expire_date')
            ->first();

        return $subscription?->trainee;
    }

    public function getBasicTraineeInfoIfNoSubscription(int $traineeId) : null
    {
        $trainee = Trainee::with('user')
            ->where('trainee_id', $traineeId)
            ->first();

        if (!$trainee||!$trainee->user)
            return null;

        $user = $trainee->user;

        return [
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'email' => $user->hide_email ? null : $user->email,
            'phone_number' => $user->hide_phone_number ? null : $user->phone_number,
        ];
    }

    public function getTraineeById(int $traineeId) : Trainee
    {
        return Trainee::where('id', $traineeId)->first();
    }
}

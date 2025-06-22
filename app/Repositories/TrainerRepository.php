<?php

namespace App\Repositories;

use App\Models\Trainer;
use Illuminate\Support\Collection;

class TrainerRepository
{
    public function create(array $data) : Trainer
    {
        return Trainer::create($data);
    }

    public function update(Trainer $trainer, array $data) : bool
    {
        return $trainer->update($data);
    }

    public function delete(Trainer $trainer) : bool
    {
        return $trainer->delete();
    }

    public function getAllTrainers() : Collection
    {
        return Trainer::with('user')
            ->orderByDesc('rating')
            ->get();
    }

    public function getTrainerByIdWithCertificate(int $trainerId) : ?Trainer
    {
        return Trainer::with('verifiedCertificates')
            ->with('user')
            ->find($trainerId);
    }

    public function getTrainerById(int $trainerId) : ?Trainer
    {
        return Trainer::where('id', $trainerId)->first();
    }
}

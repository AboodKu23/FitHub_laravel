<?php

namespace App\Services\Trainee;

use App\Repositories\TrainerRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TrainersIntegrationService
{
    protected TrainerRepository $trainerRepository;

    public function __construct(TrainerRepository $trainerRepository)
    {
        $this->trainerRepository = $trainerRepository;
    }

    public function getTrainers(): array
    {
        $trainers = $this->trainerRepository->getAllTrainers();

        if ($trainers->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No trainers found.',
                'data' => []
            ];
        }

        $trainersList = $trainers->map(function ($trainer) {
            return [
                'trainer_info' => $trainer->user,
                'trainer' => $trainer
            ];
        });

        return [
            'success' => true,
            'message' => 'Trainers retrieved successfully.',
            'data' => $trainersList
        ];
    }

    public function getTrainer(int $trainerId): array
    {
        try {
            $trainer = $this->trainerRepository->getTrainerByIdWithCertificate($trainerId);

            return [
                'success' => true,
                'message' => 'Trainer retrieved successfully.',
                'data' => [
                    'trainer' => $trainer,
                    'certificates' => $trainer->verifiedCertificates
                ]
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => "Trainer with ID {$trainerId} not found."
            ];
        }
    }
}

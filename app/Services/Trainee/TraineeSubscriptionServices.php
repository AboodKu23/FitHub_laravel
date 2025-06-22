<?php

namespace App\Services\Trainee;

use App\Models\SubscriptionRequests;
use App\Models\Trainee;
use App\Models\Trainer;
use App\Repositories\SubscriptionRequestRepository;
use App\Repositories\TrainerRepository;
use Illuminate\Support\Collection;

class TraineeSubscriptionServices
{
    protected SubscriptionRequestRepository $subscriptionRequestRepository;
    protected TrainerRepository $trainerRepository;

    public function __construct(SubscriptionRequestRepository $subscriptionRequestRepository, TrainerRepository $trainerRepository)
    {
        $this->subscriptionRequestRepository = $subscriptionRequestRepository;
        $this->trainerRepository = $trainerRepository;
    }

    public function sendSubscriptionRequest(int $traineeId , int $trainerId ): array
    {
        $trainer = $this->trainerRepository->getTrainerByIdWithCertificate($trainerId);
        $trainee = Trainee::where('id', $traineeId)->firstOrFail();

        if (!$trainer) {
            return [
                'success' => false,
                'message' => "Trainer not found"
            ];
        }

        $currentMonth = now()->format('Y-m');
        $currentMonthSubscription = $this->subscriptionRequestRepository->getThisMonthActiveRequests($traineeId, $currentMonth);

        if ($currentMonthSubscription->isNotEmpty()) {
            return [
                'success' => false,
                'message' => "Invalid Request: you already have an active subscription request. You must cancel it before sending a new one."
            ];
        }

        $subscription = $this->subscriptionRequestRepository->create([
            'trainer_id' => $trainer->id,
            'trainee_id' => $trainee->id,
            'request_date' => now(),
        ]);

        return [
            'success' => true,
            'subscription' => $subscription
        ];
    }

    public function getSubscriptionRequestById(int $requestId): SubscriptionRequests
    {
        return $this->subscriptionRequestRepository->findSubscriptionRequestById($requestId);
    }

    public function getAllRequests(Trainee $trainee): Collection
    {
        return $this->subscriptionRequestRepository->getAllTraineeRequests($trainee);
    }

    public function getAcceptedRequests(Trainee $trainee): Collection
    {
        return $this->subscriptionRequestRepository->getApprovedTraineeRequests($trainee);
    }

    public function getRejectedRequests(Trainee $trainee): Collection
    {
        return $this->subscriptionRequestRepository->getRejectedTraineeRequests($trainee);
    }

    public function getCancelledRequests(Trainee $trainee): Collection
    {
        return $this->subscriptionRequestRepository->getCancelledTraineeRequests($trainee);
    }

    public function getActiveRequests(Trainee $trainee): Collection
    {
        return $this->subscriptionRequestRepository->getActiveTraineeRequests($trainee);
    }

    public function cancelRequest(SubscriptionRequests $subscriptionRequest): bool
    {
        return $this->subscriptionRequestRepository->cancelRequests($subscriptionRequest);
    }
}

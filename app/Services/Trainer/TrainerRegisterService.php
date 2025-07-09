<?php

namespace App\Services\Trainer;

use App\Notifications\VerificationCodeNotification;
use App\Repositories\CertificateRepository;
use App\Repositories\TempCertificateRepository;
use App\Repositories\TempUserRepository;
use App\Repositories\TrainerRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TrainerRegisterService
{
    protected UserRepository $userRepository;
    protected TrainerRepository $trainerRepository;
    protected TempUserRepository $tempUserRepository;
    protected TempCertificateService $tempCertificateService;
    protected CertificateService $certificateService;

    public function __construct(UserRepository $userRepository,TrainerRepository $trainerRepository,TempUserRepository $tempUserRepository,TempCertificateService $tempCertificateService ,CertificateService $certificateService)
    {
        $this->userRepository = $userRepository;
        $this->trainerRepository = $trainerRepository;
        $this->tempUserRepository = $tempUserRepository;
        $this->tempCertificateService = $tempCertificateService;
        $this->certificateService = $certificateService;
    }

    public function completeTrainerRegistration(array $trainerData):JsonResponse
    {
        $tempUser = $this->tempUserRepository->getByToken($trainerData['temp_token']);
        if(!$tempUser)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid token'
            ]);
        }
            $user = $this->userRepository->create([
                'first_name' => $tempUser->first_name,
                'last_name' => $tempUser->last_name,
                'user_type' => 'Trainer',
                'gender' => $tempUser->gender,
                'phone_number' => $tempUser->phone_number,
                'country' => $tempUser->country,
                'city' => $tempUser->city,
                'region' => $tempUser->region,
                'email' => $tempUser->email,
                'password' => $tempUser->password,
            ]);

            $trainer = $this->trainerRepository->create([
                'trainer_id' => $user->id,
                'experience_years' => $trainerData['experience_years'],
            ]);

            $tempCertificate = $this->tempCertificateService->getTempCertificate($tempUser->temp_token);

            foreach ($tempCertificate as $certificate) {
                $this->certificateService->addCertificate([
                    'trainer_id' => $trainer->id,
                    'certificate_name' => $certificate->certificate_name,
                    'issuing_organization' => $certificate->issuing_organization,
                    'issue_date' => $certificate->issue_date,
                    'expiry_date' => $certificate->expiry_date,
                    'certificate_file' => $certificate->certificate_file,
                ]);
            }

            $this->tempUserRepository->delete($tempUser);
            $this->tempCertificateService->deleteTempCertificate($tempCertificate);
            $code = $this->userRepository->generateVerificationCode($user);
            $user->notify(new VerificationCodeNotification($code));

            return response()->json([
                'status' => 'success',
                'message' => 'Registration completed successfully'
            ]);
    }
}

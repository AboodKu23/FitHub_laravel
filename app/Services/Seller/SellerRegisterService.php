<?php

namespace App\Services\Seller;

use App\Notifications\VerificationCodeNotification;
use App\Repositories\UserRepository;

class SellerRegisterService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function sellerRegister(array $sellerData): array
    {
        $user = $this->userRepository->create([
            'first_name' => $sellerData['first_name'],
            'last_name' => $sellerData['last_name'],
            'user_type' => 'Seller',
            'gender' => $sellerData['gender'],
            'phone_number' => $sellerData['phone_number'],
            'country' => $sellerData['country'],
            'city' => $sellerData['city'],
            'region' => $sellerData['region'],
            'email' => $sellerData['email'],
            'password' => $sellerData['password'],
        ]);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not created'
            ];
        }
        $code = $this->userRepository->generateVerificationCode($user);
        $user->notify(new VerificationCodeNotification($code));
        return [
            'user' => $user,
            'verify-token' => $code
        ];
    }
}

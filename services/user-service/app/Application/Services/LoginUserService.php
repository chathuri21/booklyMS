<?php

namespace App\Application\Services;

use App\Domain\DTOs\LoginUserDTO;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Services\TokenServiceInterface;
use App\Domain\Services\LoggerInterface;
use Exception;

class LoginUserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TokenServiceInterface $tokenService,
        private LoggerInterface $logger
    )
    {
        $this->logger->info('LoginUserService instantiated');
    }

    public function execute(LoginUserDTO $dto): array
    {
        $user = $this->userRepository->findByEmail($dto->email);

        if (!$user || !password_verify($dto->password, $user->password)) {
            $this->logger->info('Invalid login attempt for email: ' . $dto->email);
            throw new Exception('Invalid credentials', 401);
        }

        if (!$user->is_active) {
            $this->logger->info('Inactive account login attempt for email: ' . $dto->email);
            throw new Exception('User account is inactive', 403);
        } 

        $token = $this->tokenService->generateToken($user);

        return [
            'user' => $user,
            'access_token' => $token,
        ];
    }
}
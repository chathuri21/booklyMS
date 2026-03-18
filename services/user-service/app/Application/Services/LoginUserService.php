<?php

namespace App\Application\Services;

use App\Domain\DTOs\LoginUserDTO;
use App\Domain\Exceptions\InvalidCredentialsException;
use App\Domain\Exceptions\InactiveAccountException;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Services\TokenServiceInterface;
use App\Domain\Services\LoggerInterface;

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

        if (!$user) {
            $this->logger->info('Invalid login attempt for email: ' . $dto->email);
            throw new InvalidCredentialsException();
        }

        $user->checkPassword($dto->password);
        $user->ensureIsActive();

        $token = $this->tokenService->generateToken($user);

        return [
            'user' => $user,
            'access_token' => $token,
        ];
    }
}
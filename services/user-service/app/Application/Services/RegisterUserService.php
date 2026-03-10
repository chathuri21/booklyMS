<?php

namespace App\Application\Services;

use App\Domain\DTOs\RegisterUserDTO;
use App\Domain\Exceptions\UserAlreadyExistsException;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Services\EventDispatcherInterface;
use App\Domain\Services\LoggerInterface;
use App\Domain\Services\TokenServiceInterface;  
use App\Events\UserCreated;

class RegisterUserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EventDispatcherInterface $eventDispatcher,
        private TokenServiceInterface $tokenService,
        private LoggerInterface $logger
    )
    {
        $this->logger->info('RegisterUserService instantiated');
    }

    public function execute(RegisterUserDTO $dto): array
    {
        $existingUser = $this->userRepository->findByEmail($dto->email);

        if ($existingUser) {
            $this->logger->info('Registration attempt with existing email: ' . $dto->email);
            throw new UserAlreadyExistsException();
        }

        $user = $this->userRepository->create($dto);
  
        $token = $this->tokenService->generateToken($user);

        $this->logger->info('Dispatching UserCreated event');
        $this->eventDispatcher->dispatch(new UserCreated($user));

        return [
            'user' => $user,
            'access_token' => $token
        ];
    }
}
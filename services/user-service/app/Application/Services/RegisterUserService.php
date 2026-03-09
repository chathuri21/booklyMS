<?php

namespace App\Application\Services;

use App\Domain\DTOs\RegisterUserDTO;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Services\EventDispatcherInterface;
use App\Domain\Services\LoggerInterface;
use App\Domain\Services\TokenServiceInterface;  
use App\Events\UserCreated;

class RegisterUserService
{
    public function __construct(
        private UserRepositoryInterface $user,
        private EventDispatcherInterface $event,
        private TokenServiceInterface $token,
        private LoggerInterface $logger
    )
    {
        $this->logger->info('RegisterUserService instantiated');
    }

    public function execute(RegisterUserDTO $dto): array
    {
        $user = $this->user->create($dto);
  
        $token = $this->token->generateToken($user);

        $this->logger->info('Dispatching UserCreated event');
        $this->event->dispatch(new UserCreated($user, $this->logger));

        return [
            'message' => 'User registered successfully',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}
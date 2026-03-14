<?php

namespace Tests\Unit\Application\Services;

use App\Application\Services\LoginUserService;
use App\Domain\DTOs\LoginUserDTO;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Services\TokenServiceInterface;
use App\Domain\Services\LoggerInterface;
use App\Domain\Exceptions\InvalidCredentialsException;
use App\Domain\Exceptions\InactiveAccountException;
use App\Domain\Entities\User;
use PHPUnit\Framework\TestCase;
// use Tests\TestCase;

class LoginUserServiceTest extends TestCase
{
    private $userRepository;
    private $tokenService;
    private $logger;
    private $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->tokenService = $this->createMock(TokenServiceInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->service = new LoginUserService(
            $this->userRepository,
            $this->tokenService,
            $this->logger
        );
    }

    public function testSuccessfulLogin()
    {
        $password = 'secret123';

        $user = new User(
            email: 'test@example.com',
            password: password_hash($password, PASSWORD_BCRYPT),
            is_active: true
        );

        $dto = new LoginUserDTO(
            email: 'test@example.com',
            password: $password
        );

        $this->userRepository
            ->method('findByEmail')
            ->with($dto->email)
            ->willReturn($user);

        $this->tokenService
            ->method('generateToken')
            ->with($user)
            ->willReturn('fake-token');

        $result = $this->service->execute($dto);

        $this->assertEquals($user, $result['user']);
        $this->assertEquals('fake-token', $result['access_token']);
    }

    public function testInvalidCredentialsThrowsException()
    {
        $dto = new LoginUserDTO(
            email: 'wrong@example.com',
            password: 'wrong-password'
        );

        $this->userRepository
            ->method('findByEmail')
            ->willReturn(null);

        $this->expectException(InvalidCredentialsException::class);

        $this->service->execute($dto);
    }

    public function testInactiveAccountThrowsException()
    {
        $password = 'secret123';

        $user = new User(
            email: 'test@example.com',
            password: password_hash($password, PASSWORD_BCRYPT),
            is_active: false
        );

        $dto = new LoginUserDTO(
            email: 'test@example.com',
            password: $password
        );

        $this->userRepository
            ->method('findByEmail')
            ->willReturn($user);

        $this->expectException(InactiveAccountException::class);

        $this->service->execute($dto);
    }
}
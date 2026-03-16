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

class LoginUserServiceTest extends TestCase
{
    protected UserRepositoryInterface $userRepository;
    protected TokenServiceInterface $tokenService;
    protected LoggerInterface $logger;
    protected $service;

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

    private function makeDTO(): LoginUserDTO
    {
        return new LoginUserDTO(
            email: 'test@example.com',
            password: 'secret123'
        );
    }

    private function makeUser(string $password = 'secret123', bool $isActive = true): User
    {
        return new User(
            id: 1,
            name: 'Test User',
            email: 'test@example.com',
            phone: '1234567890',
            password: password_hash($password, PASSWORD_BCRYPT),
            role: 'customer',
            isActive: $isActive,
            eloquentUser: null
        );
    }

    public function test_login_returns_expected_structure()
    {
        $user = $this->makeUser();
        $dto = $this->makeDTO();

        $this->userRepository
            ->method('findByEmail')
            ->with($dto->email)
            ->willReturn($user);

        $this->tokenService
            ->method('generateToken')
            ->with($user)
            ->willReturn('fake-token');

        $result = $this->service->execute($dto);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('access_token', $result);
        $this->assertEquals('fake-token', $result['access_token']);
        $this->assertNotEmpty($result['access_token']);
        $this->assertNotEmpty($result['user']);
        $this->assertInstanceOf(User::class, $result['user']);
        $this->assertIsString($result['access_token']);
        $this->assertEquals('Test User', $result['user']->name);
        $this->assertEquals('1234567890', $result['user']->phone);
        $this->assertEquals('test@example.com', $result['user']->email);
        $this->assertEquals('customer', $result['user']->role);
    }

    public function test_login_generates_token_on_success(): void
    {
        $user = $this->makeUser();
        $dto = $this->makeDTO();

        $this->userRepository->method('findByEmail')->willReturn($user);

        $this->tokenService->expects($this->once())
            ->method('generateToken')
            ->with($user)
            ->willReturn('fake-token');

        $result = $this->service->execute($dto);

        $this->assertEquals('fake-token', $result['access_token']);
        
    }

    public function test_invalid_credentials_throws_exception()
    {
        $this->userRepository->method('findByEmail')->willReturn(null);

        $this->expectException(InvalidCredentialsException::class);

        $this->service->execute($this->makeDTO());
    }

    public function test_inactive_account_throws_exception()
    {
        $user = $this->makeUser(isActive: false);
        $dto = $this->makeDTO();

        $this->userRepository->method('findByEmail')->willReturn($user);

        $this->expectException(InactiveAccountException::class);

        $this->service->execute($dto);
    }
}
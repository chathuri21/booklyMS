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

    public function test_login_returns_expected_structure()
    {
        $dto = $this->makeDTO();

        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('checkPassword')->with($dto->password);
        $user->expects($this->once())->method('ensureIsActive');

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
    }

    public function test_login_generates_token_on_success(): void
    {
        $user = $this->createMock(User::class);
        $user->method('checkPassword');
        $user->method('ensureIsActive');
        
        $dto = $this->makeDTO();

        $this->userRepository->method('findByEmail')->willReturn($user);

        $this->tokenService->expects($this->once())
            ->method('generateToken')
            ->with($user)
            ->willReturn('fake-token');

        $result = $this->service->execute($dto);

        $this->assertEquals('fake-token', $result['access_token']);
        
    }

    public function test_invalid_user_throws_exception()
    {
        $this->userRepository->method('findByEmail')->willReturn(null);

        $this->expectException(InvalidCredentialsException::class);

        $this->service->execute($this->makeDTO());
    }
}
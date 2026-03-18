<?php

namespace Tests\Unit\Application\Services;

use App\Application\Services\RegisterUserService;
use App\Domain\DTOs\RegisterUserDTO;
use App\Domain\Entities\User;
use App\Domain\Exceptions\UserAlreadyExistsException;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Services\EventDispatcherInterface;
use App\Domain\Services\LoggerInterface;
use App\Domain\Services\TokenServiceInterface;
use App\Events\UserCreated;
use PHPUnit\Framework\TestCase;

class RegisterUserServiceTest extends TestCase
{
    protected LoggerInterface $logger;
    protected UserRepositoryInterface $userRepository;
    protected TokenServiceInterface $tokenService;
    protected EventDispatcherInterface $eventDispatcher;   
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->tokenService = $this->createMock(TokenServiceInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->service = new RegisterUserService(
            userRepository: $this->userRepository,
            eventDispatcher: $this->eventDispatcher,
            tokenService: $this->tokenService,
            logger: $this->logger
        );
    }

    private function makeDTO(): RegisterUserDTO
    {
        return new RegisterUserDTO(
            name: 'Test User',
            email: 'test@example.com',
            phone: '1234567890',
            password: 'password',
            role: 'customer'
        );
    }

    private function makeUser(): User
    {
        return new User(
            id: 1,
            name: 'Test User',
            email: 'test@example.com',
            phone: '1234567890',
            password: password_hash('password', PASSWORD_BCRYPT),
            role: 'customer',
            isActive: true
        );
    }

    public function test_register_returns_expected_structure(): void
    {
        $this->userRepository->method('create')->willReturn($this->makeUser());
        $this->tokenService->method('generateToken')->willReturn('test-token');
        $this->eventDispatcher->method('dispatch');

        $result = $this->service->execute($this->makeDTO());

        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('access_token', $result);
        $this->assertEquals('test-token', $result['access_token']);
        $this->assertNotEmpty($result['access_token']);
        $this->assertNotEmpty($result['user']);
        $this->assertInstanceOf(User::class, $result['user']);
        $this->assertIsString($result['access_token']);
        $this->assertEquals('Test User', $result['user']->name);
        $this->assertEquals('1234567890', $result['user']->phone);
        $this->assertEquals('test@example.com', $result['user']->email);
        $this->assertEquals('customer', $result['user']->role);
    }

    public function test_repository_create_is_called(): void
    {
        $dto = $this->makeDTO();
        $user = $this->makeUser();

        $this->userRepository->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willReturn($user);

        $this->tokenService->method('generateToken')->willReturn('test-token');
        $this->eventDispatcher->method('dispatch');

        $this->service->execute($dto);
    }

    public function test_register_generates_token_on_success(): void
    {
        $dto = $this->makeDTO();
        $user = $this->makeUser();

        $this->userRepository->expects($this->once())->method('create')->willReturn($user);

        $this->tokenService->expects($this->once())
            ->method('generateToken')
            ->with($user)
            ->willReturn('test-token');

        $this->eventDispatcher->method('dispatch');

        $result = $this->service->execute($dto);

        $this->assertEquals('test-token', $result['access_token']);
    }

    public function test_register_dispatches_event_on_success(): void
    {
        $dto = $this->makeDTO();

        $this->userRepository->method('create')->willReturn($this->makeUser());
        $this->tokenService->method('generateToken')->willReturn('test-token');

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(UserCreated::class)); 

        $this->service->execute($dto);
    }

    public function test_user_created_event_contains_correct_user(): void
    {
        $dto = $this->makeDTO();
        $user = $this->makeUser();

        $this->userRepository->method('create')->willReturn($user);
        $this->tokenService->method('generateToken')->willReturn('test-token');

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($event) use ($user) {
                return $event instanceof UserCreated && $event->user->email === $user->email;
            }));

        $this->service->execute($dto);

    }

    public function test_register_throws_exception_on_failure(): void
    {
        $dto = $this->makeDTO();

        $this->userRepository->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willThrowException(new \Exception('Database error')); 
        
        $this->tokenService->expects($this->never())->method('generateToken');

        $this->eventDispatcher->expects($this->never())->method('dispatch');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Database error');    

        $this->service->execute($dto);
    }

    public function test_register_throws_exception_when_user_already_existes() : void 
    {
        $dto = $this->makeDTO();
        $existingUser = $this->makeUser();

        $this->userRepository->expects($this->once())
            ->method('findByEmail')
            ->with($dto->email)
            ->willReturn($existingUser);
            
        $this->userRepository->expects($this->never())->method('create');

        $this->tokenService->expects($this->never())->method('generateToken');

        $this->eventDispatcher->expects($this->never())->method('dispatch');

        $this->expectException(UserAlreadyExistsException::class);
        
        $this->service->execute($dto);
    }
}

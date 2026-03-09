<?php

namespace Tests\Unit\Application\Services;

use App\Domain\DTOs\RegisterUserDTO;
use App\Models\User;
use App\Application\Services\RegisterUserService;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Services\EventDispatcherInterface;
use App\Domain\Services\LoggerInterface;
use App\Domain\Services\TokenServiceInterface;
use App\Events\UserCreated;
use PHPUnit\Framework\TestCase;

class RegisterUserServiceTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_register_returns_expected_structure(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $tokenService = $this->createMock(TokenServiceInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
    
        $dto = new RegisterUserDTO(
            name: 'Test User',
            email: 'test@example.com',
            phone: '1234567890',
            password: 'password',
            role: 'customer'
        );

        $user = new User([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'role' => 'customer'
        ]);

        $userRepository->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willReturn($user);

        $tokenService->expects($this->once())
            ->method('generateToken')
            ->willReturn('test-token');

        $eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(UserCreated::class));


        $service = new RegisterUserService(
            userRepository: $userRepository,
            eventDispatcher: $eventDispatcher,
            tokenService: $tokenService,
            logger: $logger
        );

        $result = $service->execute($dto);

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
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $tokenService = $this->createMock(TokenServiceInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
    
        $dto = new RegisterUserDTO(
            name: 'Test User',
            email: 'test@example.com',
            phone: '1234567890',
            password: 'password',
            role: 'customer'
        );

        $user = new User([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'role' => 'customer'
        ]);

        $userRepository->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willReturn($user);

        $service = new RegisterUserService(
            userRepository: $userRepository,
            eventDispatcher: $eventDispatcher,
            tokenService: $tokenService,
            logger: $logger
        );
        
        $result = $service->execute($dto);
    }

    public function test_register_generates_token_on_success(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $tokenService = $this->createMock(TokenServiceInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
    
        $dto = new RegisterUserDTO(
            name: 'Test User',
            email: 'test@example.com',
            phone: '1234567890',
            password: 'password',
            role: 'customer'
        );

        $tokenService->expects($this->once())
            ->method('generateToken')
            ->willReturn('test-token');

        $service = new RegisterUserService(
            userRepository: $userRepository,
            eventDispatcher: $eventDispatcher,
            tokenService: $tokenService,
            logger: $logger
        );

        $result = $service->execute($dto);
        $this->assertEquals('test-token', $result['access_token']);
    }

    public function test_register_dispatches_event_on_success(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $tokenService = $this->createMock(TokenServiceInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
    
        $dto = new RegisterUserDTO(
            name: 'Test User',
            email: 'test@example.com',
            phone: '1234567890',
            password: 'password',
            role: 'customer'
        );

        $eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(UserCreated::class));    

        $service = new RegisterUserService(
            userRepository: $userRepository,
            eventDispatcher: $eventDispatcher,
            tokenService: $tokenService,
            logger: $logger
        );

        $service->execute($dto);
    }
    public function test_user_created_event_contains_correct_user(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $tokenService = $this->createMock(TokenServiceInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
    
        $dto = new RegisterUserDTO(
            name: 'Test User',
            email: 'test@example.com',
            phone: '1234567890',
            password: 'password',
            role: 'customer'
        );

        $user = new User([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'role' => 'customer'
        ]);

        $eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($event) use ($user) {
                return $event instanceof UserCreated && $event->user->id === $user->id;
            }));

        $service = new RegisterUserService(
            userRepository: $userRepository,
            eventDispatcher: $eventDispatcher,
            tokenService: $tokenService,
            logger: $logger
        );
        $service->execute($dto);

    }

    public function test_register_throws_exception_on_failure(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $tokenService = $this->createMock(TokenServiceInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
    
        $dto = new RegisterUserDTO(
            name: 'Test User',
            email: 'test@example.com',
            phone: '1234567890',
            password: 'password',
            role: 'customer'
        );

        $userRepository->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willThrowException(new \Exception('Database error')); 

        $service = new RegisterUserService(
            userRepository: $userRepository,
            eventDispatcher: $eventDispatcher,
            tokenService: $tokenService,
            logger: $logger
        );  

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Database error');    

        $service->execute($dto);
    }
}

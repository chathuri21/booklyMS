<?php

namespace App\Providers;

use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Services\EventDispatcherInterface;
use App\Domain\Services\TokenServiceInterface;
use App\Domain\Services\LoggerInterface;
use App\Exceptions\Handler;
use App\Infrastructure\Persistence\EloquentUserRepository;
use App\Infrastructure\Events\LaravelEventDispatcher;   
use App\Infrastructure\Auth\SanctumTokenService;
use App\Infrastructure\Logging\LaravelLogger;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(EventDispatcherInterface::class, LaravelEventDispatcher::class);
        $this->app->bind(TokenServiceInterface::class, SanctumTokenService::class);
        $this->app->bind(LoggerInterface::class, LaravelLogger::class);
        $this->app->bind(ExceptionHandler::class, Handler::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

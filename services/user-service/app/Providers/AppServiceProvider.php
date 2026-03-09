<?php

namespace App\Providers;

use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Services\EventDispatcherInterface;
use App\Domain\Services\TokenServiceInterface;
use App\Domain\Services\LoggerInterface;
use App\Infrastructure\Persistence\EloquentUserRepository;
use App\Infrastructure\Events\LaravelEventDispatcher;   
use App\Infrastructure\Auth\SanctumTokenService;
use App\Infrastructure\Logging\LaravelLogger;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        // $this->app->singleton(LoggerInterface::class, function ($app) {
        //     return $app->make('log');
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

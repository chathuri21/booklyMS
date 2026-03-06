<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Domain\User\Events\UserCreated;
use App\Infrastructure\Messaging\Listeners\PublishUserCreated;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // ->withMiddleware(function (Middleware $middleware): void {
    //     $middleware->group('api', [
    //         \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    //         'throttle:api',
    //         \Illuminate\Routing\Middleware\SubstituteBindings::class,
    //     ]);
    //     $middleware->throttleApi(function (Request $request) {
    //         return Limit::perMinute(60)->by($request->ip());
    //     });

    // })
    ->withMiddleware(function (Middleware $middleware): void {

        // $middleware->statefulApi();

        $middleware->api();

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withEvents(discover: true)
    ->create();

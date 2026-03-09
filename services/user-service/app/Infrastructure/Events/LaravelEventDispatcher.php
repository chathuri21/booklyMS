<?php

namespace App\Infrastructure\Events;

use App\Domain\Services\EventDispatcherInterface;  

class LaravelEventDispatcher implements EventDispatcherInterface
{
    public function dispatch(object $event): void
    {
        event($event);
    }
}
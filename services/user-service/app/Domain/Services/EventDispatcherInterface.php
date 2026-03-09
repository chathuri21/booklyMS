<?php

namespace App\Domain\Services;                

interface EventDispatcherInterface
{
    public function dispatch(object $event): void;
}

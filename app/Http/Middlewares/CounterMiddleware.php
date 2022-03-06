<?php

namespace App\Http\Middlewares;

use RTC\Contracts\Http\MiddlewareInterface;
use RTC\Contracts\Http\RequestInterface;

class CounterMiddleware implements MiddlewareInterface
{
    protected static int $counts = 1;


    public function handle(RequestInterface $request): void
    {
        self::$counts += 1;

        $request->getMiddleware()->next();
    }
}
<?php

namespace App\Http\Middlewares;

use RTC\Contracts\Http\MiddlewareInterface;
use RTC\Contracts\Http\RequestInterface;
use RTC\Http\Middleware;

class CounterMiddleware extends Middleware
{
    protected static int $counts = 1;


    public function handle(RequestInterface $request): void
    {
        self::$counts += 1;

        $request->getResponse()->header('X-Client-Count', self::$counts);
        $request->getMiddleware()->next();
    }
}
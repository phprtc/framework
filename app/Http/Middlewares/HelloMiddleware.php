<?php

namespace App\Http\Middlewares;

use RTC\Contracts\Http\RequestInterface;
use function dump;

class HelloMiddleware extends \RTC\Http\Middleware
{
    public function handle(RequestInterface $request): void
    {
        dump(self::class);
        $request->getMiddleware()->next();
    }
}
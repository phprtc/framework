<?php

namespace App\Http\Middlewares;

use RTC\Contracts\Http\RequestInterface;

class TestRouteMiddleware extends \RTC\Http\Middleware
{
    public function handle(RequestInterface $request): void
    {
        //$request->getResponse()->html(TestRouteMiddleware::class);
        $request->getMiddleware()->next();
    }
}
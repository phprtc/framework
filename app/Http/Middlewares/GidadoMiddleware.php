<?php

namespace App\Http\Middlewares;

use RTC\Contracts\Http\RequestInterface;

class GidadoMiddleware extends \RTC\Http\Middleware
{
    public function handle(RequestInterface $request): void
    {
        if ($request->getMiddleware()->hasNext()) {
            $request->getMiddleware()->next();
        } else {
            $request->getResponse()->html('Hello, Gidado Middleware Responded');
        }
    }
}
<?php

namespace App\Http;

use App\Http\Middlewares\CounterMiddleware;
use App\Http\Middlewares\GidadoMiddleware;
use App\Http\Middlewares\HelloMiddleware;
use App\Http\Middlewares\TestRouteMiddleware;
use RTC\Contracts\Http\HttpHandlerInterface;

class Kernel extends \RTC\Http\Kernel
{
    protected HttpHandlerInterface $handler;

    protected array $httpMiddlewares = [
        CounterMiddleware::class,
    ];

    protected array $routeMiddlewares = [
        'test' => TestRouteMiddleware::class,
    ];

    protected bool $useDefaultMiddlewares = true;


    public function __construct()
    {
        $this->handler = new Handler();
    }
}
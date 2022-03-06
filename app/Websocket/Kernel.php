<?php

namespace App\Websocket;

use App\Websocket\Handlers\ChatWebsocketHandler;
use App\Websocket\Handlers\TestWebsocketHandler;

class Kernel extends \RTC\Websocket\Kernel
{
    protected array $handlers = [
        '/ws/test' => TestWebsocketHandler::class,
        '/ws/chat' => ChatWebsocketHandler::class
    ];
}
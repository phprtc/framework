<?php

namespace App\Websocket;

use RTC\Websocket\Kernel;

class WSKernel extends Kernel
{
    protected array $handlers = [
        '/ws/test' => TestWebsocketHandler::class,
        '/ws/chat' => ChatWebsocketHandler::class
    ];
}
<?php

namespace App\Websocket;

use RTC\Contracts\Websocket\ConnectionInterface;
use RTC\Contracts\Websocket\FrameInterface;
use RTC\Websocket\WebsocketHandler;
use Swoole\Timer;
use Throwable;
use function dump;

class TestWebsocketHandler extends WebsocketHandler
{
    public function onMessage(ConnectionInterface $connection, FrameInterface $frame): void
    {
        //dump("Test Server: message({$frame->getRawMessage()})");
    }

    public function onOpen(ConnectionInterface $connection): void
    {
        Timer::tick(1500, fn() => $connection->send('test.time', date('H:i:s')));

        dump("Test Server: connection opened({$connection->getIdentifier()})");
    }

    public function onClose(ConnectionInterface $connection): void
    {
        dump("Test Server: connection closed({$connection->getIdentifier()})");
    }

    public function onError(ConnectionInterface $connection, Throwable $exception): void
    {
        dump("Test Server: error({$connection->getIdentifier()}) \n Exception $exception");
    }
}
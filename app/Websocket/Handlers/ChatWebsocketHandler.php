<?php

namespace App\Websocket\Handlers;

use RTC\Contracts\Websocket\ConnectionInterface;
use RTC\Contracts\Websocket\FrameInterface;
use RTC\Websocket\WebsocketHandler;
use Throwable;
use function dump;

class ChatWebsocketHandler extends WebsocketHandler
{
    public function onMessage(ConnectionInterface $connection, FrameInterface $frame): void
    {
        dump("Chat Server: message({$frame->getPayload()->getRaw()})");

        if ($frame->getCommand() == 'chat.message') {
            $connection->send('chat.forward', strtoupper($frame->getMessage()));
        }
    }

    public function onOpen(ConnectionInterface $connection): void
    {
        //Timer::tick(2300, fn() => $connection->send('chat.time', date('H:i:s')));
        $this->addConnection($connection);
        $this->getConnection($connection->getIdentifier());
        dump("Chat Server: connection opened({$connection->getIdentifier()})");
    }

    public function onClose(ConnectionInterface $connection): void
    {
        dump("Chat Server: connection closed({$connection->getIdentifier()})");
    }

    public function onError(ConnectionInterface $connection, Throwable $exception): void
    {
        dump("Chat Server: error({$connection->getIdentifier()}) \n Exception $exception");
    }
}
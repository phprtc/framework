<?php

namespace App\Websocket\Handlers;

use RTC\Console\Console;
use RTC\Contracts\Server\ServerInterface;
use RTC\Contracts\Websocket\EventInterface;
use RTC\Contracts\Websocket\ConnectionInterface;
use RTC\Contracts\Websocket\FrameInterface;
use RTC\Websocket\Room;
use RTC\Websocket\WebsocketHandler;
use Throwable;

class ChatWebsocketHandler extends WebsocketHandler
{
    protected Room $room;
    protected Console $console;


    public function __construct(ServerInterface $server, int $size = 2048)
    {
        parent::__construct($server, $size);

        $this->room = new Room( '2go', 1024);
        $this->console = new Console();
        $this->console->setPrefix('[WS Chat] ');
    }

    public function onEvent(ConnectionInterface $connection, EventInterface $event): void
    {
        $this->console->comment("Event: {$event->getEvent()} -> {$event->getFrame()->getRaw()}");

        if ($event->eventIs('chat.room.join')) {
            $this->console->writeln('Joined');
            $connection->send('chat.forward', strtoupper($event->getMessage()));
        }

        if ($event->eventIs('chat.room.message')) {
            $connection->send('chat.forward', strtoupper($event->getMessage()));
        }
    }

    public function onMessage(ConnectionInterface $connection, FrameInterface $frame): void
    {
    }

    public function onOpen(ConnectionInterface $connection): void
    {
        $this->addConnection($connection);
        $this->console->info("Connection opened: {$connection->getIdentifier()}");
    }

    public function onClose(ConnectionInterface $connection): void
    {
        $this->room->remove($connection);
        $this->console->writeln("Connection closed: {$connection->getIdentifier()}");
    }

    public function onError(ConnectionInterface $connection, Throwable $exception): void
    {
        $this->console->error("Error: {$connection->getIdentifier()} \n Exception: $exception");
    }
}
<?php

namespace App\Websocket\Handlers;

use RTC\Console\Console;
use RTC\Contracts\Server\ServerInterface;
use RTC\Contracts\Websocket\ConnectionInterface;
use RTC\Contracts\Websocket\EventInterface;
use RTC\Contracts\Websocket\FrameInterface;
use RTC\Websocket\Exceptions\RoomOverflowException;
use RTC\Websocket\Room;
use RTC\Websocket\WebsocketHandler;
use Throwable;

class ChatWebsocketHandler extends WebsocketHandler
{
    protected Room $room;
    protected Console $console;
    private string $startDate;


    public function __construct(ServerInterface $server, int $size = 2048)
    {
        parent::__construct($server, $size);

        $this->startDate = date('Y-m-d H:i:s');
        $this->room = new Room('2go', 1024);
        $this->console = new Console();
        $this->console->setPrefix('[WS Chat] ');
    }

    public function onEvent(ConnectionInterface $connection, EventInterface $event): void
    {
        $this->console->comment("Event: {$event->getEvent()} -> {$event->getFrame()->getRaw()}");

        if ($event->eventIs('room.join')) {
            $this->console->writeln('Joined');

        }

        if ($event->eventIs('room.message')) {
            $this->room->sendAsClient(
                connection: $connection,
                event: 'room.message',
                message: $event->getMessage(),
                meta: ['sender_name' => $connection->getIdentifier()]
            );
        }
    }

    public function onMessage(ConnectionInterface $connection, FrameInterface $frame): void
    {
    }

    /**
     * @param ConnectionInterface $connection
     * @return void
     * @throws RoomOverflowException
     */
    public function onOpen(ConnectionInterface $connection): void
    {
        $this->addConnection($connection);

        $this->room->add(
            connection: $connection,
            joinedMessage: sprintf('<b><i>%s</i></b> joined this chat', $connection->getIdentifier())
        );

        $connection->send(
            event: 'welcome',
            data: [
                'message' => sprintf('Welcome<br/>This server has been running since <b>%s</b>.', $this->startDate)
            ]
        );

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
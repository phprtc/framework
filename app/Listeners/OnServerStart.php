<?php

namespace App\Listeners;

use RTC\Console\Console;
use RTC\Contracts\Server\ServerInterface;

class OnServerStart
{
    public function __invoke(ServerInterface $server): void
    {
        Console::getInstance()->info("[server] started at http://$server->host:$server->port");
    }
}
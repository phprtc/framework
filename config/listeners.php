<?php

use RTC\Server\Enums\Events;

return [
    // Server Start
    Events::SERVER_START->value => [
        App\Listeners\OnServerStart::class,
    ],

    // Websocket Connection Opened
    Events::WS_CONNECTION_OPENED->value => [

    ],

    // Websocket Connection Closed
    Events::WS_CONNECTION_CLOSED->value => [

    ],
];

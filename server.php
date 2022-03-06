<?php

use App\Http\Kernel as HttpKernel;
use App\Websocket\Kernel as WSKernel;
use RTC\Server\Server;
use RTC\Watcher\Watcher;

require __DIR__ . '/vendor/autoload.php';


Server::create('0.0.0.0', 9600)
    ->setDocumentRoot(__DIR__ . '/public')
    ->setHttpKernel(HttpKernel::class)
    ->setWebsocketKernel(WSKernel::class)
    ->onStart(function (\Swoole\Http\Server $server) {

        Watcher::create()
            ->addPath(__DIR__ . '/app')
            ->addPath(__DIR__ . '/routes')
            ->addPath(__DIR__ . '/vendor')
            ->onChange(fn() => $server->reload());

        echo "Server started at http://{$server->host}:$server->port\n";
    })
    ->run();
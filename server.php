<?php

use App\Http\Kernel as HttpKernel;
use App\Websocket\Kernel as WSKernel;
use Dotenv\Dotenv;
use RTC\Server\Server;
use Swoole\Runtime;

require __DIR__ . '/vendor/autoload.php';

$env = Dotenv::createImmutable(__DIR__)->load();

Runtime::enableCoroutine();

try {
    $server = new Server(
        host: strval($env['SERVER_HOST']),
        port: intval($env['SERVER_PORT']),
        size: intval($env['SERVER_WS_CONNECTION_SIZE']),
        heartbeatInterval: intval($env['SERVER_WS_HEARTBEAT_INTERVAL']),
        clientTimeout: intval($env['SERVER_WS_CLIENT_TIMEOUT']),
        listeners: require __DIR__ . '/config/listeners.php',
    );

    $server
        ->setRootDirectory(__DIR__)
        ->setDocumentRoot(__DIR__ . '/public')
        ->setPidFile(__DIR__ . '/.pid')
        ->setHttpKernel(HttpKernel::class);

    if ('true' == $env['SERVER_ENABLE_WEBSOCKET']) {
        $server->setWebsocketKernel(WSKernel::class);
    }

    // Monitor filesystem changes for hot code reloading
    $server->setHotCodeReload(
        status: 'true' == strtolower(strval($env['SERVER_HOT_CODE_RELOAD'])),
        paths: [
            __DIR__ . '/app',
            __DIR__ . '/routes',
            __DIR__ . '/vendor'
        ]
    );

    $server->setLogOption(
        filePath: __DIR__ . '/storage/logs/phprtc.log',
    );

    if ('true' == $env['SERVER_DAEMONIZE']) {
        $server->daemonize();
    }

    $server->run();
} catch (Throwable $e) {
    console()->error($e);
}
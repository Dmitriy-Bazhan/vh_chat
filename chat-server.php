<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\OriginCheck;
use app\Chat;

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

//set_time_limit(0);
//ignore_user_abort(true);

$wsServer = new WsServer(new Chat());

$server = IoServer::factory(
    new HttpServer(
        $wsServer
    ),
    9000
);

$wsServer->enableKeepAlive($server->loop, 30);

$server->run();
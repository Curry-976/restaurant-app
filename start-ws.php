<?php
require __DIR__ . '/vendor/autoload.php';
require 'ws-server.php';

use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new DeliverySocket()
        )
    ),
    8080
);

$server->run();

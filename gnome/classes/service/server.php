<?php 
// server.php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use gnome\classes\service\Chat;

require dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php';
require 'Chat.php';  // This will be your custom WebSocket application

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            Chat::getInstance()
        )
    ),
    8080  // Port number
);

$server->run();
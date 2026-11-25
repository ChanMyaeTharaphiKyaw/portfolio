<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/NotificationServer.php';

use React\EventLoop\Factory;
use React\Socket\SocketServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;

// Database Configuration
$dbHost = '127.0.0.1';
$dbName = 'findmypet';
$dbUser = 'root';
$dbPass = '';

$loop = Factory::create();

// Create Notification Server
$notificationServer = new NotificationServer(
    $dbHost,
    $dbName,
    $dbUser,
    $dbPass
);

// Check New Notifications
$loop->addPeriodicTimer(0.5, function () use ($notificationServer) {
    $notificationServer->checkAndPushNotifications();
});

// Start WebSocket Server
$socket = new SocketServer('0.0.0.0:8080', [], $loop);

$server = new IoServer(
    new HttpServer(
        new WsServer(
            $notificationServer
        )
    ),
    $socket,
    $loop
);

echo "=====================================\n";
echo "WebSocket Server Started\n";
echo "Listening : ws://localhost:8080\n";
echo "=====================================\n";

$loop->run();
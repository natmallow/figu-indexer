<?php 

namespace gnome\classes\service;

namespace gnome\classes\service;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;
use Predis\Client as RedisClient;

class Chat implements MessageComponentInterface {
    protected $clients;
    private static $instance = null;
    protected $redis;

    private function __construct() {
        $this->clients = new SplObjectStorage();
        $this->redis = new RedisClient([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $this->checkRedisForMessages();
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Broadcast received message to all connected clients (you might want to change this logic)
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
        echo "An error occurred: {$e->getMessage()}\n";
    }

    public function broadcastMessage($msg) {
        // Queue message in Redis instead of sending directly
        $this->redis->lpush("websocket_messages", $msg);
    }

    public function checkRedisForMessages() {
        while ($msg = $this->redis->rpop("websocket_messages")) {
            foreach ($this->clients as $client) {
                $client->send($msg);
            }
        }
    }
}

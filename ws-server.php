<?php
require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class DeliverySocket implements MessageComponentInterface {

    protected $orders; // order_id => [connections]

    public function __construct() {
        $this->orders = [];
        echo "WebSocket sécurisé lancé\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        // On attend un message "join"
    }

    public function onMessage(ConnectionInterface $conn, $msg) {
        $data = json_decode($msg, true);
        if (!$data || !isset($data["type"])) return;

        // 1️⃣ Rejoindre une commande
        if ($data["type"] === "join") {
            if (!isset($data["order_id"], $data["role"])) return;

            $conn->order_id = (int)$data["order_id"];
            $conn->role = $data["role"];

            $this->orders[$conn->order_id][] = $conn;
            return;
        }

        // 2️⃣ Livreur envoie sa position
        if ($data["type"] === "driver_location" && $conn->role === "driver") {
            $orderId = $conn->order_id;

            if (!isset($this->orders[$orderId])) return;

            foreach ($this->orders[$orderId] as $client) {
                if ($client !== $conn && $client->role === "client") {
                    $client->send(json_encode([
                        "lat" => $data["lat"],
                        "lng" => $data["lng"]
                    ]));
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        if (isset($conn->order_id)) {
            $this->orders[$conn->order_id] = array_filter(
                $this->orders[$conn->order_id],
                fn($c) => $c !== $conn
            );
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }
}

<?php
namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    private $clients = [];
    private $rooms = [];

    public function __construct() {
        echo "Chat server initialized\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients[$conn->resourceId] = [
            'connection' => $conn,
            'room' => null
        ];
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        
        if (!$data || json_last_error() !== JSON_ERROR_NONE) {
            echo "Invalid JSON received\n";
            return;
        }

        switch ($data['type']) {
            case 'join':
                $this->handleJoin($from, $data['roomId']);
                break;
            
            case 'message':
                $this->handleMessage($from, $data);
                break;
        }
    }

    private function handleJoin(ConnectionInterface $client, $roomId) {
        // Remove o cliente da sala anterior se estiver em alguma
        if ($this->clients[$client->resourceId]['room'] !== null) {
            $oldRoomId = $this->clients[$client->resourceId]['room'];
            unset($this->rooms[$oldRoomId][$client->resourceId]);
            echo "Client {$client->resourceId} left room {$oldRoomId}\n";
            
            // Remove a sala se estiver vazia
            if (empty($this->rooms[$oldRoomId])) {
                unset($this->rooms[$oldRoomId]);
                echo "Room {$oldRoomId} removed (empty)\n";
            }
        }

        // Cria a sala se não existir
        if (!isset($this->rooms[$roomId])) {
            $this->rooms[$roomId] = [];
        }

        // Adiciona o cliente à nova sala
        $this->rooms[$roomId][$client->resourceId] = $client;
        $this->clients[$client->resourceId]['room'] = $roomId;

        echo "Client {$client->resourceId} joined room {$roomId}\n";
        echo "Clients in room {$roomId}: " . implode(', ', array_keys($this->rooms[$roomId])) . "\n";
    }

    private function handleMessage(ConnectionInterface $from, $data) {
        $clientId = $from->resourceId;
        
        // Verifica se o cliente está em alguma sala
        if (!isset($this->clients[$clientId]) || $this->clients[$clientId]['room'] === null) {
            echo "Client {$clientId} tried to send message without being in a room\n";
            return;
        }

        $roomId = $this->clients[$clientId]['room'];
        
        if (!isset($this->rooms[$roomId])) {
            echo "Room {$roomId} not found for client {$clientId}\n";
            return;
        }

        $message = json_encode([
            'type' => 'message',
            'nickname' => $data['nickname'],
            'message' => $data['message'],
            'roomId' => $roomId // Para debug
        ]);

        echo "Sending message in room {$roomId} from client {$clientId}\n";
        
        // Envia a mensagem para todos os outros clientes na mesma sala
        foreach ($this->rooms[$roomId] as $id => $client) {
            if ($id !== $clientId) {
                $client->send($message);
                echo "Message sent to client {$id} in room {$roomId}\n";
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $clientId = $conn->resourceId;
        
        // Remove o cliente da sala em que estava
        if (isset($this->clients[$clientId]) && $this->clients[$clientId]['room'] !== null) {
            $roomId = $this->clients[$clientId]['room'];
            unset($this->rooms[$roomId][$clientId]);
            
            // Remove a sala se estiver vazia
            if (empty($this->rooms[$roomId])) {
                unset($this->rooms[$roomId]);
                echo "Room {$roomId} removed (empty)\n";
            }
            
            echo "Client {$clientId} disconnected from room {$roomId}\n";
        }

        // Remove o cliente da lista de clientes
        unset($this->clients[$clientId]);
        echo "Connection {$clientId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
} 
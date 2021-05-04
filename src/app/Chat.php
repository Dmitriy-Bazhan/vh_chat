<?php

namespace app;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use app\database\ConnectToChatBb;
use app\auth\Auth;

class Chat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later

        $querystring = $conn->httpRequest->getUri()->getQuery();
        $token = explode('=', $querystring);

        $auth = new Auth();
        $res = $auth->userAutorize($token[1]);

        if (is_null($res['id'])) {
            echo "TOKEN! ({$token[1]})\n";
            $temp['msg'] = 'В доступе отказано или chat_api_id неверен';
            $temp['user'] = 'Admin';
            $msg = json_encode($temp);
            $conn->send($msg);
            $conn->close();
            return false;
        }
        $message = json_encode(['message' => 'HELLO ON OPEN']);
        $this->clients->attach($conn, $message);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
        $temp = json_decode($msg, true);

        $auth = new Auth();
        $res = $auth->userAutorize($temp['token']);

        if (is_null($res['id'])) {
            $temp['msg'] = 'В доступе отказано или chat_api_id неверен';
            $msg = json_encode($temp);
            $from->send($msg);
            $from->close();
            return false;
        }

        if (isset($temp['msg']) && strlen($temp['msg']) > 0 && !empty($temp['msg'])) {
            $connectToChatDb = ConnectToChatBb::makeConnect();
            $query = 'INSERT INTO comments(user_name,comment) VALUES (?,?)';
            $param = [$temp['user'], $temp['msg']];
            ConnectToChatBb::query($connectToChatDb, $query, $param);
            foreach ($this->clients as $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}
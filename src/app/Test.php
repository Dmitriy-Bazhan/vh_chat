<?php
namespace app;

use app\database\ConnectToChatBb;
use app\database\ConnectToUsersBb;

class Test
{
    private $connect;

    public function __construct()
    {
        $this->connect = ConnectToChatBb::makeConnect();
    }

    public function testUsersConnect()
    {
        $query = 'SELECT * FROM users';
        $param = [];
        $result = $this->query($query, $param);

        $res = $result->fetch(\PDO::FETCH_ASSOC);
        dump($res);
    }

    public function testTokenToDb()
    {
        $res['token'] = 'Dima';
        $res['message'] = 'Enabled';
//        $query = 'INSERT INTO temporary_token(token,ip) VALUES (?,null)';
//        $param = [str_replace('\'', '', $res['token'])];

        $temp['user'] = 'Dima';
        $temp['msg'] = 'FFFFFFFF';

        $query = 'SELECT * FROM comments';
        $param = [];

        dump($this->connect);
        dump($query);

        $result = $this->query($query, $param);
        $res = $result->fetchAll(\PDO::FETCH_ASSOC);
        dump($res);
    }

    private function query($query, $param)
    {
        try {
            $result = $this->connect->prepare($query);
            $result->execute($param);

        } catch (\PDOException $e) {
            echo('QUERY FAILED :' . $e->getMessage());
        }
        return $result;
    }
}
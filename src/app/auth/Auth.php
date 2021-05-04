<?php

namespace app\auth;

use app\database\ConnectToChatBb;

class Auth
{
    public function userAutorize($api_password)
    {
        $connectToChatDb = ConnectToChatBb::makeConnect();
        $query = 'SELECT id, name FROM users WHERE api_password = ?';
        $param = [$api_password];

        try {
            $result = $connectToChatDb->prepare($query);
            $result->execute($param);
        } catch (\PDOException $e) {
            echo('QUERY TO USERS TABLE FAILED :' . $e->getMessage()); //переделать в отправку логов
        }
        return $result->fetch(\PDO::FETCH_ASSOC);
    }
}
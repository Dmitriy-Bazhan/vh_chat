<?php

namespace app\database;

class ConnectToChatBb
{
    public static function makeConnect()
    {
        static $connect = '';

        $ds = DIRECTORY_SEPARATOR;
        $path = str_replace('src\app\database\\', '', __DIR__ . $ds . 'config' . $ds . 'config.php');
        $config = require_once $path;

        if (empty($connect)) {
            try {
                $connect = new \PDO($config['chat']['dns'], $config['chat']['user'], $config['chat']['password'], [\PDO::ATTR_PERSISTENT => true]);
            } catch (\PDOException $ex) {
                echo('CONNECTION TO CHAT DATABASES FAILED :' . $ex->getMessage());
            }
        }
        return $connect;
    }

    public static function query($connect, $query, $param)
    {
        try {
            $result = $connect->prepare($query);
            $result->execute($param);

        } catch (\PDOException $e) {
            echo('QUERY FAILED :' . $e->getMessage());
        }
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }
}
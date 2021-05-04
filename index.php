<?php
ini_set('display_errors', true);
error_reporting(E_ALL);

use app\Test;
use app\auth\Auth;
use \app\database\ConnectToChatBb;

require 'vendor/autoload.php';

require_once __DIR__ . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'helper.php';

$uri = $_SERVER['REQUEST_URI'];

switch ($uri) {
    case '/auth' :
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && !empty($_POST['id'])) {
            $api_password = $_POST['id'];
            $auth = new Auth();
            $res = $auth->userAutorize($api_password);

            if (!is_null($res['id'])) {
                $res['message'] = 'Enabled';
                $message = json_encode($res);
            }
            sendHeaders();
            echo json_encode($message);

        }
        break;
    case '/test' :
        $test = new Test();
        $test->testUsersConnect();
        break;
    case '/add_comments' :
        $connect = ConnectToChatBb::makeConnect();
        $query = "SELECT * FROM comments ORDER BY id DESC LIMIT 50";
        $param = [];
        $result = ConnectToChatBb::query($connect, $query, $param);

        sendHeaders();
        echo json_encode($result);
        break;
    case '/testTokenToDb' :
        $test = new Test();
        $test->testTokenToDb();
        break;
    default :
        break;
}

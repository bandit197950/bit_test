<?php

namespace bit_test;

include_once("conf\utils.php");

use bit_test\www\conf\Utils;
use bit_test\www\classes\Controller;
use bit_test\www\classes\Main;
use bit_test\www\classes\Router;
use bit_test\www\conf\Config;
use bit_test\www\lib\MySQLDb;

session_start();
session_write_close();

try {
    Utils::Startup();

    $db = new MySQLDb(Config::db_host(), Config::db_login(), Config::db_pwd(), Config::db_name());
    $db->Connect();
    Main::SetDB($db);

    $router = new Router();
    $router->SetControllersPath("controller");

    if (isset($_SESSION['id']) || $_GET['route'] == 'login') {
        $router->LoadController($_GET['route']);
        session_write_close();
    } else {
        Controller::ChangeLocation('login');
    }
} catch (\Exception $e) {
    Utils::ShowException($e);
}
<?php
    session_start();
    try {
        require_once "conf/startup.php";
        require_once "MySQLDb.php";

        $db = new MySQLDb($conf['db_host'], $conf['db_login'], $conf['db_pwd'], $conf['db_name']);
        $db->Connect();
        Main::SetDB($db);

        $router = new Router();
        $router->SetControllersPath("controller");

        if(isset($_SESSION['id']) || $_GET['route'] == 'login') {
            $router->LoadController($_GET['route']);
        }
        else {
            Controller::ChangeLocation('login');
        }
    }
    catch(Exception $e) {
        include "lib/error.php";
        session_write_close();
        ShowException($e);
    }
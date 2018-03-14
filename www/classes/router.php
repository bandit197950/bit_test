<?php

namespace bit_test\www\classes;

use bit_test\www\conf\Config;

class Router extends Strict
{
    private $DefaultController;
    private $ControllersPath;

    public function __construct()
    {
    }

    public function SetControllersPath($pathname)
    {
        $this->ControllersPath = $pathname;
    }

    public function SetDefaultController($controller)
    {
        $this->DefaultController = $controller;
    }

    public function LoadController($routeString)
    {
        $route_string = trim($routeString, '/');
        $path_parts = explode("/", $route_string);

        $controller_info['path'] = $this->ControllersPath;
        $controller_info['name'] = 'Login';
        $controller_info['file'] = 'login.php';
        $controller_info['function'] = 'index';

        foreach ($path_parts as $part) {
            if (is_dir($controller_info['path'] . "/" . $part)) {
                $controller_info['path'] .= "/" . $part;
                array_shift($path_parts);
                continue;
            }

            if (is_file($controller_info['path'] . "/" . $part . ".php")) {
                $controller_info['file'] = $part . ".php";
                $controller_info['name'] = $part;

                array_shift($path_parts);
                $controller_info['function'] = (isset($path_parts[0])) ? $path_parts[0] : 'index';
                break;
            }
            die('no such controller');
        }
        $controller_path = rtrim(str_replace('/', '\\', $controller_info['path']), '\\') . '\\';
        $controller_class = Config::class_path() . $controller_path . $controller_info['name'];
        $controller = new $controller_class;

        if (method_exists($controller, $controller_info['function'])) {
            call_user_func(array($controller, $controller_info['function']));
        } else {
            $controller_info['function'] = 'index';
            if (method_exists($controller, $controller_info['function'])) {
                call_user_func(array($controller, $controller_info['function']));
            } else {
                session_write_close();
                die("method doesn't exists");
            }
        }

    }
}

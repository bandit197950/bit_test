<?php
	class Router extends Strict	{
		private $DefaultController;
		private $ControllersPath;
		
		public function __construct() {
		}
		
		public function SetControllersPath($pathname) {
			$this->ControllersPath = $pathname;
		}
		
		public function SetDeafaultController($controller) {
			$this->DefaultController = $controller;
		}
		
		public function LoadController($route_string) {
			$route_string = trim($route_string, '/');
			$path_parts = explode("/", $route_string);
			
			$controller_info['path']     = $this->ControllersPath;
			$controller_info['name']     = 'Controller_login';
			$controller_info['file']     = 'login.php';
			$controller_info['function'] = 'index';
			
			foreach($path_parts as $part) {
				if(is_dir($controller_info['path'] . "/" . $part)) 	{
					$controller_info['path'] .= "/" . $part;
					array_shift($path_parts);
					continue;
				}
				
				if(is_file($controller_info['path'] . "/" . $part . ".php")) {
					$controller_info['file'] = $part . ".php";
					$controller_info['name'] = 'Controller_' . $part;
					
					array_shift($path_parts);
					$controller_info['function'] = (isset($path_parts[0])) ? $path_parts[0] : 'index';
					break;
				}
				session_write_close();
				die('no such controller');				
			}
			
			include_once $controller_info['path'] . "/" . $controller_info['file'];
			
			$controller = new $controller_info['name'];

			if(method_exists($controller, $controller_info['function'])) {
				call_user_func(array($controller, $controller_info['function']));
			}
			else {
				$controller_info['function'] = index;
				if(method_exists($controller, $controller_info['function'])) {
					call_user_func(array($controller, $controller_info['function']));
				}
				else {
				    session_write_close();
					die("method doesn't excists");
				}
			}
			
		}
	};
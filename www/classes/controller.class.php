<?php
	require_once "conf/config.php";

	abstract class Controller extends Strict {
		protected $Error = array();
		
		public abstract function Index();
	
		private static function Load($path, $name, $prefix) {
			require_once $path . $name  . ".php";
			
			$full_class_name = $prefix . $name;
			$class = new $full_class_name;

			return $class;
		}
		
		public static function LoadModel($model_name) {
			return self::Load("model/", $model_name, "Model_");
		}
		
		public static function loadView($view_name)	{
			return self::Load("view/", $view_name, "View_");
		}

		public static function GetLocation($location) {
			global $conf;
			return $conf['doc_path'] . $location; 
		}

		public static function ChangeLocation($location) {
			$location = self::GetLocation($location);
			header("Location: ${location}");
			exit();
		}
	
	};
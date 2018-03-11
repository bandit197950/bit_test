<?php
	class Main extends Strict {
		private static $Db;
		
		public static function SetDB($db) {
			self::$Db = $db;
		}
		
		public static function GetDB() {
			return self::$Db;
		}
	}
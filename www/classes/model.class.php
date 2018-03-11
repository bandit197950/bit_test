<?php
	abstract class Model extends Strict	{
		protected $db;
		protected $Error;
		
		public function __construct() {}
		
		public function GetLastError() {
			return $this->Error;
		}
	};
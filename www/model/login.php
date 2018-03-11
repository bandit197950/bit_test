<?php
	class Model_login extends Model {
		public function __construct() {
			$this->db = Main::GetDB();
		}
		
		public function Authenticate($_email, $_password) {
			$email    = $this->db->Escape(trim($_email));
			$password = $this->db->Escape(trim($_password));
			$result   = $this->db->QueryFirst("SELECT * FROM users u WHERE u.`email` = '" . $email . "' AND u.`password` = '" . md5($password) . "'");
			return $result;
		}
	};
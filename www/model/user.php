<?php
	class Model_user extends Model {
		public function __construct() {
			$this->db = Main::getDB();
		}
		
		public function GetUserInfo($userId){
			$user_id = (int) $userId;
			$result = $this->db->QueryFirst("SELECT * FROM users WHERE id='" . $user_id . "'");
			return $result;
		}
	
		public function WriteOffAmount($id, $amount)
		{
		    $ok = true;
            if(($user = $this->GetUserInfo($id))) {
                $user['balance'] -= round($amount, 2);
                if($user['balance'] >= 0) {
                    $r = $this->db->QueryUpdate("users", $user, "`id`=" . (int)$id);
                    $ok = !!$r;
                    if(!ok) {
                        $this->Error = $this->db->GetLastError();
                    }
                }
                else {
                    $this->Error = "Can't write off amount, because it's greater when balance";
                    $ok = false;
                }
            }
            else {
                $this->Error = "User not found (id=$id)";
                $ok = false;
            }
			return $ok;
		}
		
	};
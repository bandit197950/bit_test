<?php
	class Controller_profile extends Controller {
		public function Index() {
		    $user_id         = $_SESSION['id'];
		    $errors          = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
		    unset($_SESSION['errors']);
			$user_model      = Controller::LoadModel("user");
			$user_info       = $user_model->GetUserInfo($user_id);
            $history_model   = Controller::LoadModel("balance_history");
			$balance_history = $history_model->GetHistory($user_id);
			
			$header_view  = new View("header");
			$profile_view = new View("profile");
			$footer_view  = new View("footer");
			
			$header['title']         = "Profile info";
			$data['action']          = self::GetLocation("profile/WriteOff");
			$data['logout_action']   = self::GetLocation("login/Logout");
			$data['error']           = array_merge(empty($this->Error) ? [] : $this->Error, $errors);
			$data['first_name']      = $user_info['first_name'];
			$data['last_name']       = $user_info['last_name'];
			$data['email']           = $user_info['email'];
			$data['balance']         = $user_info['balance'];
			$data['balance_history'] = $balance_history;
			$data['submit_button']   = 'Write off';
			
			$header_view->setData($header);
			$profile_view->setData($data);
			
			$header_view->display();
			$profile_view->display();
			$footer_view->display();
		}

		public function WriteOff() {
		    $ok = false;
            $user_id = $_SESSION['id'];
            unset($_SESSION['errors']);

            if(isset($user_id)) {
                $this->Validate();
                if(empty($this->Error)) {
                    $user_model    = Controller::LoadModel("user");
                    $history_model = Controller::LoadModel("balance_history");
                    $user_info     = $user_model->GetUserInfo($user_id);
                    $db = Main::GetDB();
                    try {
                        $wr_off_amt     = round(floatval($_REQUEST['write_off_amount']), 2);
                        $balance_before = $user_info['balance'];
                        $db->BeginTransaction();
                        if(!$user_model->WriteOffAmount($user_id, $wr_off_amt)) {
                            $this->Error[] = $user_model->GetLastError();
                            $db->RollbackTransaction();
                        }
                        else {
                            if(!$history_model->WriteHistory($user_id, $balance_before, $wr_off_amt)) {
                                $this->Error[] = $history_model->GetLastError();
                                $db->RollbackTransaction();
                            }
                            else {
                                $db->CommitTransaction();
                                $ok = true;
                            }
                        }
                    }
                    catch(Exception $e) {
                        $db->RollbackTransaction();
                        $this->Error[] = $e;
                    }
                }
            }
            if(!empty($this->Error))
                $_SESSION['errors'] = $this->Error;
            self::ChangeLocation('profile');
        }

		private function Validate() {
		    $write_off_amount = round(floatval($_REQUEST['write_off_amount']), 2);
			if($write_off_amount <= 0) {
				$this->Error[] = "Write off amount must be positive";
			}
		}
	}
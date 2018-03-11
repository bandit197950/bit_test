<?php
	class Controller_login extends Controller {
		public function Logout() {
			unset($_SESSION['id']);
			unset($_SESSION['first_name']);
			unset($_SESSION['last_name']);

            session_write_close();
			self::ChangeLocation('login');			
		}
		
		public function Index() {
			if(isset($_SESSION['id']))
				self::ChangeLocation('profile');
			
			$login_model = Controller::LoadModel("login");
			
			if(isset($_REQUEST['email']) || isset($_REQUEST['password'])) {
				$this->Validate();
				if(empty($this->Error)) {
					$user = $login_model->authenticate($_REQUEST['email'], $_REQUEST['password']);
					if($user) {
						$_SESSION['id']         = $user['id'];
						$_SESSION['first_name'] = $user['first_name'];
						$_SESSION['last_name']  = $user['last_name'];

						session_write_close();
						self::ChangeLocation('profile');
					}
					else {
						$this->Error[] = "Email or password are incorrect";
					}
				}
			}
			
			$header_view = new View("header");
			$login_view  = new View("login");
			$footer_view = new View("footer");
			
			$header['title'] = "Login";
			
			$data['title']      = 'Login';
			$data['email']       = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
			$data['error']      = $this->Error;
			$data['action']     = self::GetLocation("login");

			$header_view->SetData($header);
			$login_view->SetData($data);
			
			$header_view->Display();
			$login_view->Display();
			$footer_view->Display();
		}
		
		private function Validate() {
			if(!$_REQUEST['email'] || trim($_REQUEST['email']) == '') {
				$this->Error[] = "Email is empty";
			}

			if(!$_REQUEST['password'] || trim($_REQUEST['password']) == '') {
				$this->Error[] = "Pasword is empty";
			}
			else if(strlen($_REQUEST['password']) < 3) {
				$this->Error[] = "The pasword is too short (less then 3 characters)";
			}
		}
	}
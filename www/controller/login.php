<?php

namespace bit_test\www\Controller;

use bit_test\www\classes\Controller;
use bit_test\www\classes\View;

class Login extends Controller
{
    public function Logout()
    {
        session_start();
        $_SESSION['logout'] = true;
        unset($_SESSION['id']);
        session_write_close();
        self::ChangeLocation('login');
    }

    public function Index()
    {
        session_start();

        if (isset($_SESSION['logout'])) {
            unset($_SESSION['logout']);
            $this->RegenerateSessionId();
            session_write_close();
        } elseif (isset($_SESSION['id'])) {
            session_write_close();
            self::ChangeLocation('profile');
        }

        $login_model = Controller::LoadModel("Login");

        $email = "";

        if (isset($_POST['email']) || isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $this->Validate($email, $password);
            if (empty($this->Error)) {
                $user = $login_model->Authenticate($email, $password);
                if ($user) {
                    session_start();
                    $this->RegenerateSessionId();
                    $_SESSION['id'] = $user['id'];
                    session_write_close();

                    self::ChangeLocation('profile');
                } else {
                    $this->Error[] = "Email or password are incorrect";
                }
            }
        }
        $header_view = new View("header");
        $login_view = new View("login");
        $footer_view = new View("footer");

        $header['title'] = "Login";

        $data['title'] = 'Login';
        $data['email'] = $email;
        $data['error'] = $this->Error;
        $data['action'] = self::GetLocation("login");

        $header_view->SetData($header);
        $login_view->SetData($data);

        $header_view->Display();
        $login_view->Display();
        $footer_view->Display();
    }

    private function Validate($email, $password)
    {
        if (!$email || trim($email) == '') {
            $this->Error[] = "Email is empty";
        }

        if (!$password || trim($password) == '') {
            $this->Error[] = "Password is empty";
        }
    }

    //
    // May be problem if connection is poor
    //
    private function RegenerateSessionId()
    {
        $_SESSION['destroyed'] = time();
        session_regenerate_id();
        unset($_SESSION['destroyed']);
    }
}

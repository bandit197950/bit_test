<?php

namespace bit_test\www\Controller;

use bit_test\www\classes\Controller;
use bit_test\www\classes\View;
use bit_test\www\classes\Main;
use bit_test\www\model\User;
use bit_test\www\model\BalanceHistory;

class Profile extends Controller
{
    public function Index()
    {
        session_start();
        $errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
        unset($_SESSION['errors']);
        session_write_close();

        $user_id = $_SESSION['id'];

        $user_model = Controller::LoadModel("User");
        $history_model = Controller::LoadModel("BalanceHistory");

        $db = Main::GetDB();
        $user_info = [];
        $balance_history = [];
        try {
            $db->BeginTransaction();
            $user_info = $user_model->GetUserInfo($user_id);
            $balance_history = $history_model->GetHistory($user_id);
            $db->CommitTransaction();
        } catch (\Exception $e) {
            $db->RollbackTransaction();
        }
        $header_view = new View("header");
        $profile_view = new View("profile");
        $footer_view = new View("footer");

        $header['title'] = "Profile info";
        $data['action'] = self::GetLocation("profile/WriteOff");
        $data['logout_action'] = self::GetLocation("login/Logout");
        $data['error'] = array_merge(empty($this->Error) ? [] : $this->Error, $errors);
        $data['first_name'] = $user_info['first_name'];
        $data['last_name'] = $user_info['last_name'];
        $data['email'] = $user_info['email'];
        $data['balance'] = $user_info['balance'];
        $data['balance_history'] = $balance_history;
        $data['submit_button'] = 'Write off';

        $header_view->SetData($header);
        $profile_view->SetData($data);

        $header_view->Display();
        $profile_view->Display();
        $footer_view->Display();
    }

    public function WriteOff()
    {
        session_start();
        $user_id = $_SESSION['id'];
        unset($_SESSION['errors']);
        session_write_close();

        if (isset($user_id)) {
            /** @var User $user_model */
            $user_model = Controller::LoadModel("User");
            /** @var BalanceHistory $history_model */
            $history_model = Controller::LoadModel("BalanceHistory");
            $db = Main::GetDB();
            try {
                $wr_off_amt = round(floatval($_POST['write_off_amount']), 2);
                $db->BeginTransaction();
                $user_info = $user_model->GetUserInfo($user_id);
                if (!$user_info) {
                    throw new \Exception($user_model->GetLastError());
                }
                $this->Validate($user_info['balance'], $wr_off_amt);
                if (empty($this->Error)) {
                    $balance_before = $user_info['balance'];
                    if (!$user_model->WriteOffAmount($user_info, $wr_off_amt)) {
                        throw new \Exception($user_model->GetLastError());
                    } else {
                        if (!$history_model->WriteHistory($user_id, $balance_before, $wr_off_amt)) {
                            throw new \Exception($history_model->GetLastError());
                        } else {
                            $db->CommitTransaction();
                        }
                    }
                } else {
                    $db->RollbackTransaction();
                }
            } catch (\Exception $e) {
                $db->RollbackTransaction();
                $this->Error[] = $e;
            }
        }
        if (!empty($this->Error)) {
            session_start();
            $_SESSION['errors'] = $this->Error;
            session_write_close();
        }
        self::ChangeLocation('profile');
    }

    private function Validate($balance, $wrOffAmount)
    {
        if (bccomp($wrOffAmount, '0.00', 2) <= 0) {
            $this->Error[] = "Write off amount must be positive";
        } elseif (bccomp($balance, $wrOffAmount, 2) < 0) {
            $this->Error[] = "Can't write off amount, because it's greater than balance";
        }
    }
}

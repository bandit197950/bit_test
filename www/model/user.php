<?php

namespace bit_test\www\model;

use bit_test\www\classes\Model;
use bit_test\www\classes\Main;
use bit_test\www\conf\Config;

class User extends Model
{
    public function __construct()
    {
        $this->Db = Main::getDB();
    }

    public function GetUserInfo($userId)
    {
        $user_id = (int)$userId;
        $result = $this->Db->QueryFirst("SELECT * FROM users WHERE id='" . $user_id . "'");
        return $result;
    }

    public function WriteOffAmount($user, $amount)
    {
        $ok = true;
        if ($user) {
            $balance = bcadd($user['balance'], -$amount, 2 );
            if (bccomp($balance, '0.00', 2) >= 0) {
                $user['balance'] = $balance;
                $r = $this->Db->QueryUpdate("users", $user, "`id`=" . (int)$user['id']);
                $ok = !!$r;
                if (!$ok) {
                    $this->Error = $this->Db->GetLastError();
                }
            } else {
                $this->Error = "Can't write off amount, because it's greater than balance";
                $ok = false;
            }
        } else {
            $this->Error = "User is null";
            $ok = false;
        }
        return $ok;
    }

}

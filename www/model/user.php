<?php

namespace bit_test\www\model;

use bit_test\www\classes\Model;
use bit_test\www\classes\Main;

class User extends Model
{
    public function __construct()
    {
        $this->Db = Main::getDB();
    }

    public function GetUserInfo($userId, $lockForUpdate)
    {
        $user_id = (int)$userId;
        $sql = "SELECT * FROM users WHERE id='$user_id'" . ($lockForUpdate ? " FOR UPDATE;" : ";");
        $result = $this->Db->QueryFirst($sql);
        return $result;
    }

    public function WriteOffAmount($user, $amount)
    {
        if ($user) {
            $balance = bcadd($user['balance'], -$amount, 2);
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

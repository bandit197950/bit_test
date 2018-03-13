<?php

namespace bit_test\www\model;

use bit_test\www\classes\Model;
use bit_test\www\classes\Main;

class BalanceHistory extends Model
{
    public function __construct()
    {
        $this->Db = Main::getDB();
    }

    public function GetHistory($userId)
    {
        $user_id = (int)$userId;
        return $this->Db->FetchAllRecords("SELECT * FROM balance_history WHERE user_id='" . $user_id . "'");
    }

    public function WriteHistory($userId, $balanceBefore, $amount)
    {
        $record = [];
        $record["user_id"] = (int)$userId;
        $record["balance_before"] = round($balanceBefore, 2);
        $record["write_off_amount"] = round($amount, 2);
        $r = $this->Db->QueryInsert("balance_history", $record);
        $ok = !!$r;
        if (!$ok) {
            $this->Error = $this->Db->GetLastError();
        }
        return $ok;
    }
}
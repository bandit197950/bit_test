<?php
class Model_balance_history extends Model {
    public function __construct() {
        $this->db = Main::getDB();
    }

    public function GetHistory($userId){
        $user_id = (int) $userId;
        return $this->db->FetchAllRecords("SELECT * FROM balance_history WHERE user_id='" . $user_id . "'");
    }

    public function WriteHistory($userId, $balanceBefore, $amount)
    {
        $ok = true;
        $record = [];
        $record["user_id"]          = (int)$userId;
        $record["balance_before"]   = round($balanceBefore, 2);
        $record["write_off_amount"] = round($amount, 2);
        $r = $this->db->QueryInsert("balance_history", $record);
        $ok = !!$r;
        if(!ok) {
            $this->Error = $this->db->GetLastError();
        }
        return $ok;
    }
};
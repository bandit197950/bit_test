<?php

namespace bit_test\www\model;

use bit_test\www\classes\Model;
use bit_test\www\classes\Main;

class Login extends Model
{
    public function __construct()
    {
        $this->Db = Main::GetDB();
    }

    public function Authenticate($_email, $_password)
    {
        $email = $this->Db->Escape(trim($_email));
        $password = $this->Db->Escape(trim($_password));
        $result = $this->Db->QueryFirst("SELECT * FROM users u WHERE u.`email` = '" . $email . "' AND u.`password` = '" . md5($password) . "'");
        return $result;
    }
}

;
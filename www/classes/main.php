<?php

namespace bit_test\www\classes;

use bit_test\www\lib\Db;

class Main extends Strict
{
    /** @var Db $Db */
    private static $Db;

    public static function SetDB($db)
    {
        self::$Db = $db;
    }

    public static function GetDB()
    {
        return self::$Db;
    }
}
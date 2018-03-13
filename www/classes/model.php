<?php

namespace bit_test\www\classes;

use bit_test\www\lib\Db;

abstract class Model extends Strict
{
    /** @var Db $Db */
    protected $Db;
    protected $Error;

    public function __construct()
    {
    }

    public function GetLastError()
    {
        return $this->Error;
    }
}

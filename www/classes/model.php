<?php

namespace bit_test\www\classes;

abstract class Model extends Strict
{
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

;
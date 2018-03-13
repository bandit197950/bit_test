<?php

namespace bit_test\www\lib;

abstract class Db
{
    abstract public function Connect($newLink = false);

    abstract public function GetLastError();

    abstract public function BeginTransaction();

    abstract public function CommitTransaction();

    abstract public function RollbackTransaction();

    abstract public function Close();

    abstract public function Query($sql);

    abstract public function FetchArray();

    abstract public function QueryUpdate($table, $data, $where = '1');

    abstract public function QueryInsert($table, $data);

    abstract public function FetchAllRecords($sql);

    abstract public function Escape($string);

    abstract public function QueryFirst($queryString);
}
<?php

namespace bit_test\www\lib;

class MySQLDb extends Db
{
    private $Server = "";
    private $User = ""; //database login name
    private $Pass = ""; //database login password
    private $Database = ""; //Database name
    private $Pre = ""; //table Prefix
    private $InnodbVersion = 0;

    private $Error = "";
    private $ErrNo = 0;

    private $AffectedRows = 0;

    private $LinkId = null;
    private $QueryId = FALSE;

    function __construct($server, $user, $pass, $database, $pre = '')
    {
        $this->Server = $server;
        $this->User = $user;
        $this->Pass = $pass;
        $this->Database = $database;
        $this->Pre = $pre;
    }

    private function SupportReadWriteTransaction()
    {
        return $this->InnodbVersion < "5.6.5";
    }

    public function Connect($newLink = false)
    {
        $this->LinkId = mysqli_connect($this->Server, $this->User, $this->Pass, $newLink);

        if (!$this->LinkId) {
            $this->ThrowErr("Could not connect to server: <b>$this->Server</b>.");
        } else {
            if (!mysqli_select_db($this->LinkId, $this->Database)) {
                $this->ThrowErr("Could not open database: <b>$this->Database</b>.");
            } else {
                mysqli_query($this->LinkId, "set character_set_server='utf8'");
                mysqli_query($this->LinkId, "set character_set_client='utf8'");
                mysqli_query($this->LinkId, "set character_set_results='utf8'");
                mysqli_query($this->LinkId, "set collation_connection='utf8_general_ci'");
                mysqli_query($this->LinkId, "SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ");
                $this->InnodbVersion = $this->QueryFirst("select variable_value
                                                               from information_schema.global_variables
                                                               where variable_name = 'innodb_version';");
            }
        }
        $this->Server = '';
        $this->User = '';
        $this->Pass = '';
        $this->Database = '';
    }

    public function GetLastError()
    {
        if ($this->LinkId) {
            $error = mysqli_error($this->LinkId);
        } else {
            $error = mysqli_connect_error();
        }
        return $error;
    }

    public function BeginTransaction()
    {
        $ok = true;
        if (!$this->SupportReadWriteTransaction()) {
            if (!mysqli_autocommit($this->LinkId, false)) {
                $this->ThrowErr("Start transaction failed");
                $ok = false;
            }
        } else if (!mysqli_begin_transaction($this->LinkId, MYSQLI_TRANS_START_READ_WRITE)) {
            $this->ThrowErr("Start transaction failed");
            $ok = false;
        }
        return $ok;
    }

    public function CommitTransaction()
    {
        $ok = true;
        if (!mysqli_commit($this->LinkId)) {
            $this->ThrowErr("Commit transaction failed");
            $ok = false;
        }
        if (!$this->SupportReadWriteTransaction()) {
            mysqli_autocommit($this->LinkId, true);
        }
        return $ok;
    }

    public function RollbackTransaction()
    {
        $ok = true;
        if (!mysqli_rollback($this->LinkId)) {
            $this->ThrowErr("Rollback transaction failed");
            $ok = false;
        }
        if (!$this->SupportReadWriteTransaction()) {
            mysqli_autocommit($this->LinkId, true);
        }
        return $ok;
    }

    public function Close()
    {
        if (!mysqli_close($this->LinkId)) {
            $this->ThrowErr("Connection close failed.");
        }
    }

    public function Escape($string)
    {
        if (get_magic_quotes_runtime()) {
            $string = stripslashes($string);
        }
        return mysqli_real_escape_string($this->LinkId, $string);
    }

    public function Query($sql)
    {
        $this->QueryId = mysqli_query($this->LinkId, $sql);
        if (!$this->QueryId) {
            $this->ThrowErr("<b>MySQL Query fail:</b> $sql");
            return 0;
        }

        $this->AffectedRows = mysqli_affected_rows($this->LinkId);
        return $this->QueryId;
    }

    public function FetchArray($queryId = FALSE)
    {
        $record = null;
        if ($queryId != FALSE) {
            $this->QueryId = $queryId;
        }
        if (isset($this->QueryId)) {
            $record = mysqli_fetch_assoc($this->QueryId);
        } else {
            $this->ThrowErr("Invalid query_id: <b>$this->QueryId</b>. Records could not be fetched.");
        }
        return $record;
    }

    public function FetchAllRecords($sql)
    {
        $query_id = $this->Query($sql);
        $records = null;

        while ($row = $this->FetchArray($query_id)) {
            $records[] = $row;
        }
        $this->FreeResult($query_id);
        return $records;
    }

    public function FreeResult($queryId = FALSE)
    {
        if ($queryId != FALSE) {
            $this->QueryId = $queryId;
        }
        mysqli_free_result($this->QueryId);
    }

    public function QueryFirst($queryString)
    {
        $query_id = $this->query($queryString);
        $record = $this->FetchArray($query_id);
        $this->FreeResult($query_id);
        return $record;
    }

    public function QueryUpdate($table, $data, $where = '1')
    {
        $q = "UPDATE `" . $this->Pre . $table . "` SET ";

        foreach ($data as $key => $val) {
            if (strtolower($val) == 'null') {
                $q .= "`$key` = NULL, ";
            } else if (strtolower($val) == 'now()') {
                $q .= "`$key` = NOW(), ";
            } else {
                $q .= "`$key`='" . $this->escape($val) . "', ";
            }
        }

        $q = rtrim($q, ', ') . ' WHERE ' . $where . ';';

        return $this->Query($q);
    }

    public function QueryInsert($table, $data)
    {
        $q = "INSERT INTO `" . $this->Pre . $table . "` ";
        $v = '';
        $n = '';

        foreach ($data as $key => $val) {
            $n .= "`$key`, ";
            if (strtolower($val) == 'null') {
                $v .= "NULL, ";
            } else if (strtolower($val) == 'now()') {
                $v .= "NOW(), ";
            } else {
                $v .= "'" . $this->Escape($val) . "', ";
            }
        }

        $q .= "(" . rtrim($n, ', ') . ") VALUES (" . rtrim($v, ', ') . ");";

        if ($this->Query($q)) {
            return mysqli_insert_id($this->LinkId);
        } else {
            return false;
        }
    }

    private function ThrowErr($msg = '')
    {
        if ($this->LinkId) {
            $this->Error = mysqli_error($this->LinkId);
            $this->ErrNo = mysqli_errno($this->LinkId);
        } else {
            $this->Error = mysqli_connect_error();
            $this->ErrNo = mysqli_connect_errno();
        }
        ?>
        <table align="center" border="1" cellspacing="0" style="background:white;color:black;width:80%;">
            <tr>
                <th colspan=2>Database Error</th>
            </tr>
            <tr>
                <td align="right" valign="top">Message:</td>
                <td><?php echo $msg; ?></td>
            </tr>
            <?php if (strlen($this->Error) > 0) {
                echo '<tr><td align="right" valign="top" nowrap>MySQL Error:</td><td>' . $this->Error . '</td></tr>';
                echo '<tr><td align="right" valign="top" nowrap>MySQL Errno:</td><td>' . $this->ErrNo . '</td></tr>';
            } ?>
            <tr>
                <td align="right">Date:</td>
                <td><?php echo date("l, F j, Y \a\\t g:i:s A"); ?></td>
            </tr>
            <tr>
                <td align="right">Script:</td>
                <td><a href="<?php echo @$_SERVER['REQUEST_URI']; ?>"><?php echo @$_SERVER['REQUEST_URI']; ?></a></td>
            </tr>
            <?php if (strlen(@$_SERVER['HTTP_REFERER']) > 0) echo '<tr><td align="right">Referer:</td><td><a href="' . @$_SERVER['HTTP_REFERER'] . '">' . @$_SERVER['HTTP_REFERER'] . '</a></td></tr>'; ?>
        </table>
        <?php
    }
}


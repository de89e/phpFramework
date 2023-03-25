<?php

namespace framework\system\database\provider;

use framework\system\database\dbInterface;
use framework\system\database\dbSqlCommandInterface;

class ProviderMysqli extends dbInterface implements dbSqlCommandInterface
{

    public $conn = null;
    public $result = null;

    public function setDatabase($database)
    {
        $this->database = $database;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setSource($source)
    {
        $this->source = $source;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    public function Open()
    {
        if (!function_exists('mysqli_connect')) {
            die('Mysqli extension error!');
        }
        $this->conn = mysqli_connect($this->source, $this->username, $this->password);
        if ($this->conn) {

            if (mysqli_select_db($this->conn, $this->database)) {
                mysqli_query($this->conn, 'SET character_set_connection='
                    . $this->charset . ', character_set_results='
                    . $this->charset . ', character_set_client=binary');

                return TRUE;
            } else {
                return FALSE;
            }
        } else {

            return FALSE;
        }
    }

    public function Close()
    {
        if ($this->conn) {
            mysqli_close($this->conn);
        }
    }

    public function Query($sql)
    {
        if ($this->conn) {
            return $this->result = mysqli_query($this->conn, $sql);
        }
        return FALSE;
    }

    /*
     * dbSqlCommandInterface 实现
     */

    public function getLastInsertId()
    {
        if ($this->result) {
            $sql = 'select LAST_INSERT_ID()';
            $this->Query($sql);
            $result = $this->getResultByOneArray();
            return $result[0];
        }
    }

    public function getResultCount()
    {
        if ($this->result) {
            return mysqli_num_rows($this->result);
        }
    }

    public function getResultAffectedCount()
    {
        if ($this->result) {
            return mysqli_affected_rows($this->conn);
        }
    }

    public function getResultByArray()
    {
        if ($this->result) {
            $rows = $row = array();
            while ($row = mysqli_fetch_array($this->result, MYSQLI_BOTH)) {

                $rows[] = $row;
            }
            return $rows;
        }
    }

    public function getResultByArrayNum()
    {
        if ($this->result) {
            $rows = $row = array();
            while ($row = mysqli_fetch_array($this->result, MYSQLI_NUM)) {

                $rows[] = $row;
            }
            return $rows;
        } else {
            return NULL;
        }
    }

    public function getResultByArrayAssoc()
    {
        if ($this->result) {
            $rows = $row = array();
            while ($row = mysqli_fetch_array($this->result, MYSQLI_ASSOC)) {

                $rows[] = $row;
            }
            return $rows;
        }
    }

    public function getResultByOneArray()
    {
        if ($this->result) {
            return mysqli_fetch_array($this->result, MYSQLI_BOTH);
        }
    }

    public function getResultByOneObject()
    {
        if ($this->result) {
            return mysqli_fetch_object($this->result);
        }
    }
}

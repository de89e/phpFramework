<?php

namespace framework\system\database\provider;

use framework\system\database\dbInterface;
use framework\system\database\dbSqlCommandInterface;

class ProviderMysql extends dbInterface implements dbSqlCommandInterface
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
        if (!function_exists('mysql_connect')) {
            die('Mysql extension error!');
        }
        $this->conn = mysql_connect($this->source, $this->username, $this->password);
        if ($this->conn) {
            mysql_select_db($this->database, $this->conn);
            mysql_query('SET character_set_connection='
                . $this->charset . ', character_set_results='
                . $this->charset . ', character_set_client=binary', $this->conn);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function Close()
    {
        if ($this->conn) {
            mysql_close($this->conn);
        }
    }

    public function Query($sql)
    {
        if ($this->conn) {
            return $this->result = mysql_query($sql, $this->conn);
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
            return mysql_num_rows($this->result);
        }
    }

    public function getResultAffectedCount()
    {
        if ($this->result) {
            return mysql_affected_rows($this->conn);
        }
    }

    public function getResultByArray()
    {
        if ($this->result) {
            $rows = $row = array();
            while ($row = mysql_fetch_array($this->result, MYSQL_BOTH)) {

                $rows[] = $row;
            }
            return $rows;
        }
    }

    public function getResultByArrayNum()
    {
        if ($this->result) {
            $rows = $row = array();
            while ($row = mysql_fetch_array($this->result, MYSQL_NUM)) {

                $rows[] = $row;
            }
            return $rows;
        }
    }

    public function getResultByArrayAssoc()
    {
        if ($this->result) {
            $rows = $row = array();
            while ($row = mysql_fetch_array($this->result, MYSQL_ASSOC)) {

                $rows[] = $row;
            }
            return $rows;
        }
    }

    public function getResultByOneArray()
    {
        if ($this->result) {
            return mysql_fetch_array($this->result, MYSQL_BOTH);
        }
    }

    public function getResultByOneObject()
    {
        if ($this->result) {
            return mysql_fetch_object($this->result);
        }
    }
}

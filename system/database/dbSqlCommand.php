<?php

namespace framework\system\database;

class dbSqlCommand extends dbObject implements dbSqlCommandInterface
{

    public $Provider = null;
    public $dbConnection = null;
    public $sqlString = null;

    public function __construct(&$dbConnection = null)
    {

        $this->dbConnection = $dbConnection;
        $this->Provider = &$dbConnection->Provider;
    }

    /*
     * 需要先在Connection里注册
     * 更改Provider 
     */

    public function setProvider($Provider = 'mysql')
    {
        $Provider = 'Provider' . $Provider;
        if (array_key_exists($Provider, $this->dbConnection->Connections)) {
            $this->Provider = &$this->dbConnection->Connections()[$Provider];
        }
    }

    public function exec($sqlString)
    {

        if (is_object($sqlString) && method_exists($sqlString, 'toString')) {
            $this->sqlString = $sqlString->toString();
        } elseif (is_string($sqlString)) {
            $this->sqlString = $sqlString;
        }
        
        return $this->Provider->query($this->sqlString);
    }

    public function getLastInsertId()
    {

        return $this->Provider->getLastInsertId();
    }

    public function getResultCount()
    {

        return $this->Provider->getResultCount();
    }

    public function getResultAffectedCount()
    {
        return $this->Provider->getResultAffectedCount();
    }

    public function getResultByArray()
    {

        return $this->Provider->getResultByArray();
    }

    public function getResultByArrayNum()
    {

        return $this->Provider->getResultByArrayNum();
    }

    public function getResultByArrayAssoc()
    {

        return $this->Provider->getResultByArrayAssoc();
    }

    public function getResultByOneObject()
    {

        return $this->Provider->getResultByOneObject();
    }

    public function getResultByOneArray()
    {

        return $this->Provider->getResultByOneArray();
    }
}

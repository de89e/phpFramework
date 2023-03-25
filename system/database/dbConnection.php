<?php

namespace framework\system\database;

class dbConnection extends dbObject
{

    public $Provider = 'ProviderMysql';
    private $Connections = [];

    /**
     *
     * 链接字符串实例
     */
    private $ConnectionString;

    /*
     * $ConnectionString like 
     * Provider=mysql;Source=192.168.1.80;Username=root;Password=cuijiahai;Database=test;Prefix=cc_
     */

    public function Open($ConnectionString = null)
    {

        $this->setConnectionString($ConnectionString);


        $provider = $this->Provider = 'Provider' . $this->ConnectionString->Provider;


        $class = 'framework\\system\\database\\provider\\' . $this->Provider;

        $this->Connections[$provider] = $this->Provider = new $class;

        $this->Provider->setSource($this->ConnectionString->Source);
        $this->Provider->setUsername($this->ConnectionString->Username);
        $this->Provider->setPassword($this->ConnectionString->Password);
        $this->Provider->setDatabase($this->ConnectionString->Database);
        $this->Provider->setCharset($this->ConnectionString->Charset);
        
        if (!$this->Provider->Open()) {
            _notice("An error occurred while establishing a database connection");
        }
    }

    public function CloseAll()
    {
        if (is_null($this->Connections)) {
            return FALSE;
        }
        foreach ($this->Connections as $provider => $connection) {
            if (is_object($connection)) {
                $connection->Close();
            }
        }
        return TRUE;
    }

    private function setConnectionString($ConnectionString)
    {
        if (is_object($ConnectionString) && $ConnectionString instanceof dbConnectionString) {
            $this->ConnectionString = $ConnectionString;
        }
    }

    public function Connections()
    {
        return $this->Connections;
    }

    public function ConnectionString()
    {
        return $this->ConnectionString;
    }

    public function &Conn()
    {
        return $this->Provider->conn;
    }

    public function __destruct()
    {
        $this->CloseAll();
    }
}

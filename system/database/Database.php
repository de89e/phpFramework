<?php

/*
  ////////////////////////////////////////////////////////////
  //
  //建立数据库链接
  //
  ////////////////////////////////////////////////////////////


  $connStr = new dbConnectionString('Provider=mysql;Source=192.168.1.254;Username=root;Password=cuijiahai;Database=test');
  $conn = new dbConnection($connStr);
  $conn->Open();
  $sqlCommand = new dbSqlCommand($conn);
  $sqlString = new dbSqlString;
  ////////////////////////////////////////////////////////////
  //
  //执行数据库查询
  //
  ////////////////////////////////////////////////////////////

  $sqlString->setSelect('*');
  $sqlString->setTable('contacts');
  $sqlCommand->exec($sqlString);

  ////////////////////////////////////////////////////////////
  //
  //处理查询数据
  //
  ////////////////////////////////////////////////////////////
  $store = new dbDataStore();
  $store->createMapping([
  [
  'name' => 'id',
  'mapping' => 'id'
  ], [
  'name' => 'contact',
  'mapping' => 'contact'
  ], [
  'name' => 'cellphone',
  'mapping' => 'cellphone'
  ], [
  'name' => 'address',
  'mapping' => 'address'
  ]
  ]);
  $store->loadArray($sqlCommand->arrayResult);
  $store->setProperty('success', true);
  echo $store->getJSON();
 * 
 */
////////////////////////////////////////////////////////////
//
//建立数据库链接
//
////////////////////////////////////////////////////////////

namespace framework\system\database;

use framework;
use framework\system\kernel\Component;
use framework\system\data\CDbDataStore;

class Database extends Component {

    public $connStr = '';
    public $dbConnection = '';
    public $sqlCommand = '';
    public $sqlString = '';
    private $started = '';

    function init() {
        framework::cm()->get('com.config')->addMappingFile([
            DIR_APPLICATION . DS . 'config' . DS . 'database' . EXT,
        ]);
    }

    public function start($database_config_index = '') {


        if (empty($database_config_index)) {
            $database_config_index = 'mysqli';
        }
        $conn_config = framework::cm()->get('com.config')->get('application.database.' . $database_config_index);
        if (!empty($conn_config) && $this->started != $database_config_index) {
            
            $this->connStr = new dbConnectionString($conn_config);
            
            $this->dbConnection = new dbConnection();
            
            $this->dbConnection->Open($this->connStr);
            
            $this->sqlCommand = new dbSqlCommand($this->dbConnection);
            $this->sqlString = new dbSqlString;
            $this->started = $database_config_index;
        }
    }

    public function end() {
        ;
    }

    public function sqlString() {

        return $this->sqlString;
    }

    public function sqlCommand() {

        return $this->sqlCommand;
    }
    public function __destruct() {
        ;
    }

}

<?php

namespace framework\system\database;

abstract class dbInterface {

    public $source;
    public $username;
    public $password;
    public $database;
    public $charset;

    abstract public function setSource($source);

    abstract public function setUsername($username);

    abstract public function setPassword($password);

    abstract public function setDatabase($database);

    abstract public function setCharset($charset);

    abstract public function Query($sql);
    /*
     * 打开这个Provider的数据链接。
     */

    abstract public function Open();
    /*
     * 关闭这个Provider的数据链接。
     */

    abstract public function Close();
}

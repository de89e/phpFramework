<?php

namespace framework\system\database;

class dbConnectionString extends dbObject {

    private $String = '';
    protected $Provider = 'mysql';
    protected $Source = 'localhost';
    protected $Username = 'root';
    protected $Password = 'root';
    protected $Database = 'test';
    protected $Charset = 'utf-8';
    protected $Prefix = '';
    protected $Prefix_symbol = '#@';

    public function __construct($String = null) {
        $this->String($String);
    }

    /*
     * $ConnectionString like 
     * Provider=mysql;Source=192.168.1.80;Username=root;Password=cuijiahai;Database=test;Prefix=cc_
     */

    public function String($String = null) {
        if (!empty($String)) {
            $this->String = $String;
        }
        $connStrObj = self::parameterToObject($this->String, TRUE);

        $this->Provider = isset($connStrObj->Provider) ? $connStrObj->Provider : NULL;
        $this->Source = isset($connStrObj->Source) ? $connStrObj->Source : NULL;
        $this->Username = isset($connStrObj->Username) ? $connStrObj->Username : NULL;
        $this->Password = isset($connStrObj->Password) ? $connStrObj->Password : NULL;
        $this->Database = isset($connStrObj->Database) ? $connStrObj->Database : NULL;
        $this->Charset = isset($connStrObj->Charset) ? $connStrObj->Charset : NULL;
        $this->Prefix = isset($connStrObj->Prefix) ? $connStrObj->Prefix : NULL;
        $this->Prefix_symbol = isset($connStrObj->Prefix_symbol) ? $connStrObj->Prefix_symbol : NULL;

        if (($this->Prefix_symbol != '#@') && !empty($this->Prefix_symbol)) {
            define('SQL_STR_PRIFIX_SYMBOL', $this->Prefix_symbol);
        } else {
            define('SQL_STR_PRIFIX_SYMBOL', '#@');
        }
        if (!empty($this->Prefix)) {
            define('SQL_STR_PRIFIX', $this->Prefix);
        }
        /*
         * 
         */
        return 'Please use function toString()';
    }

    /*
     * dbConnectionString Object to String
     */

    public function toString() {

        return $this->String;
    }

    /**
     * Array To Object
     * @param array $array
     * @return object
     */
    public static function arrayToObject($array) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::arrayToObject($value);
            }
        }
        $object = (object) $array;
        return $object;
    }

    /**
     * ['para1:value1','para2:value2']
     * @param type $parameter
     */
    public static function parameterToObject($parameters, $comma = FALSE) {
        $array = static::parameterToArray($parameters, $comma);
        return static::arrayToObject($array);
    }

    public static function parameterToArray($parameters, $comma = FALSE) {

        $array = [];
        if ($comma && is_string($parameters)) {
            $parameters = explode(';', $parameters);
        }
        foreach ($parameters as $parameter) {
            if (is_array($parameter)) {
                
            } elseif (is_string($parameter)) {
                $parameter = explode('=', $parameter, 2);
            }
            if (isset($parameter[0])) {
                if (isset($parameter[1])) {
                    $parameter[1] = empty($parameter[1]) ? false : $parameter[1];
                } else {
                    $parameter[1] = false;
                }
                $array[$parameter[0]] = $parameter[1];
            }
        }
        return $array;
    }

}

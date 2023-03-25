<?php

/**
 * @author lei@congshan.net
 */

namespace framework\system\kernel;

class Conversion {

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
    public static function paramToObject($parameters, $comma = FALSE) {
        $array = static::paramToArray($parameters, $comma);
        return static::arrayToObject($array);
    }

    public static function paramToArray($parameters, $comma = FALSE) {

        $array = [];
        if ($comma && is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }
        foreach ($parameters as $parameter) {
            if (is_array($parameter)) {
                
            } elseif (is_string($parameter)) {
                $parameter = explode(':', $parameter, 2);
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

    public static function ipByClass($ip, $class = 'D') {
        $IPArray = explode('.', $ip);
        switch ($class) {
            case 'D':$IP = $IPArray[0] . '.' . $IPArray[1] . '.' . $IPArray[2] . '.' . $IPArray[3];
                break;
            case 'C':$IP = $IPArray[0] . '.' . $IPArray[1] . '.' . $IPArray[2] . '.' . $IPArray[3] = 0;
                break;
            case 'B':$IP = $IPArray[0] . '.' . $IPArray[1] . '.' . $IPArray[2] = 0 . '.' . $IPArray[3] = 0;
                break;
        }

        return $IP;
    }

    public static function convertString($string, $fromCode = 'gbk', $toCode = 'utf-8') {
        if (is_string($string)) {
            if (function_exists('mb_convert_encoding')) {
                return mb_convert_encoding($string, $toCode, $fromCode);
            } else {
                return iconv($fromCode, $toCode, $string);
            }
        }
        if (is_array($string)) {
            foreach ($string as $key => $value) {
                $string[$key] = self::convertString($value, $fromCode, $toCode);
            }
            return $string;
        }
    }

}

<?php

/**
 * 自PHP5.4起系统默认关闭自动转义，框架将设定为所有的版本都不转义。
 */
if (function_exists('get_magic_quotes_gpc')) {

    if (get_magic_quotes_gpc()) {

        function stripslashes_gpc($value)
        {
            if (is_array($value)) {
                $value = array_map('stripslashes_gpc', $value);
            } else {
                $value = stripslashes($value);
            }
            return $value;
        }

        $_POST = array_map('stripslashes_gpc', $_POST);
        $_GET = array_map('stripslashes_gpc', $_GET);
        $_COOKIE = array_map('stripslashes_gpc', $_COOKIE);
        $_REQUEST = array_map('stripslashes_gpc', $_REQUEST);
    }
}

use framework\system\kernel\ExceptionEx;
use framework\system\manager\ComponentManager;
use framework\system\manager\EventManager;
use framework\system\manager\MessageManager;

class framework
{

    protected static $componentManager;
    protected static $messageManager;
    protected static $eventManager;

    /**
     * 保存CLI模式下传入的参数
     * @static Array
     */
    public static $argv = [];
    public static $cli = false;

    /**
     * @param $argv
     */
    public static function main($argv)
    {
        /*
         * 设定PHP超时
         */
        set_time_limit(FRAMEWORK_TIMEOUT_LIMIT);

        static::$componentManager = new ComponentManager;

        static::$messageManager = new MessageManager;

        static::$eventManager = new EventManager;

        if (!empty($argv)) {
            /*
             * 保存参数
             */
            static::$argv = $argv;
            static::$cli = true;
        }

        //echo spl_autoload_functions();
        /*
         * 加载核心组件
         */
        static::cm()->init();
        static::em()->init();
        static::mm()->init();
        do {

            /*
             * 执行事件
             */
            static::em()->erun();

            /*
             * 等待
             */
            usleep(MICRO_TIME_SLEEP);
        } while (!static::mm()->get('message.framework.exit'));
    }

    /**
     * @return mixed
     */
    public static function &cm()
    {

        return static::$componentManager;
    }

    /**
     * @return mixed
     */
    public static function &em()
    {

        return static::$eventManager;
    }

    /**
     * @return mixed
     */
    public static function &mm()
    {

        return static::$messageManager;
    }

    /**
     * @return mixed
     */
    public static function componentManager()
    {

        return static::$componentManager;
    }

    /**
     * @return mixed
     */
    public static function eventManager()
    {

        return static::$eventManager;
    }

    /**
     * @return mixed
     */
    public static function messageManager()
    {

        return static::$messageManager;
    }

    /**
     * @param null $string
     * @return mixed|null
     */
    public static function getOption($string = null)
    {

        $return = null;
        foreach (static::$argv as $value) {
            if ($string == $value) {
                $return = current(static::$argv);
            }
        }
        return $return;
    }

    /**
     *
     */
    public static function shutdownHandler()
    {
        $GLOBALS['time_CongPHP_end'] = microtime(true);
        $GLOBALS['time_CongPHP_use'] = sprintf('%0.5f', $GLOBALS['time_CongPHP_end'] - $GLOBALS['time_CongPHP_start']);

        /**
         * Application Output
         */
        $output = framework::mm()->get('message.application.output');

        /**
         * The finnal time elapse
         */
        if (PROCESSED_TIME_DISPLAY) {
            $processed_time_display = framework::mm()->get('message.framework.elapse');
            $output = str_replace('{{elapse}}', $GLOBALS['time_CongPHP_use'], $output);
            if ($processed_time_display === false) {
                echo $output;
                return;
            } else if (framework::mm()->get('message.response.contentType') != "" || $processed_time_display == 'header') {
                header("X-CongPHP-Processed-Time: " . $GLOBALS['time_CongPHP_use']);
                echo $output;
                return;
            } else {
                echo $output;
                echo '<!--CongPHP_Processed_in_', $GLOBALS['time_CongPHP_use'], '-->';
                return;
            }
        } else {
            echo $output;
        }
    }

    /**
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {

        ExceptionEx::errorHandler($errno, $errstr, $errfile, $errline);
    }

    /**
     * @param $e
     */
    public static function exceptionHandler($e)
    {
        ExceptionEx::exceptionHandler($e);
    }

}

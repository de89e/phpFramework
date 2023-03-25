<?php

/*
 * 在框架中禁止出现只有Function的脚本文件，如需要使用公共脚本文件，请使用静态类写法替换，并在加载器中进行映射
 * 加载器提供两个函数进行映射注册
 *
 * psr4AutoLoader::addClass2Dir();
 *
 * 在需要实现别名文件名的时候注册，例如类名为 FRAMEWORK 文件名保存为F.php
 * psr4AutoLoader::addClass2File();
 *
 *
 */

//开始性能计时
$GLOBALS['time_CongPHP_start'] = microtime(true);

const _VERSION_PHP = '5.4.0';
const _VERSION_CONGPHP = '0.0.1';
const DS = DIRECTORY_SEPARATOR;
const EXT = '.php';
const CHAR_SET = 'UTF-8';
const MICRO_TIME_SLEEP = 1;

//开始定义框架常量
defined('_DEBUG') ? null : define('_DEBUG', false);
define('DIR_FRAMEWORK', rtrim(__DIR__, DS));
define('DIR_SYSTEM', DIR_FRAMEWORK . DS . 'system');

//版本检查
phpversion() < _VERSION_PHP ? _die('CongPHP需要的最低PHP版本应不小于PHP_V_' . _VERSION_PHP, __FILE__, __LINE__) : null;
defined('DIR_HTDOCS') ? null : define('DIR_HTDOCS', rtrim(__DIR__, DIRECTORY_SEPARATOR) . '/../htdocs');
defined('DIR_APPLICATION') ? true : _die('你必须在入口文件（一般为Index.php)定义站点应用目录!<br/>例如:  define(\'DIR_APPLICATION\',\'/../application\')', __FILE__, __LINE__);
//开始定义目录常量
define('DIR_APPLICATION_VIEW', DIR_APPLICATION . DS . 'view');
require DIR_FRAMEWORK . DS . 'AutoLoader' . DS . 'loader' . EXT;
//注册加载器
spl_autoload_register(['psr4AutoLoader', 'classLoader']);
spl_autoload_extensions(EXT);
//注册错误处理函数
_DEBUG == true ? null : set_error_handler(['framework', 'errorHandler']);
_DEBUG == true ? null : set_exception_handler(['framework', 'exceptionHandler']);
//注册停止函数
register_shutdown_function(['framework', 'shutdownHandler']);

//正确显示框架致命错误
function _die($message, $file = "", $line = "")
{
    header('HTTP/1.1 500 Internal Server Error');
    header('Status: 500 Internal Server Error');
    header('X-PHP-Framework: CongPHP');
    header('Content-type:text/html;charset=' . CHAR_SET);
    die('
            <style type="text/css">
                table.tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #729ea5;border-collapse: collapse;}
                table.tftable th {font-size:12px;background-color:#acc8cc;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;}
                table.tftable tr {background-color:#d4e3e5;}
                table.tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;}
            </style>

            <table id="tfhover" class="tftable" border="1">
                <tr>
                    <th>' . "STOP" . '</th>
                    <th>' . $file . '</th>
                    <th>' . $line . '</th>
                </tr>
                <tr>
                    <td colspan="3">' . $message . '</td>
                </tr>
            </table>
            ');
    /*
die("\r\n" . '<br/><div style=\'font-size: 87.87pt;\'>:( Oooooooooops </div> ' .
"\r\n" . '<br/><div style=\'font-size: 16pt;\'>Stop:' . $message . '</div>' .
"\r\n" . '<br/>File:' . $file .
"\r\n");
 *
 */
}

function _notice($message, $file = "", $line = "")
{
    echo '
            <style type="text/css">
                table.tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #729ea5;border-collapse: collapse;}
                table.tftable th {font-size:12px;background-color:#acc8cc;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;}
                table.tftable tr {background-color:#d4e3e5;}
                table.tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;}
            </style>

            <table id="tfhover" class="tftable" border="1">
                <tr>
                    <th>' . "Notice" . '</th>
                    <th>' . $file . '</th>
                    <th>' . $line . '</th>
                </tr>
                <tr>
                    <td colspan="3">' . $message . '</td>
                </tr>
            </table>
            ';
}

final class cong extends framework
{
    /**
     * Thanks for using CongPHP！
     * 感谢您使用CongPHP!
     *
     * Happy Every Day ; )
     * 祝您生活愉快; )
     */
}

//Here We Go
cong::main(isset($argv) ? $argv : null);

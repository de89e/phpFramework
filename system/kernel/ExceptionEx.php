<?php

namespace framework\system\kernel;

use framework;

class ExceptionEx extends \Exception {

    static public function errorHandler($errno = NULL, $errstr = NULL, $errfile = NULL, $errline = NULL) {
        /**
         * 
         */
        $levels = array(
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parsing Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Runtime Notice'
        );

        /*
         * 如果这个错误类型没有包含在error_reporting里
         * @会修改错误级别
         */
        if (!(error_reporting() & $errno)) {

            return TRUE;
        }

        /**
         * 
         */
        foreach ($levels as $key => $value) {

            if ($key == $errno) {
                $errno = $value;
                break;
            }
        }

        /**
         * 
         */
        $errorData = array(
            'httpCode' => 500,
            'errno' => $errno,
            'errstr' => $errstr,
            'errfile' => $errfile,
            'errline' => $errline
        );
        self::display($errorData);
    }

    static public function exceptionHandler($e) {
        $errorData = array(
            'httpCode' => 404,
            'errno' => 'Exception',
            'errstr' => $e->getMessage(),
            'errfile' => $e->getFile(),
            'errline' => $e->getLine()
        );

        /**
         * 
         */
        //var_dump($errorData);
        //var_dump($e);
    }

    static public function display($errorData = "") {

        if (empty($errorData)) {
            return;
        }
        if (!empty($errorData['httpCode'])) {
            framework::mm()->send("message.response.status", $errorData['httpCode']);
        }
        echo '
            <style type="text/css">
                table.tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #729ea5;border-collapse: collapse;}
                table.tftable th {font-size:12px;background-color:#acc8cc;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;}
                table.tftable tr {background-color:#d4e3e5;}
                table.tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;}
            </style>

            <table id="tfhover" class="tftable" border="1">
                <tr>
                    <th>' . $errorData['errno'] . '</th>
                    <th>' . $errorData['errfile'] . '</th>
                    <th>' . $errorData['errline'] . '</th>
                </tr>
                <tr>
                    <td colspan="3">' . $errorData['errstr'] . '</td>
                </tr>
            </table>
            ';
        //echo '<pre>', $e->xdebug_message, '</pre>';
    }

}

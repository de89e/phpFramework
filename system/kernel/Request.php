<?php

namespace framework\system\kernel;

use framework;

class Request extends Component
{

    public function init()
    {
        /*
        preg_match_all('/[?|&]([A-Za-z0-9\_\-]+)=([A-Za-z0-9\:\_\%\.\-\/]+)/', $this->url, $OUT, PREG_SET_ORDER);
        for ($i = 0; !empty($OUT[$i]); $i++) {
        $_GET[$OUT[$i][1]] = urldecode($OUT[$i][2]);
        }
         *
         *
         * example
         * http://127.0.0.3/htdocs/index.php/common/captcha/index_action/goods/12345.html?abc=123&bbc=1&path=common/path
         */
        //var_dump($_SERVER);
        preg_match_all('/[?|&]([A-Za-z0-9\_\-]+)=([\sA-Za-z0-9\:\;\_\%\.\-\/{}\(\)|",\[\]]+)/', $_SERVER['REQUEST_URI'], $OUT, PREG_SET_ORDER);
        for ($i = 0;!empty($OUT[$i]); $i++) {
            $_GET[$OUT[$i][1]] = urldecode($OUT[$i][2]);
        }

        //开启全局缓冲区，避免Header错误
        ob_start();
    }

    public function start()
    {
        /**
         * Payload Data
         */
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);
        $_POST['payload'] = $data;

    }

    public function end()
    {
        unset($_POST);
        unset($_GET);
    }

    public function get($get = 'get.null')
    {

        $_get_var = explode('.', $get);
        $type = isset($_get_var[0]) ? $_get_var[0] : null;
        $name = isset($_get_var[1]) ? $_get_var[1] : null;
        if (empty($type)) {
            return null;
        }

        $_r = null;
        if ('get' == $type) {
            $_r = isset($_GET[$name]) ? str_replace('$', '', $_GET[$name]) : null;
        }
        if ('post' == $type) {
            $_r = isset($_POST[$name]) ? $_POST[$name] : null;
        }
        if ('payload' == $type) {
            $_r = isset($_POST['payload'][$name]) ? $_POST['payload'][$name] : null;
        }
        if ('cookie' == $type) {
            $_r = isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
        }
        if ('server' == $type) {
            $_r = isset($_SERVER[$name]) ? $_SERVER[$name] : null;
        }
        if ('all' == $type) {
            if ('get' == $name) {
                //
            }
        }
        $_r = str_replace(['\\\\u0026'], ['&'], $_r);
        return $_r;
    }

    public function set($set, $value)
    {

        $_set = explode('.', $set)[0];
        $type = isset($_set[0]) ? $_set[0] : null;
        $name = isset($_set[1]) ? $_set[1] : null;
        if (empty($type)) {
            return null;
        }

        $_r = null;
        if ('get' == $type) {
            $_GET[$name] = $value;
        }
        if ('post' == $type) {
            $_POST[$name] = $value;
        }
        if ('cookie' == $type) {
            $_COOKIE[$name] = $value;
        }
    }

    public function getRemoteIP($IPLevel = 'D')
    {
        $remoteIP = $_SERVER['REMOTE_ADDR'];
        $IPArray = explode('.', $remoteIP);
        switch ($IPLevel) {
            case 'D':
                $remoteIP = $IPArray[0] . '.' . $IPArray[1] . '.' . $IPArray[2] . '.' . $IPArray[3];
                break;
            case 'C':
                $remoteIP = $IPArray[0] . '.' . $IPArray[1] . '.' . $IPArray[2] . '.' . $IPArray[3] = 0;
                break;
            case 'B':
                $remoteIP = $IPArray[0] . '.' . $IPArray[1] . '.' . $IPArray[2] = 0 . '.' . $IPArray[3] = 0;
                break;
        }
        return $remoteIP;
    }

    public function getRemoteIPX($IPLevel = 'D')
    {
        if (!isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return false;
        }
        if (empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return false;
        }
        $remoteIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
        $IPArray = explode('.', $remoteIP);
        switch ($IPLevel) {
            case 'D':
                $remoteIP = $IPArray[0] . '.' . $IPArray[1] . '.' . $IPArray[2] . '.' . $IPArray[3];
                break;
            case 'C':
                $remoteIP = $IPArray[0] . '.' . $IPArray[1] . '.' . $IPArray[2] . '.' . $IPArray[3] = 0;
                break;
            case 'B':
                $remoteIP = $IPArray[0] . '.' . $IPArray[1] . '.' . $IPArray[2] = 0 . '.' . $IPArray[3] = 0;
                break;
        }
        return $remoteIP;
    }

    public function getHost()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public function getRemotePort()
    {

        $remotePort = $_SERVER['REMOTE_PORT'];

        return $remotePort;
    }

    public function getPathInfo()
    {
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : null;
        return $path_info;
    }

    public function getRequestURL()
    {
        $applicationObj = framework::cm()->get('com.application');
        if (framework::$argv) {
            return framework::getOption('-url');
        }
        $length = strlen($applicationObj->getSiteRoot());
        if (isset($_SERVER['REQUEST_URI'])) {
            $temp = explode('?', $_SERVER['REQUEST_URI']);
            $temp = array_shift($temp);
            if ($_SERVER['SCRIPT_NAME'] == $temp) {
                $cong_request_url = dirname($_SERVER['REQUEST_URI']);
            } else {
                $cong_request_url = $_SERVER['REQUEST_URI'];
            }

            if (substr($cong_request_url, 0, $length) == $applicationObj->getSiteRoot()) {
                $cong_request_url = substr($cong_request_url, $length);
            }

            return $cong_request_url;
        }
    }

    public function getReferer()
    {

        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }

    public function getQueryString()
    {
        return $_SERVER['QUERY_STRING'];
    }

    public function getServerIP()
    {
        return $_SERVER['SERVER_ADDR'];
    }

    public function getServerPort()
    {
        return $_SERVER['SERVER_PORT'];
    }

    public function getRequestTime()
    {
        if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            return $_SERVER['REQUEST_TIME_FLOAT'];
        }
        return $GLOBALS['cong_start_time'];
    }

    public function getAllHeaders($getHeaderSort = false)
    {
        if (function_exists("getallheaders")) {
            $allheaders = getallheaders();
        } else {
            $allheaders = $_SERVER;
        }
        $headers = [];
        $headers_sort = [];
        $i = 0;
        foreach ($allheaders as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $name = str_replace('_', '-', substr($name, 5));
            }
            switch (strtolower($name)) {
                case 'host':
                    $headers['host'] = $value;
                    $headers_sort['host'] = $i;
                    $i++;
                    break;
                case 'user-agent':
                    $headers['user-agent'] = $value;
                    $headers_sort['user_agent'] = $i;
                    $i++;
                    break;
                case 'accept':
                    $headers['accept'] = $value;
                    $headers_sort['accept'] = $i;
                    $i++;
                    break;
                case 'accept-language':
                    $headers['accept-language'] = $value;
                    $headers_sort['language'] = $i;
                    $i++;
                    break;
                case 'accept-encoding':
                    $headers['accept-encoding'] = $value;
                    $headers_sort['encoding'] = $i;
                    $i++;
                    break;
                case 'connection':
                    $headers_sort['connection'] = $i;
                    $i++;
                    break;
            }
        }
        if ($getHeaderSort) {
            $headers['headers_sort'] = $headers_sort;
        }
        return $headers;
    }

    /*
     * ['url']="http://www.baidu.com
     * ['data']="&
     * ['method']=post
     * ['header']=""
     * ['responseHeader']=false
     */

    public function curl($dataArray = "")
    {
        if (!isset($dataArray['url'])) {
            return false;
        }

        if (!function_exists("curl_init")) {
            return false;
        };
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $dataArray['url']); //抓取指定网页
        if (isset($dataArray['method'])) {
            if (strtolower($dataArray['method']) == 'get') {} else {
                curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
            }
        } else {
            curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
        }

        if (isset($dataArray['header'])) {
            $header = $dataArray['header'];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if (isset($dataArray['ssl'])) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (isset($dataArray['responseHeader'])) {
            curl_setopt($ch, CURLOPT_HEADER, 1); //设置返回header
        } else {
            curl_setopt($ch, CURLOPT_HEADER, 0); //设置返回header
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上

        foreach ($dataArray['params'] as $key => $value) {
            if (isset($params)) {
                $params .= '&' . $key . '=' . str_replace(['&'], ['\\\\u0026'], $value);
            } else {
                $params = '&' . $key . '=' . str_replace(['&'], ['\\\\u0026'], $value);
            }

        }
        //echo $params;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $data = curl_exec($ch); //运行curl
        curl_close($ch);
        return $data;
    }

    private function _safe_uri($uri)
    {
        $uri = str_replace('//', '/', $uri);
        $uri = str_replace('../', '', $uri);
        /**
         * 参考自CodeIgniter
         */
        $bad = array('$', '(', ')', '%28', '%29');
        $good = array('&#36;', '&#40;', '&#41;', '&#40;', '&#41;');

        return str_replace($bad, $good, $uri);
    }

    private function _safe_request($var)
    {
        $var = preg_replace('/[\*\|\?\=\>\<\.\-]+/', '_', $var);
        return $var;
    }
}

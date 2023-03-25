<?php

namespace framework\system\kernel;

use framework;

/**
 */
class Response extends Component
{

    private $status = 'OK';
    private $headers = [];
    public $content = '';
    public $compress = false;

    public function init()
    {
        ;
    }

    public function start()
    {

        framework::em()->push('event.response.header');
        framework::em()->push('event.response.output');
    }

    public function end()
    {
        ;
    }

    public function output()
    {
        $this->content = framework::mm()->get('message.response.content');
        $this->compress = framework::mm()->get('message.response.compress');

        if (framework::mm()->get('message.response.contentType') == "application/json" || framework::mm()->get('message.response.contentType') == "json") {

            if (is_array($this->content) || is_object($this->content)) {
                $this->content = json_encode($this->content);
            }
        }
        while (ob_get_level()) {
            //ob_end_flush();
            $ob_content .= ob_get_contents();
            ob_end_clean();
        }
        $this->content = $ob_content . $this->content;
        /*
         * 如果存在压缩消息则对输出进行压缩。
         */
        if ($this->compress) {
            $this->content = $this->compress($this->compress);
        }

        /*
         * Bye ;)
         */

        framework::mm()->send('message.application.output', $this->content);
    }

    public function header()
    {

        $messages = [
            'message.response.status' => 'status',
            'message.response.redirect' => 'redirect',
            'message.response.contentType' => 'contentType',
            'message.response.noCache' => 'noCache',
            'message.response.expires' => 'expires',
            'message.response.compress' => 'compress',
            'message.response.setHeader' => 'setHeader',
        ];
        foreach ($messages as $message => $method) {

            if (framework::mm()->get($message)) {
                $value = framework::mm()->get($message);
                $this->$method($value);
            }

        }
        if (empty($this->headers)) {
            header('HTTP/1.1 200 OK');
            return false;
        }

        if (headers_sent()) {
            return false;
        }

        foreach ($this->headers as $key => $value) {
            header($value);
        }

        return true;
    }

    public function setHeader($string)
    {
        if (is_string($string)) {
            array_push($this->headers, $string);
        } else {
            foreach ($string as $key => $value) {
                if (is_numeric($key)) {
                    $this->headers[$key] = $value;
                }
            }
        }
    }

    public function contentType($value = null)
    {
        $nocharset = 0;
        if (empty($value)) {
            $contentType = 'text/html';
            $charset = 'utf-8';
        }
        if (is_string($value)) {
            $contentType = $value;
            $charset = 'utf-8';
        }
        if (is_array($value) && !empty($value)) {
            if (count($value) > 1) {
                $contentType = $value[0];
                $charset = $value[1];
            } else {
                $contentType = $value[0];
                $charset = 0;
            }
        }
        $char_set = null;
        if ($charset != 0) {
            $char_set = '; charset=' . $charset;
        } else {
            $nocharset = 1;
        }
        if ($nocharset) {
            $this->setHeader('Content-Type: ' . $contentType);
        } else {
            $this->setHeader('Content-Type: ' . $contentType . $char_set);
        }

        return $this;
    }

    public function noCache()
    {
        $this->setHeader('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
        $this->setHeader('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        $this->setHeader('Pragma: no-cache');
        return $this;
    }

    public function expires($expires = 3600)
    {

        $this->setHeader('Expires: ' . date('r', (time() + $expires)));
        $this->setHeader('Cache-Control: max-age=' . $expires);
        return $this;
    }

    public function redirect($Location = './')
    {
        $this->status(302);
        $this->setHeader(['Location: ' . $Location]);
        return $this;
    }

    public function compress($level = 0)
    {
        $this->compress = $level;

        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)) {
            $encoding = 'gzip';
        }

        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false)) {
            $encoding = 'x-gzip';
        }

        if (!isset($encoding) || ($level < 1 || $level > 9)) {
            return false;
        }

        if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
            return false;
        }

        if (headers_sent()) {
            return false;
        }

        if (connection_status()) {
            return false;
        }

        $this->setHeader('Content-Encoding: ' . $encoding);
        if (empty($this->content)) {
            return false;
        }
        return $this->content = gzencode($this->content, (int) $level);
    }

    public function status($code = 404)
    {
        $status = [
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ', // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded',
        ];
        $this->status = $status[$code];
        $this->setHeader('HTTP/1.1 ' . $code . ' ' . $this->status);
        return $this;
    }

    public function setCookie($name = null, $value = null, $expire = 0, $path = null, $domain = null, $secure = false)
    {
        if (!$name) {
            setcookie($name, $value, $expire, $path, $domain, $secure);
        }
    }

    public function removeCookie($name = null)
    {
        if ($name) {
            setcookie($name, null, time() - 3600 * 24 * 365, '/');
        }
    }

    public function removeAllCookie()
    {

        foreach ($_COOKIE as $key => $value) {
            $this->removeCookie($key);
        }
    }

}

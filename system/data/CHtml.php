<?php

namespace framework\system\data;

use framework;
use framework\system\kernel\Conversion;

class CHtml extends CModel {

    public static function css($src = '') {
        return '<link rel="stylesheet" type="text/css" href="' . $src . '" />';
    }

    public static function image($params) {
        $params = Conversion::paramToObject($params);
        $html = '<img src="';
        $html .= $params->src;
        $html .= '"';
        foreach ($params as $key => $value) {
            if ($key == "src") {
                continue;
            }
            $html .= ' ' . $key . '="' . $value . '" ';
        }
        $html .= '/>';
        return $html;
    }

    public static function redirect($url = "./", $sec = 0, $msg = '') {
        $go = '';
        if ($msg) {
            $go = " <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
                <script>alert(\"{$msg}\")</script>";
        }
        if ($url == "reload") {
            $location = "window.location.reload();";
        } elseif ($url == "replace") {
            $location = "window.location.replace(location.href);";
        } elseif ($url == "goback") {
            $location = "history.go(-1);";
        } else {
            $location = "window.location=\"{$url}\";";
        }
        $sec = isset($sec) ? $sec * 1000 : 0;
        $go .= "
    <script>
        function go(){
          $location
        }
        setTimeout('go()',{$sec});
    </script>";
        return $go;
    }

}

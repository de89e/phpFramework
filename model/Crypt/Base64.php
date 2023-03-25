<?php

class Base64Model extends baseModel {
    /*
     * 字符转码 /   =>   _
     * 
     * 字符转码 +   =>   . 
     * 
     * 字符转码 =   =>   -
     */

    public function encode($data) {
        $data = base64_encode($data);
        $data = str_replace('/', '_', $data);
        $data = str_replace('+', '.', $data);
        $data = str_replace('=', '-', $data);
        return $data;
    }

    public function decode($data) {
        $data = str_replace('_', '/', $data);
        $data = str_replace('.', '+', $data);
        $data = str_replace('-', '=', $data);
        $data = base64_decode($data);
        return $data;
    }

}

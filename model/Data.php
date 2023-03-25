<?php

class DataModel extends baseModel {

    public function getUUID($string = '') {
        $uuid = $string;
        $uuid .= uniqid();
        $uuid .= mt_rand(0, 65500);
        $uuid .= mt_rand(0, 65500);
        $uuid = md5($uuid.microtime());
        $uuid = substr($uuid, 0, 8) .
                '-' .
                substr($uuid, 8, 4) .
                '-' .
                substr($uuid, 12, 4) .
                '-' .
                substr($uuid, 16, 4) .
                '-' .
                substr($uuid, 20, 12);
        return $uuid;
    }

}

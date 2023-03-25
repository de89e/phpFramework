<?php

class Rc4Model extends baseModel {

    public function base64Encrypt($data, $key) {
        $this->loadModel('Crypt\Base64', 'Base64');
        $crypt_data = self::getRc4Encode($data, $key);
        $base64_crypt_data = $this->Base64->encode($crypt_data);
        return $base64_crypt_data;
    }

    public function base64Decrypt($data, $key) {
        $this->loadModel('Crypt\Base64', 'Base64');
        $crypt_data = $this->Base64->decode($data);
        $data = self::getRc4Encode($crypt_data, $key);
        return $data;
    }

    public static function encrypt($data, $key) {
        return self::getRc4Encode($data, $key);
    }

    public static function decrypt($data, $ley) {
        return self::getRc4Encode($data, $key);
    }

    public static function getRc4Encode($data, $pwd) {
        $cipher = '';
        $key[] = "";
        $box[] = "";
        $pwd_length = strlen($pwd);
        $data_length = strlen($data);
        for ($i = 0; $i < 256; $i++) {
            $key[$i] = ord($pwd[$i % $pwd_length]);
            $box[$i] = $i;
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $key[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $data_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $k = $box[(($box[$a] + $box[$j]) % 256)];
            $cipher .= chr(ord($data[$i]) ^ $k);
        }


        return $cipher;
    }

}

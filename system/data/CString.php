<?php

namespace framework\system\data;

class CString {

    protected $string;

    public function __construct($string = NULL) {
        $this->string = $string;
    }

    /**
     * 或许是我开始用PHP的第一个自己的函数。
     * 深埋在这里吧。
     * @param string $str
     * @param integer $start
     * @param integer $cut
     * @param string $charset
     * @return string
     */
    public function cn_substr($str, $start, $cut, $charset = 'utf-8') {
        $rstr = '';
        $word = '';
        $wno = 0;
        for ($i = 0; $i < strlen($str);) {
            if (ord($str[$i]) <= 127 && ord($str[$i]) >= 0) {
                $word = substr($str, $i, 1);
                $i++;
                $rstr.=($wno >= $start && $wno < ($start + $cut)) ? $word : '';
                $wno++;
            } elseif (ord($str[$i]) <= 223 && ord($str[$i]) >= 194) {
                $word = substr($str, $i, 2);
                $i+=2;
                $rstr.=($wno >= $start && $wno < ($start + $cut)) ? $word : '';
                $wno++;
            } elseif (ord($str[$i]) <= 239 && ord($str[$i]) >= 224) {
                $word = substr($str, $i, 3);
                $i+=3;
                $rstr.=($wno >= $start && $wno < ($start + $cut)) ? $word : '';
                $wno++;
            } elseif (ord($str[$i]) >= 240) {
                $word = substr($str, $i, 4);
                $i+=4;
                $rstr.=($wno >= $start && $wno < ($start + $cut)) ? $word : '';
                $wno++;
            }
            if ($wno >= ($start + $cut)) {
                break;
            }
        }
        $this->string = $rstr;
        return $rstr;
    }

}

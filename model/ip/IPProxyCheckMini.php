<?php

class IPProxyCheckMiniModel extends baseModel {

    var $log_file = 'check_proxy_log.txt';
    var $str_log_file = 'proxy_log.txt';
    var $totalProxy = 0;
    var $totalClick = 0;
    var $proxyLevel = '';
    var $str_log = "\r\n";

    function proxyLevel() {
        $HTTPHeaderArray[0] = '_PROXY';
        $HTTPHeaderArray[1] = 'FORWARDED';
        $HTTPHeaderArray[2] = 'HTTP_CACHE_CONTROL';
        $HTTPHeaderArray[3] = 'HTTP_CLIENT_IP';
        $HTTPHeaderArray[4] = 'HTTP_FORM';
        $HTTPHeaderArray[5] = 'HTTP_FORWARDED';
        $HTTPHeaderArray[6] = 'HTTP_MAX_FORWARDS';
        $HTTPHeaderArray[7] = 'HTTP_PRAGMA';
        $HTTPHeaderArray[8] = 'HTTP_PROXY';
        $HTTPHeaderArray[9] = 'HTTP_PROXY_CONNECTION';
        $HTTPHeaderArray[10] = 'HTTP_VIA';
        $HTTPHeaderArray[11] = 'HTTP_X';
        $HTTPHeaderArray[12] = 'HTTP_X_BLUECOAT_VIA';
        $HTTPHeaderArray[13] = 'HTTP_X_FORWARDED_FOR';
        $HTTPHeaderArray[14] = 'HTTP_X_HOST';
        $HTTPHeaderArray[15] = 'HTTP_X_REFERER';
        $HTTPHeaderArray[16] = 'HTTP_X_SERVER_HOSTNAME';
        $HTTPHeaderArray[17] = 'PROXY_';
        $HTTPHeaderArray[18] = 'PROXY_HOST';
        $HTTPHeaderArray[19] = 'PROXY_PORT';
        $HTTPHeaderArray[20] = 'PROXY_REQUEST';
        $HTTPHeaderArray[21] = 'REMOTE_IDENT';
        $HTTPHeaderArray[22] = 'REMOTE_USER';
        $HTTPHeaderArray[23] = 'HTTP_ACCEPT';
        $HTTPHeaderArray[24] = 'HTTP_ACCEPT_LANGUAGE';
        $HTTPHeaderArray[25] = 'HTTP_ACCEPT_ENCODING';
        $HTTPHeaderArray[26] = 'HTTP_USER_AGENT';
        $HTTPHeaderArray[27] = 'HTTP_CONNECTION';
        $HTTPHeaderArray[28] = 'REMOTE_PORT';
        $HTTPHeaderArray[29] = 'HTTP_REFERER';
        $HTTPHeaderArray[30] = 'HTTP_X_CLIENT_IP';
        $HTTPHeaderArray[31] = 'REMOTE_ADDR';
        $this->str_log .= date('Y-m-d h:i:sa') . "\r\n";
        for ($i = 0; $i < Count($HTTPHeaderArray); $i++) {
            if (!isset($_SERVER[$HTTPHeaderArray[$i]])) {
                $_SERVER[$HTTPHeaderArray[$i]] = '';
            } else {

                $this->str_log .= $HTTPHeaderArray[$i] . ' : ' . $_SERVER[$HTTPHeaderArray[$i]] . "\r\n";
            }
        }
        if (
                ($_SERVER['REMOTE_IDENT'] == '')
                and ( $_SERVER['REMOTE_USER'] == '')
                //and ( $_SERVER['HTTP_PRAGMA'] == '')
                and ( $_SERVER['HTTP_VIA'] == '')
                and ( $_SERVER['HTTP_FORM'] == '')
                and ( $_SERVER['HTTP_CLIENT_IP'] == '')//
                and ( $_SERVER['HTTP_FORWARDED'] == '')//
                and ( $_SERVER['HTTP_MAX_FORWARDS'] == '')//
                and ( $_SERVER['HTTP_PROXY'] == '')
                and ( $_SERVER['HTTP_PROXY_CONNECTION'] == '')
                and ( $_SERVER['HTTP_X_CLIENT_IP'] == '')
                and ( $_SERVER['HTTP_X'] == '')//
                and ( $_SERVER['HTTP_X_BLUECOAT_VIA'] == '')//
                and ( $_SERVER['HTTP_X_HOST'] == '')//
                and ( $_SERVER['HTTP_X_REFERER'] == '')//
                and ( $_SERVER['HTTP_X_SERVER_HOSTNAME'] == '')//
                and ( $_SERVER['HTTP_X_BLUECOAT_VIA'] == '')//
                and ( $_SERVER['FORWARDED'] == '')//
                and ( $_SERVER['_PROXY'] == '')
                and ( $_SERVER['PROXY_'] == '')
                and ( $_SERVER['PROXY_HOST'] == '')
                and ( $_SERVER['PROXY_PORT'] == '')
                and ( $_SERVER['PROXY_REQUEST'] == '')
        //and ( $_SERVER['HTTP_CACHE_CONTROL'] == '')
        ) {
            if ($_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
                if ($_SERVER['HTTP_X_FORWARDED_FOR'] == $_SERVER['REMOTE_ADDR']) {
                    return 'HIGH_ANONYMITY';
                } else {
                    return 'ANONYMITY_PROXY';
                }
            } else {
                return 'HIGH_ANONYMITY';
            }
        } else {
            return 'TRANSPARENT_PROXY';
        }
    }

    function checkProxy($log = false) {
        $proxyLevel = $this->proxyLevel();
        $szRemoteHost = isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : null;
        $szRemoteAddr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        /*
          if (empty($szRemoteHost)) {

          $szRemoteHost = gethostbyaddr($szRemoteAddr);
          if (empty($szRemoteHost)) {
          $szRemoteHost = $szRemoteAddr;
          }
          }
          if ($szRemoteHost != $szRemoteAddr) {
          //$proxyLevel = 'TRANSPARENT_PROXY';
          }
         * 
         */
        $this->proxyLevel = $proxyLevel;
        return $proxyLevel;
    }

}

<?php

namespace cong\web;

use cong\framework;
use cong\kernel\CRoute;
use cong\kernel\CConversion;

/**
 * Description of CUrlManager
 *
 * @author Administrator
 */
class CUrlManager extends CRoute {

    private $host = "localhost";
    private $referer = "./";
    private $callback = null;
    private $sso = null;

    public function init() {
        parent::init();
        $this->host = framework::console()->component("system.web.httpRequest")->getHost();
        $this->referer = framework::console()->component("system.web.httpRequest")->getReferer();
    }

    public function createUrl($argv) {
        $protocol = "";
        $host = "";
        $path = "";
        $url = "";
        $argv = CConversion::parameterToObject($argv);
        $_url = "";
        if (property_exists($argv, "protocol")) {
            if (empty($argv->protocol)) {
                $protocol = \framework::console()->component("system.web.httpRequest")->getProtocol();
                $protocol = $protocol . "://";
            } else {
                $protocol = $argv->protocol . "://";
            }
            $_url = $protocol;
        }
        if (property_exists($argv, "host")) {
            if (empty($argv->host)) {
                $host = framework::console()->component("system.web.httpRequest")->getHost();
            } else {
                $host = $argv->host;
            }
            $_url = empty($_url) ? "http://" : $_url;
            $_url = $_url . $host;
        }
        if (!property_exists($argv, "path")) {
            $argv->path = "root";
        }

        if ($argv->path == "root" || $argv->path == "rewrite") {
            $path = framework::console()->component("system.web.httpRequest")->getHttpRoot();
        }
        if ($argv->path == "info") {
            $path = framework::console()->component("system.web.httpRequest")->getScriptName();
        }
        if ($argv->path == "compat") {
            $scriptName = framework::console()->component("system.web.httpRequest")->getScriptName();
            $request = framework::console()->component("system.web.httpRequest")->getRequest();
            if (strstr($request, $scriptName)) {
                $path = framework::console()->component("system.web.httpRequest")->getScriptName();
            } else {
                $path = framework::console()->component("system.web.httpRequest")->getHttpRoot();
            }
        }
        $_url = $_url . $path;

        if (property_exists($argv, "url")) {
            if (empty($argv->url)) {
                $url = "";
            } else {
                $url = $argv->url;
            }
            $_url = $_url . $url;
        }
        return $_url;
    }

    public function __call($name, $arguments) {
        parent::__call($name, $arguments);
        if (isset($this->$name)) {
            return $this->$name;
        }
    }

}

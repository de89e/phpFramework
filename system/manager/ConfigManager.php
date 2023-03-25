<?php

namespace framework\system\manager;

use framework\system\kernel\Manager;

class ConfigManager extends Manager {

    protected $config = [];

    public function init() {
        $this->addMappingFile([
            DIR_FRAMEWORK . DS . 'config' . DS . 'system.config' . EXT,
            DIR_APPLICATION . DS . 'config' . DS . 'application.config' . EXT,
        ]);
       
    }

    public function start() {
        parent::start();
        $this->init();
    }

    public function processMapping($_mapping) {
        parent::processMapping($_mapping);
        if (is_array($_mapping)) {
            foreach ($_mapping as $key => $value) {
                $this->config[$key] = $value;
            }
        }
    }

    public function get($key) {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }
        return FALSE;
    }

    public function set($key, $value = NULL) {

        if (is_string($key)) {
            $this->config[$key] = $value;
            return TRUE;
        }elseif(is_array($key)){

        }
    }

}

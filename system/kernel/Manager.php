<?php

namespace framework\system\kernel;

use framework\system\data\CFile;
use framework\system\data\CObject;

abstract class Manager extends CObject {
    /*
     * mapping_files
     * 定义组建的目录在哪个文件里
     */

    protected $initialized = false;
    protected $mapping_files = [];

    public function init() {

        $this->initialized = true;
    }

    public function start() {
        
    }

    public function isComponentName($name = NULL) {
        if (is_string($name)) {
            $name = substr($name, 0, 3);
            if ($name == 'com') {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function isMessageName($name = NULL) {
        if (is_string($name)) {
            $name = substr($name, 0, 7);
            if ($name == 'message') {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function isEventName($name = NULL) {
        if (is_string($name)) {
            $name = substr($name, 0, 5);
            if ($name == 'event') {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function addMappingFile($file = NULL) {


        if (empty($file)) {
            $file = $this->mapping_files;
        }
        if (is_string($file) && file_exists($file)) {
            $file = realpath($file);

            if (array_key_exists($file, $this->mapping_files)) {
                return TRUE;
            } else {
                $this->mapping_files[$file] = -1;
                $this->loadMapping();
                return TRUE;
            }
        }
        if (is_array($file)) {
            foreach ($file as $v) {
                $this->addMappingFile($v);
            }
        }


        return FALSE;
    }

    public function loadMapping($force = FALSE) {

        foreach ($this->mapping_files as $file => $loaded) {
            if ($loaded == 1 && !$force) {
                continue;
            }
            $_mapping = CFile::requireFile($file, TRUE);
            if ($this->processMapping($_mapping)) {
                $this->mapping_files[$file] = 1;
            }
            //var_dump($this->mapping_files);
        }
    }

    //PHP54不显示 abstract继承错误。
    public function processMapping($_mapping) {
        
    }

}

<?php

namespace framework\system\database;

class dbObject {

    public function __get($name) {
        if (method_exists($this, $name)) {
            return $this->$name();
        } else {
            return $this->$name;
        }
    }

}

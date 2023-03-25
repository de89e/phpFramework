<?php

namespace framework\system\kernel;

use framework\system\data\CObject;

/**
 * Base class of component
 * @author vampire
 */
abstract class Component extends CObject
{

    protected $initialized = false;

    public function __construct()
    {

        /**
         * Initialize
         */
        if ($this->initialized == false) {
            $this->init();
        }
        $this->initialized = true;
    }

    //组建初始化函数
    abstract protected function init();

    abstract public function start();

    abstract public function end();
}

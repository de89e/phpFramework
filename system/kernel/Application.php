<?php

namespace framework\system\kernel;

use framework;
use framework\system\data\CFile;

class Application extends Component
{

    protected $defaultController = 'Index';
    protected $defaultAction = 'Index';
    protected $controllerId = 'Index';
    protected $actionId = 'Index';
    protected $controllerPath = null;
    protected $controller;

    public function test()
    {
        echo '<br/>##########################application_test#############################\r\n';
        echo '<br/>http_root:' . $this->getHttpRoot() . '<br/>\r\n';
        echo '<br/>Host:' . $this->getHttpRoot() . '<br/>\r\n';
        echo '<br/>DocumentRoot:' . $this->getDocumentRoot() . '<br/>\r\n';
        echo '<br/>ScriptName:' . $this->getScriptName() . '<br/>\r\n';

        print_r($_SERVER);
        print_r($GLOBALS);
        echo '<br/>##########################application_test#############################\r\n';
    }

    /**
     * 进行初始化
     */
    public function init()
    {
        $time_zone = framework::cm()->get('com.config')->get('application.timezone');
        if ($time_zone) {
            $this->setTimeZone($time_zone);
        }
    }

    /**
     * 跑起来，压入事件。
     */
    public function start()
    {
        framework::em()->push('event.application.getControllerId');
        framework::em()->push('event.application.getActionId');
        framework::em()->push('event.application.createController');
        framework::em()->push('event.application.runController');
    }

    public function end()
    {
        ;
    }

    public function &theApp()
    {
        return $this->controller;
    }

    public function &App()
    {
        return $this->controller;
    }

    final public function createController()
    {

        if ($this->controllerId) {

            $this->controllerId = ucwords($this->controllerId);

            $controller_class = 'myapp' . $this->controllerId;
            //echo '<br/>controller path' . DIR_APPLICATION . DS . 'controller' . DS . $this->controllerPath . DS . $this->controllerId . EXT;
            if (CFile::requireFile(DIR_APPLICATION . DS . 'controller' . DS . $this->controllerPath . DS . $this->controllerId . EXT)) {
                if (!class_exists($controller_class)) {
                    _die("Controller file error");
                } else {
                    return $this->controller = new $controller_class;
                }
            } elseif (_DEBUG) {
                _die('The controller ' . $this->controllerPath . '/' . $this->controllerId . ' not found!');
            } else {
                if ($this->controllerId == $this->getDefaultController()) {
                    _die("Default Controller not found!");
                }
                $this->controllerId = $this->getDefaultController();
                $this->controllerPath = '';
                framework::em()->prev();
            }
        }
    }

    final public function runController()
    {

        if (is_object($this->controller)) {
            $this->controller->actionId = $this->actionId;
            if (method_exists($this->controller, 'action' . ucwords($this->actionId))) {
                $this->controller->action();
            } else {
                $this->actionId = $this->getDefaultAction();
                framework::em()->prev();
            }
        }
    }

    public function getControllerId()
    {

        $this->controllerId = framework::mm()->get('message.application.controllerId');

        if ($this->controllerId) {
            $controllerId = $this->controllerId;
            if (substr($controllerId, 0, 1) == '/') {
                $controllerId = substr($controllerId, 1, strlen($controllerId));
            }

            $controllerId = explode('/', $controllerId);
            $this->controllerId = array_pop($controllerId);
            $this->controllerPath = implode(DS, $controllerId);
            return $this->controllerId;
        }

        $this->controllerId = $this->getDefaultController();

        if ($this->controllerId) {

            return $this->controllerId;
        }
        $this->controllerId = $this->defaultController;

        return $this->controllerId;
    }

    public function getActionId()
    {
        $this->actionId = framework::mm()->get('message.application.actionId');
        if ($this->actionId) {

            return $this->actionId;
        }
        $this->actionId = $this->getDefaultAction();

        if ($this->actionId) {
            return $this->actionId;
        }
        $this->actionId = $this->defaultAction;
        return $this->actionId;
    }

    public function getDefaultController()
    {
        if (framework::cm()->get('com.config')->get('application.defaultControllerId')) {
            $this->defaultController = framework::cm()->get('com.config')->get('application.defaultControllerId');
        }
        return $this->defaultController;
    }

    public function getDefaultAction()
    {
        if (framework::cm()->get('com.config')->get('application.defaultActionId')) {
            $this->defaultAction = framework::cm()->get('com.config')->get('application.defaultActionId');
        }

        return $this->defaultAction;
    }

    public function checkControllerExist($path)
    {
        return CFile::fileExits(DIR_APPLICATION . DS . 'controller' . DS . $path . EXT);
    }

    public function getErrorController()
    {
        return framework::cm()->get('com.config')->get('application.errorControllerId');
    }

    public function setTimeZone($time_zone = 'Asia/Shanghai')
    {
        $result = date_default_timezone_set($time_zone);
        return $result;
    }

    public function getHttpRoot()
    {

        $theProtocol = $this->getProtocol();
        $theHttpHost = $this->getHttpHost();
        $theSiteRoot = $this->getSiteRoot();
        $theHttpRoot = $theProtocol . '://' . $theHttpHost . $theSiteRoot;
        return $theHttpRoot;
    }

    public function getHttpHost()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $theHttpHost = null;
            $theHttpHost = $_SERVER['HTTP_HOST'];
            return $theHttpHost;
        }
    }

    public function getSiteRoot()
    {
        if (isset($_SERVER['SCRIPT_NAME'])) {
            $theSiteRoot = null;
            $theSiteRoot = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
            return $theSiteRoot;
        }
        return null;
    }

    public function getProtocol()
    {
        if (isset($_SERVER['REQUEST_SCHEME'])) {
            return strstr(strtolower($_SERVER['REQUEST_SCHEME']), 'https') ? 'https' : 'http';
        }
        if (isset($_SERVER['SERVER_PROTOCOL'])) {
            return strstr(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') ? 'https' : 'http';
        }
        return 'http';
    }

    public function getDocumentRoot()
    {
        $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : (isset($_SERVER['CONTEXT_DOCUMENT_ROOT']) ? $_SERVER['CONTEXT_DOCUMENT_ROOT'] : null);
        str_replace($documentRoot, '\\', '/');
        if (substr($documentRoot, -1, 1) == '/') {
            $documentRoot = substr($documentRoot, 0, -1);
        }
        $_SERVER['DOCUMENT_ROOT'] = $documentRoot;
        return $documentRoot;
    }

}

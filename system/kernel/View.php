<?php

namespace framework\system\kernel;

use framework;
use framework\system\data\CFile;
use framework\system\data\CHtml;
use framework\system\data\CModel;

class View extends CModel
{

    protected $tpl = null;
    protected $theme = 'Default';
    protected $suffix = 'tpl';
    protected $htmlObj = null;

    public function __construct()
    {
        $config = framework::cm()->get('com.config');
        if (is_object($config)) {
            $this->suffix = $config->get('application.view.suffix');
            $this->theme = $config->get('application.view.theme');
        }
    }
    public function html()
    {
        if (is_object($this->htmlObj)) {
            return $this->htmlObj;
        } else {
            $this->htmlObj = new CHtml;
            return $this->htmlObj;
        }
    }

    public function createUrl($argv)
    {
        return framework::console()->component("system.web.urlManager")->createUrl($argv);
    }

    public function asset($asset)
    {
        //
        //$prefixPath = $this->createUrl(["path:root", "url:" . $this->assetBasePath]);
        $prefixPath = "/template/default";
        $asset = $prefixPath . $asset;
        return $asset;
    }

    public function tpl($tpl = 'Index')
    {

        $tpl = str_replace(['\\', '..'], '/', $tpl);
        $this->tpl = $tpl;
        $this->tpl = DIR_APPLICATION_VIEW . DS . $this->theme . DS . $this->tpl . '.' . $this->suffix;
    }

    public function layout($layout)
    {
        $layout = DIR_APPLICATION_VIEW . DS . $this->theme . DS . $layout . '.' . $this->suffix;
        if (CFile::fileExits($layout)) {
            require $layout;
        } else {
            _notice('Layout file not found :' . $layout, __FILE__, __LINE__);
        }
    }

    public function assign($data = null, $value = null)
    {
        if (empty($data)) {
            return null;
        }
        if (is_string($data)) {
            $this->$data = $value;
        }
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_string($key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    public function setTheme($theme = 'Default')
    {
        $this->theme = str_replace(['\\', '..'], '/', $theme);
        $this->theme = str_replace(['//'], '/', $theme);
    }

    public function setSuffix($suffix)
    {
        $this->suffix = preg_replace('/[^a-zA-Z]/', '', $suffix);
    }

    public function contents()
    {
        ob_start();
        if (CFile::fileExits($this->tpl)) {
            require $this->tpl;
        } else {

            _notice('Notice:Tpl file not found :' . $this->tpl, __FILE__, __LINE__);
        }
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    protected function render($render)
    {

        $controller_obj = null;
        $_render = $render;

        $render = explode('/', $render);
        $action = ucfirst(array_pop($render));

        $controller = ucfirst(array_pop($render));

        $path = implode('/', $render);

        $controller_class = 'myapp' . $controller;
        if (CFile::requireFile(DIR_APPLICATION . DS . 'controller' . DS . $path . DS . $controller . EXT)) {
            $controller_obj = new $controller_class;
        } else {
            _notice('Notice:Render file fail :' . $_render, __FILE__, __LINE__);
        }
        /**
         *
         */
        $action = 'action' . ucwords($action);
        if (is_callable([$controller_obj, $action])) {
            return $controller_obj->$action();
        } else {
            _notice('Notice:Render action fail :' . $_render, __FILE__, __LINE__);
        }
    }

}

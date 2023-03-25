<?php

namespace framework\system\manager;

use framework;
use framework\system\kernel\CFile;
use framework\system\kernel\Manager;

/**
 *
 * @version 1.0.0
 * @author vampire
 */
class ComponentManager extends Manager
{

    /**
     *
     * @var array $component_map 组件地图
     * [
     *      'system.web.webApplication' => [
     *          'Class' => 'cong\\web\\CWebApplication',
     *          'File' => FRAMEWORK . DS . 'web' . DS . 'CWebApplication' . EXT,
     *          'Cover' => 'system.application',
     *     ]
     * ]
     */
    protected $mapping_component = [];
    protected $kernel_is_loaded = false;
    protected $components = [];
    protected $components_alias = [];

    public function init()
    {
        $this->addMappingFile([
            DIR_FRAMEWORK . DS . 'config' . DS . 'mapping' . DS . 'component' . EXT,
        ]);
        $this->loadCoreComponent();
    }

    //映射处理函数
    public function processMapping($_mapping = null)
    {

        if (is_null($_mapping)) {
            return false;
        }

        foreach ($_mapping as $k => $v) {
            if (array_key_exists('Class', $v)) {
                $this->mapping_component[$k] = $v;
            }
        }

        return true;
    }

    /**
     *
     * @param type $component
     * @return string success
     * @return false faile
     */
    public function registerComponent($component)
    {
        if (is_array($component)) {

            $component_name = $component['Component'];
            if (!($this->isComponentName($component_name))) {
                $error = 'Component name ' . $component_name . 'is not start of com.';
            }
            if (!isset($component['Class'])) {
                $error = 'Component ' . $component_name . 'Class is not set';
            }

            /**
             * Component Register start;
             */
            $this->mapping_component[$component_name] = [];

            $this->mapping_component[$component_name]['Class'] = $component['Class'];

            if (isset($component['File'])) {
                $this->mapping_component[$component_name]['File'] = $component['File'];
            }
            if (isset($component['Cover'])) {
                $this->mapping_component[$component_name]['Cover'] = $component['Cover'];
            }
            if (isset($component['Alias'])) {
                $this->mapping_component[$component_name]['Alias'] = $component['Alias'];
            }
            return $component_name;
        }
        return false;
    }

    public function load($component_name, $argv = null)
    {
        if ($this->isComponentName($component_name)) {
            if ($this->cmpLoaded($component_name)) {
                return true;
            }
            if (!isset($this->mapping_component[$component_name])) {
                return false;
            }
            if (isset($this->mapping_component[$component_name]['File'])) {

                CFile::requireFile($this->mapping_component[$component_name]['File']);
            }
            /**
             * ['component'=>'test','class'=>'test','file'=>'test.php']
             */
            $class = $this->mapping_component[$component_name]['Class'];

            if (!isset($this->components[$component_name])) {
                try {
                    $this->components[$component_name] = new $class($argv);
                    /*
                     * 设置组建覆盖
                     */
                    if (isset($this->mapping_component[$component_name]['Cover'])) {
                        $this->setCover($component_name, $this->mapping_component[$component_name]['Cover']);
                    }
                    /*
                     * 设置组建别名
                     */
                    if (isset($this->mapping_component[$component_name]['Alias'])) {
                        $this->setAlias($component_name, $this->mapping_component[$component_name]['Alias']);
                    }
                } catch (Exception $ex) {
                    _notice('<br/>Notice:Component class [', $class, '] not found <br/>');

                    return false;
                }
            }
            return true;
        }

        if (is_array($component_name)) {
            foreach ($component_name as $value) {
                $this->load($value);
            }
        }
        return true;
    }

    /*
     * 覆盖
     */

    protected function setCover($component_name, $cover)
    {
        if (empty($cover)) {
            return false;
        }
        if (is_string($cover)) {
            if (isset($this->components[$cover])) {
                unset($this->components[$cover]);
            }
            $this->components[$cover] = $component_name;
        }
        if (is_array($cover) || is_object($cover)) {
            foreach ($cover as $_cover) {
                $this->setCover($component_name, $_cover);
            }
        }
    }

    protected function setAlias($component_name, $alias)
    {

        if (is_string($alias)) {
            if ($this->isComponentName($component_name) && $this->isComponentName($alias)) {
                $this->components_alias[$alias] = $component_name;
            }
        }
        if (is_array($alias)) {
            foreach ($alias as $v) {
                $this->setAlias($component_name, $v);
            }
        }
    }

    public function coreComponents()
    {
        $components = [];
        foreach ($this->mapping_component as $k => $v) {
            if (array_key_exists('Autoload', $v)) {
                $components[] = $k;
            }
        }
        return $components;
    }

    /**
     * 加载核心组件
     * @return boolean
     */
    public function loadCoreComponent()
    {

        framework::mm()->send('message.framework.ready', true);
        if ($this->kernel_is_loaded == true) {
            return true;
        }

        $coreComponents = $this->coreComponents();

        $this->load($coreComponents);

        $this->kernel_is_loaded = true;
        framework::mm()->send('message.framework.ready', true);
        return true;
    }

    public function &get($cmp_name = 'com.framework')
    {

        $cmp_name = $this->getCmpName($cmp_name);

        if ($this->cmpLoaded($cmp_name)) {

            return $this->components[$cmp_name];
        } else {

            $this->load($cmp_name);
            return $this->components[$cmp_name];
        }
    }

    public function getCmpName($cmp_name = null)
    {

        if (array_key_exists($cmp_name, $this->components_alias)) {
            $cmp_name = $this->components_alias[$cmp_name];
            return $cmp_name;
        }
        if (array_key_exists($cmp_name, $this->mapping_component)) {
            return $cmp_name;
        } else {
            foreach ($this->mapping_component as $component_index => $component_config) {
                if (isset($component_config['Alias']) && is_array($component_config['Alias'])) {
                    if (in_array($cmp_name, $component_config['Alias'])) {
                        $cmp_name = $component_index;
                        return $cmp_name;
                    }
                }
            }
        }
        return null;
    }

    public function cmpExist($cmp_name)
    {
        $cmp_name = $this->getCmpName($cmp_name);
        if (!isset($this->components[$cmp_name])) {

        }
    }

    public function cmpLoaded($cmp_name)
    {

        $cmp_name = $this->getCmpName($cmp_name);

        if (!isset($this->components[$cmp_name])) {
            return false;
        }
        if (is_object($this->components[$cmp_name])) {
            return true;
        }
        return false;
    }

}

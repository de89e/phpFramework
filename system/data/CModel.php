<?php

namespace framework\system\data;

/**
 * Description of CModle
 *
 * @author Administrator
 */
class CModel extends CObject
{

    protected $_data = [];

    /**
     * @param string $model
     * @param string $alias
     */
    public function loadModel($model = null, $alias = null)
    {
        $model = str_replace('\\', '/', $model);
        $path = explode('/', $model);
        $name = array_pop($path);
        $path = implode('/', $path);
        $modelClassName = ucwords(preg_replace('#[^a-zA-Z0-9]#', '', $name)) . 'Model';
        $fileName = ucfirst($name);

        $file = DIR_FRAMEWORK . DS . 'model' . DS . $path . DS . $fileName . EXT;
        if (!CFile::fileExits($file)) {
            $file = DIR_APPLICATION . DS . 'model' . DS . $path . DS . $fileName . EXT;
        }

        if (CFile::requireFile($file)) {
            try {
                $model_obj = new $modelClassName;
            } catch (Exception $ex) {
                _die('Class ' . $modelClassName . ' not found in ' . $file);
            }

            /** 2019-07-20
             * if (class_exists($modelClassName)) {
             * $model_obj = new $modelClassName;
             * } else {
             * _die('Class ' . $modelClassName . ' not found in ' . $file);
             * }
             *  */
        } else {
            _die('Model file ' . $file . ' not found!');
        }

        if ($alias) {
            $alias = preg_replace('#[^a-zA-Z0-9]#', '_', $alias);
            $modelName = $alias;
        } else {
            $modelName = preg_replace('#[^a-zA-Z0-9]#', '_', $model);
        }

        $this->$modelName = $model_obj;
    }

    /**
     * @param string $lang lang file
     * @return array lang
     */
    public function loadLang($lang = null)
    {
        $file = DIR_APPLICATION . DS . 'lang' . DS . $lang . EXT;
        return CFile::requireFile($file, true);
    }

    public function __set($name, $value)
    {

        $this->_data[$name] = $value;

    }

    public function __get($name)
    {

        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        } else {
            return null;
        }
    }

}

<?php

/*
 * PSR-4自动加载器
 * \一级命名空间\二级命名空间\类名
 * \一级命名空间\二级命名空间 映射 文件目录 
 * \类名 映射 文件名
 * PSR-4自动加载器 未发现加载文件后 启动PSR-0加载
 */

class psr4AutoLoader {

    protected static $mappingFile = [];
    protected static $class2Dir = [];
    protected static $class2File = [];

    public static function classLoader($className) {




        //系统定义映射文件。
        $mappingFile = [
            DIR_FRAMEWORK . DS . 'config' . DS . 'mapping' . DS . 'autoload' . EXT,
            DIR_APPLICATION . DS . 'config' . DS . 'mapping' . DS . 'autoload' . EXT
        ];
        static::addMappingFile($mappingFile);
        $classInfo = explode('\\', $className);
        $classInfo = array_filter($classInfo);
        ksort($classInfo);
        $count = count($classInfo);
        if ($count <= 1) {
            $searchDirBy = $classInfo[0];
            $searchFileBy = $classInfo[0];
        } else {
            $searchDirBy = '';
            for ($i = 0; $i < $count - 1; $i++) {
                $searchDirBy .= $classInfo[$i] . '\\';
            }

            $searchDirBy = rtrim($searchDirBy, '\\');
            $searchFileBy = $classInfo[$count - 1];
        }
        if (empty(static::$class2Dir) || empty(static::$class2File)) {
            _die('系统类映射文件或映射信息丢失！');
        }

        //查找文件名映射
        if (array_key_exists($searchFileBy, static::$class2File)) {

            $findedFile = static::$class2File[$searchFileBy];
        } else {
            $findedFile = $searchFileBy;
        }
        //查找目录映射
        if (array_key_exists($searchDirBy, static::$class2Dir)) {

            $findedDir = static::$class2Dir[$searchDirBy];
        } else {

            $findedDir = DIR_FRAMEWORK;
        }



        $classFile = $findedDir . DS . $findedFile . EXT;
        //载入类文件
        if (!(static::requireFile($classFile))) {

            static::psr0Loader($className);
        }
    }

    public static function psr0Loader($className) {
        //echo $className . '<br/>';
        $info = explode('\\', $className);
        $path = '';
        $info = array_filter($info);
        foreach ($info as $value) {
            if ('framework' == $value) {
                $path = DIR_FRAMEWORK;
            } elseif ('application' == $value) {
                $path = DIR_APPLICATION;
            } else {
                $path = $path . DS . $value;
            }
        }
        $classFile = $path . EXT;

        if (!(static::requireFile($classFile))) {
            return FALSE;
        }
    }

    public static function addClass2Dir($class = 'framework', $dir = DIR_FRAMEWORK) {
        if (is_dir($dir)) {
            $dir = realpath($dir);
            if (!array_key_exists($class, self::$class2Dir)) {

                static::$class2Dir[$class] = $dir;
            }
        }
    }

    public static function addClass2File($class = 'framework', $file = 'framework') {
        if (!array_key_exists($class, self::$class2File)) {
            static::$class2File[$class] = $file;
        }
    }

    //映射处理函数
    protected static function processMapping($_mapping = NULL) {
        if (is_null($_mapping)) {
            return FALSE;
        }
        if (isset($_mapping['class2Dir']) && is_array($_mapping['class2Dir'])) {
            for ($i = 0; $i < count($_mapping['class2Dir']); $i++) {
                $namespace = $_mapping['class2Dir'][$i][0];
                $dir = realpath($_mapping['class2Dir'][$i][1]);
                static::addClass2Dir($namespace, $dir);
            }
        }
        if (isset($_mapping['class2File']) && is_array($_mapping['class2File'])) {
            for ($i = 0; $i < count($_mapping['class2File']); $i++) {
                static::addClass2File($_mapping['class2File'][$i][0], $_mapping['class2File'][$i][1]);
            }
        }
        return TRUE;
    }

    protected static function loadMapping($force = FALSE) {

        foreach (static::$mappingFile as $file => $loaded) {
            if ($loaded == 1 && !$force) {
                continue;
            }
            $_mapping = self::requireFile($file, TRUE);
            if (static::processMapping($_mapping)) {
                static::$mappingFile[$file] = 1;
            }
            //var_dump($_mapping);
        }
    }

    public static function addMappingFile($file) {

        if (is_array($file)) {
            foreach ($file as $k => $v) {
                static::addMappingFile($v);
            }
        }

        if (is_string($file) && file_exists($file)) {
            $file = realpath($file);
            if (array_key_exists($file, self::$mappingFile)) {
                return TRUE;
            } else {
                static::$mappingFile[$file] = -1;
                static::loadMapping();
                return TRUE;
            }
        }
        return FALSE;
    }

    protected static function requireFile($file = NULL, $_Return = FALSE) {

        if (is_array($file)) {
            foreach ($file as $key => $value) {
                $fileInArray = $value;
                static::requireFile($fileInArray, $_Return);
            }
        }
        if (!is_string($file)) {
            return FALSE;
        }
        $file = str_replace('\\', DS, $file);
        if (file_exists($file)) {
            if ($_Return) {
                return require_once $file;
            } else {
                require_once $file;
                return TRUE;
            }
        }
        return FALSE;
    }

}

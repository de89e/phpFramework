<?php

namespace framework\system\data;

class CFile extends CModel
{

    public static $error = null;

    public static function requireFile($file = null, $_Return = false)
    {

        if (is_array($file)) {
            foreach ($file as $key => $value) {
                $fileInArray = $value;
                self::requireFile($fileInArray, $_Return);
            }
        }
        if (!is_string($file)) {
            return false;
        }
        $file = str_replace("\\", DS, $file);
        $file = str_replace("//", DS, $file);

        try {
            if ($_Return) {
                return require_once $file;
            } else {
                if (self::fileExits($file)) {
                    require_once $file;
                    return true;
                } else {
                    return false;
                }

            }
        } catch (Exception $ex) {
            if (_DEBUG) {
                _notice('CFile Require -- The file ' . $file . " not found!");
            }
        } /*
        if (file_exists($file)) {

        if ($_Return) {
        return require_once $file;
        } else {
        require_once $file;
        return TRUE;
        }
        } else {
        if (_DEBUG) {
        _notice('CFile Require -- The file ' . $file . " not found!");
        }
        }
         *
         */
        return false;
    }

    public static function fileExits($file)
    {
        $file = str_replace("\\", DS, $file);
        $file = str_replace("//", DS, $file);
        if (file_exists($file) && is_file($file)) {
            return true;
        }
        return false;
    }

    public static function dirExits($dir)
    {
        $dir = str_replace("\\", DS, $dir);
        $dir = str_replace("//", DS, $dir);
        if (file_exists($dir) && is_dir($dir)) {
            return true;
        }
        return false;
    }

    public static function fileInclude($file, $once = true)
    {
        $file = str_replace("\\", DS, $file);
        $file = str_replace("//", DS, $file);
        if ($once) {
            return include_once $file;
        }
        return include $file;
    }

    public static function fileWrite($path, $content)
    {

    }

    public static function fileGetContent($file, $maxlen = 0, $offset = -1)
    {
        $file = str_replace("\\", DS, $file);
        $file = str_replace("//", DS, $file);
        return self::getFileContents($file, $maxlen = 0, $offset = -1);
    }

    public static function getFileContents($file, $maxlen = 0, $offset = -1)
    {
        $file = str_replace("\\", DS, $file);
        $file = str_replace("//", DS, $file);
        $contents = null;
        if (file_exists($filename)) {

            if ((filesize($file) <= $maxlen) || $maxlen == 0) {
                return $contents = file_get_contents($file);
            } else {
                return $contents = file_get_contents($file, $use_include_path = false, $context = null, $offset, $maxlen);
            }
        } else {
            return false;
        }
    }

    public static function checkFileName($path)
    {
        if (preg_match('/[\/\|\?\*\:\<\>\'\"\\\]+/', $path)) {
            return false;
        }
        return true;
    }

    public static function checkPathName($path)
    {
        if (preg_match('/[\|\?\*\:\<\>\'\"]+/', $path)) {
            return false;
        }
        return true;
    }

    /**
     * 建立目录树
     * @param string $path
     * @param integer $mode
     * @return bool
     */
    public static function mkdir($path = null, $mode = 0755)
    {

        if (!$path || !static::checkPathName($path)) {
            return false;
        }
        if (is_dir($path)) {
            return false;
        }
        $path = preg_replace('/\\\+/', '/', $path);
        $path_array = explode('/', $path);
        $_path_array = array();
        for ($i = 0; $i <= count($path_array); $i++) {
            if (!empty($path_array[$i])) {
                $_path_array[] = $path_array[$i];
            }
        }
        $_now = "";
        for ($i = 0;!empty($_path_array[$i]); $i++) {
            $_now .= $_path_array[$i] . DS;
            if (is_dir($_now)) {
                continue;
            } else {
                mkdir($_now, $mode);
            }
        }
    }

    public static function readFile($filename, $maxlen = 0)
    {
        $contents = null;
        if (file_exists($filename) && is_readable($filename) && ($fd = @fopen($filename, 'rb'))) {

            while (!feof($fd)) {
                if ($maxlen <= 8192) {
                    $contents .= fread($fd, $maxlen);
                    break;
                } else {
                    $contents .= fread($fd, 8192);
                    $maxlen = $maxlen - 8192;
                }
                if (ftell($fd) >= $maxlen) {
                    break;
                }
            }
            fclose($fd);
            return $contents;
        } else {
            return false;
        }
    }

    public static function deleteFile($files = null, $opt = null)
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                self::deleteFile($file, $opt);
            }
        } elseif (is_string($files)) {
            if (self::rm($files, $opt)) {
                return true;
            }
            return false;
        }
    }

    public static function rm($file, $opt = null)
    {

        if (!empty($opt)) {
            $opt = explode('-', $opt);
        } else {
            $opt = array();
        }
        if (!file_exists($file)) {
            self::$error = "删除失败,文件不存在";
            return false;
        }
        /**
         * 是否为目录外目录。
         */
        if (self::checkDeleteAccess($file)) {

            return false;
        }
        if (is_file($file)) {
            if (!is_writable($file) && in_array('f', $opt)) {
                chmod($file, 0777);
                @unlink($file);
            } elseif (is_writable($file)) {
                @unlink($file);
            }
            self::$error = "删除失败,只读文件。";
            return false;
        }
        if (is_dir($file)) {
            $handle = opendir($file);
            $_empty = true;
            while (false !== ($item = readdir($handle))) {
                if ($item != "." && $item != "..") {
                    if (!is_dir("$file/$item") && !in_array('r', $opt)) {

                        self::rm("$file/$item", $opt);
                    } else {

                        $_empty = false;
                    }
                }
            }
            closedir($handle);
            if ($_empty) {
                rmdir($file);
                return true;
            } else {
                self::$error = "有二级目录，已删除目录下文件。删除目录树请使用 -r 参数";
                return false;
            }
        }
    }

    public static function chmod($file, $umask)
    {

        chmod($file, $umask);
    }

}

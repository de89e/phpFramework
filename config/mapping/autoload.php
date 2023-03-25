<?php

/*
 * 框架类映射信息
 */
//初始化
$_mapping = [];

//框架定义的目录映射信息
$_mapping['class2Dir'] = [
    ['framework', DIR_FRAMEWORK],
    ['baseController', DIR_FRAMEWORK . DS . 'system' . DS . 'base'],
    ['baseModel', DIR_FRAMEWORK . DS . 'system' . DS . 'base'],
    ['baseStoreModel', DIR_FRAMEWORK . DS . 'system' . DS . 'base'],
    ['framework\system\kernel', DIR_FRAMEWORK . DS . 'system' . DS . 'kernel']
];
//框架定义的文件映射信息 默认可以不用填写
$_mapping['class2File'] = [
    ['framework', 'framework']
];

return $_mapping;

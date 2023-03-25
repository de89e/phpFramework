<?php
/*
 * 组件映射信息
 */
return [
    'com.manager.config' => [
        'Class' => 'framework\\system\\manager\\ConfigManager',
        'Autoload' => 1,
        'Alias' => [
            'com.config',
        ],
    ],
    'com.request' => [
        'Class' => 'framework\\system\\kernel\\Request',
        'Autoload' => 1,
    ],
    'com.route' => [
        'Class' => 'framework\\system\\kernel\\Route',
        'Autoload' => 1,
    ],
    'com.application' => [
        'Class' => 'framework\\system\\kernel\\Application',
        'Autoload' => 1,
    ],
    'com.response' => [
        'Class' => 'framework\\system\\kernel\\Response',
        'Autoload' => 1,
    ],
    'com.manager.session' => [
        'Class' => 'framework\\system\\manager\\SessionManager',
        'Alias' => [
            'com.session',
        ],
        //'Autoload' => 1,
    ],
    'com.database' => [
        'Class' => 'framework\\system\\database\\Database',
    ],
];

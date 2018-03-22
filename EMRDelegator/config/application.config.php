<?php
/**
 * Slice specific configuration values.
 * @copyright Copyright (c) 2012 WebPT, INC
 */
return array(
    'modules' => array(
        'NewRelic',
        'Application',
        'Service',
        'Console',
        'WebPT\EMR\Csrf',
        'SimpleLogger',
        'XssModule'
    ),
    'module_listener_options' => array(
        // Using __DIR__ to ensure cross-platform compatibility. Some platforms --
        // e.g., IBM i -- have problems with globs that are not qualified.
        'config_glob_paths' => array(
            realpath(__DIR__) . '/autoload/{,*.}{global}.php',
            realpath(__DIR__) . '/autoload/{,*.}{local}.php',
        ),
        'module_paths' => array(
            './module',
            './library/EMRCore/Zend/module',
            './vendor/webpt'
        ),
    ),
);

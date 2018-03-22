<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2012 WebPT, INC
 */
date_default_timezone_set('UTC');

error_reporting(E_ALL | E_STRICT);

define('APPLICATION_PATH', dirname(__DIR__));

chdir(APPLICATION_PATH);

require __DIR__ . "/../vendor/autoload.php";

Zend\Mvc\Application::init(include 'config/application.config.php')->run();
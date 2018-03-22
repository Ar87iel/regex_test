<?php
/**
 * @copyright Copyright (c) 2012 WebPT, INC
 */
date_default_timezone_set('UTC');

error_reporting(E_ALL | E_STRICT);

define('APPLICATION_PATH', realpath(__DIR__ . '/../../../'));

chdir(APPLICATION_PATH);

require __DIR__ . "/../../../vendor/autoload.php";

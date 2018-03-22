<?php
/**
 * Test Bootstrapper
 *
 * @copyright Copyright (c) 2012 WebPT, INC
 */
date_default_timezone_set('UTC');

error_reporting(E_ALL | E_STRICT);

defined('RELATIVE_APPLICATION_PATH') || define('RELATIVE_APPLICATION_PATH', __DIR__ . '/../../../../../');

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(RELATIVE_APPLICATION_PATH));

chdir(APPLICATION_PATH);

require __DIR__ . "/../../../../../vendor/autoload.php";

/* PHPUnit Hack when running under process isolation */
$GLOBALS['_SERVER']['_'] = '';
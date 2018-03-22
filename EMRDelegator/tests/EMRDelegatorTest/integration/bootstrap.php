<?php
/**
 * @copyright Copyright (c) 2012 WebPT, INC
 */
use EMRCore\Config\Application as ApplicationConfig;
use Zend\Config\Config as ZendConfig;
use Zend\Config\Processor\Token;

date_default_timezone_set('UTC');

error_reporting(E_ALL | E_STRICT);

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(__DIR__ . '/../../../'));

chdir(APPLICATION_PATH);

require __DIR__ . "/../../../vendor/autoload.php";

$orderedConfigFiles = array(
    APPLICATION_PATH . '/vendor/webpt/emr-core/src/EMRCore/Config/config/global.php',
    APPLICATION_PATH . '/config/autoload/global.php',
    __DIR__ . '/config/global.php',
    __DIR__ . '/config/local.php',
);

$config = new ZendConfig(array(), true);

foreach ($orderedConfigFiles as $configFile)
{
    $config->merge(new ZendConfig(require $configFile));
}

$processor = new Token($config->get('tokens'));
$processor->process($config);

ApplicationConfig::getInstance()->setConfiguration($config->toArray(), false);

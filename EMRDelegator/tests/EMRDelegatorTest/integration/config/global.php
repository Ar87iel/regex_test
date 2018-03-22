<?php

use EMRCore\Config\Controller\Validator\Config as ControllerValidatorConfig;
use EMRCore\Config\ServiceManager\Config as ServiceManagerConfig;
use EMRCore\Zend\ServiceManager\Initializer\Cache as CacheInitializer;
use EMRCore\Zend\ServiceManager\Initializer\Session as SessionInitializer;
use EMRCore\Zend\ServiceManager\Initializer\SqlConnector as SqlConnectorInitializer;
use EMRCore\Config\Logger\Config as LoggerConfig;

$sqlAdapter = 'EMRCore\SqlConnector\Mysql\Adapter';
$sqlDriver = 'pdo_mysql';

$sqlReader = array(
    'driver' => $sqlDriver,
    'host' => 'localhost',
    'port' => '3306',
    'username' => 'root',
    'password' => '',
    'database' => 'test',
);

$sqlWriter = array(
    'driver' => $sqlDriver,
    'host' => 'localhost',
    'port' => '3306',
    'username' => 'root',
    'password' => '',
    'database' => 'test',
);

$doctrineAdapter = 'EMRCore\DoctrineConnector\Adapter\Adapter';
$doctrineDriver = 'pdo_mysql';
$doctrineModelDirectory = __DIR__ . '/../../../../config/doctrine/';
$doctrineProxyDirectory = '/../../../../../data/model/proxies/';

$doctrineReader = array(
    'driver' => $doctrineDriver,
    'host' => 'localhost',
    'port'	   => '3306',
    'user' => 'root',
    'password' => '',
    'dbname'   => 'test',
);

$doctrineWriter = array(
    'driver' => $doctrineDriver,
    'host' => 'localhost',
    'port'	   => '3306',
    'user' => 'root',
    'password' => '',
    'dbname'   => 'test',
);

return array(

    // Cache initializer uses a cache registry name to instantiate the dependency.
    CacheInitializer::CONFIG_KEY => array(
        'EMRCore\Cache\ApplicationDiInterface' => 'main_application_data_pool',
    ),

    // Session initializer uses a session registry name to instantiate the dependency.
    SessionInitializer::CONFIG_KEY => array(
        'EMRDelegator\Session\AuthorizationDiInterface' => 'authorization',
        'EMRDelegator\Session\RequireAuthorizationSessionDiInterface' => 'authorization',
    ),

    ControllerValidatorConfig::CONFIG_KEY => array(
        'EMRCore\Zend\Grant\IpGrantDiInterface' => array(
            'concrete' => 'EMRCore\Zend\Grant\Ip',
            'invokable' => array(
                'method' => 'verify',
            ),
        ),
        'EMRAuth\Session\RequireAuthorizationSessionDiInterface' => array(
            'factory' => array(
                'class' => 'EMRCore\Session\SessionFactory',
                'method' => 'get',
                'params' => array( 'authorization' ),
            ),
            'invokable' => array(
                'method' => 'isAuthorized',
            ),
        ),
    ),

    // SqlConnector initializer uses driver parameters to instantiate the (adapter) dependency.
    SqlConnectorInitializer::CONFIG_KEY => array(
        'EMRCore\SqlConnector\DefaultReaderDiInterface' => array(
            'adapter' => $sqlAdapter,
            'params' => array(
                'driver' => $sqlReader['driver'],
                'host' => 'DB_READER_HOST',
                'port' => 'DB_READER_PORT',
                'username' => 'DB_READER_USERNAME',
                'password' => 'DB_READER_PASSWORD',
                'database' => 'DB_READER_SCHEMA',
            ),
        ),
        'EMRCore\SqlConnector\DefaultReaderWriterDiInterface' => array(
            'adapter' => $sqlAdapter,
            'params' => array(
                'driver' => $sqlWriter['driver'],
                'host' => 'DB_WRITER_HOST',
                'port' => 'DB_WRITER_PORT',
                'username' => 'DB_WRITER_USERNAME',
                'password' => 'DB_WRITER_PASSWORD',
                'database' => 'DB_WRITER_SCHEMA',
            ),
        ),
    ),

    ServiceManagerConfig::CONFIG_KEY => array(
        'initializers' => array(
            'CacheInjection' => 'EMRCore\Zend\ServiceManager\Initializer\Cache',
            'SessionInjection' => 'EMRCore\Zend\ServiceManager\Initializer\Session',
            'SqlInjection' => 'EMRCore\Zend\ServiceManager\Initializer\SqlConnector',
            'InterfaceInjection' => 'EMRCore\Zend\ServiceManager\Initializer\InterfaceInjection',
        ),
        'abstract_factories' => array(
            'EMRCore\Zend\ServiceManager\AbstractFactories\PrototypeFactory',
        ),
    ),

    ControllerValidatorConfig::CONFIG_KEY => array(
        'EMRCore\Session\RequireApplicationSessionDiInterface' => array(
            'factory' => array(
                'class' => 'EMRCore\Session\SessionFactory',
                'method' => 'get',
                'params' => array( 'application' ),
            ),
            'invokable' => array(
                'method' => 'isAuthorized',
            ),
        ),
    ),
    'factories' => array(
        'EMRCore\DoctrineConnector\DoctrineConnectorFactory' => array(
            'registry' => array(
                'default_master_slave' => array(
                    'adapter' => $doctrineAdapter,
                    'params' => array(
                        'wrapperClass' => 'Doctrine\DBAL\Connections\MasterSlaveConnection',
                        'driver' => $doctrineDriver,
                        'master' => array(
                            'host' => 'DB_WRITER_HOST',
                            'port' => 'DB_WRITER_PORT',
                            'user' => 'DB_WRITER_USERNAME',
                            'password' => 'DB_WRITER_PASSWORD',
                            'dbname' => 'DB_WRITER_SCHEMA',
                        ),
                        'slaves' => array(
                            'a' => array(
                                'host' => 'DB_READER_HOST',
                                'port' => 'DB_READER_PORT',
                                'user' => 'DB_READER_USERNAME',
                                'password' => 'DB_READER_PASSWORD',
                                'dbname' => 'DB_READER_SCHEMA',
                            ),
                        ),
                        'model_dir' => $doctrineModelDirectory,
                        'proxy_dir' => $doctrineProxyDirectory,
                    ),
                ),
            ),
        ),
        'EMRCore\Cache\CacheFactory' => array(
            'registry' => array(

                /* The pool in which application data goes into (e.g., DAO caches, unit results, etc) */
                'main_application_data_pool' => array(
                    'adapter' => 'EMRCore\Cache\Memcache\Adapter',
                    'params' => array(
                        'lock_ttl' => 30,
                        'servers' => array(
                            '127.0.0.1:11211',
                        ),
                    ),
                ),
                /*
                * session_application can be different between slice clusters. A cluster is basically a collection of slices
                * that operate under a specific domain name. In general, if a slice is on a domain that does not have access
                * to the application session cache cookie set under a different domain, the slice will need to override to a
                * different memcache pool.
                */
                'main_application_session_data_pool' => array(
                    'adapter' => 'EMRCore\Cache\Memcache\Adapter',
                    'params' => array(
                        'lock_ttl' => 30,
                        'servers' => array(
                            '127.0.0.1:11211',
                        ),
                    ),
                ),
            ),
        ),
        'EMRCore\Session\SessionFactory' => array(
            'registry' => array(
                'application' => array(
                    'adapter' => 'EMRCore\Session\Memcache\Adapter',
                    'params' => array(
                        'cookie_key' => 'APP_SESS_ID',
                        'session_timeout' => '2 hours',
                        'session_dto' => 'EMRCore\Session\Dto\ApplicationDefault',
                        'allowed_applications' => array(),
                        'memcache_pool' => 'main_application_session_data_pool',
                    ),
                ),
            ),
        ),
    ),

    LoggerConfig::CONFIG_KEY => array(
        'rootLogger' => array(
            'level' => 'ERROR',
            'appenders' => array( 'default' ),
        ),
        'defaultRenderer' => 'EMRCore\Logger\Renderer\DefaultObjectRenderer',
        'renderers' => array(
            'Exception' => array(
                'renderedClass' => 'Exception',
                'renderingClass' => 'EMRCore\Logger\Renderer\Exception',
            ),
            'Zend\Mvc\MvcEvent' => array(
                'renderedClass' => 'Zend\Mvc\MvcEvent',
                'renderingClass' => 'EMRCore\Logger\Renderer\MvcEvent',
            ),
            'Zend\Http\Client' => array(
                'renderedClass' => 'Zend\Http\Client',
                'renderingClass' => 'EMRCore\Logger\Renderer\EMRCore\Zend\Http\Client',
            ),
            'Zend\Mvc\Router\Http\RouteMatch' => array(
                'renderedClass' => 'Zend\Mvc\Router\Http\RouteMatch',
                'renderingClass' => 'EMRCore\Logger\Renderer\Zend\Mvc\Router\Http\RouteMatch',
            ),
        ),
        'appenders' => array(
            'default' => array(
                'class' => 'LoggerAppenderDailyFile',
                'layout' => array(
                    'class' => 'LoggerLayoutPattern',
                    'params' => array(
                        'conversionPattern' => implode( ' ', array(
                            '%date{Y-m-d H:i:s.u}',
                            'T%X{ConversationId}',
                            'P%X{ParentConversationId}',
                            'User%X{UserId}',
                            '%logger',
                            '%level',
                            '%message',
                            '%newline',
                        ) ),
                    ),
                ),
                'params' => array(
                    'file' => '/webpt/log/' . basename(APPLICATION_PATH) . '.log',
                    'append' => true,
                    'datePattern' => 'Ymd',
                ),
            ),
        ),
    ),
);

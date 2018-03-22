<?php

use EMRCore\Service\Auth\Application\Application;
use EMRCore\Config\Logger\Config as LoggerConfig;
use EMRCore\Config\DependencyInjection\Config as DiConfig;
use EMRCore\Config\Deploy\Config as DeployConfig;
use EMRCore\Config\Controller\Validator\Config as ControllerValidatorConfig;
use Psr\Log\LogLevel;

return array(

    'tokens' => array(

        'DB_WRITER_SCHEMA' => 'delegator_webpt_com',

        'DB_READER_SCHEMA' => 'delegator_webpt_com',

        // Not multi or single-tenant.
        'DOCTRINE_CONNECTION_WRAPPER' => 'Doctrine\DBAL\Connections\MasterSlaveConnection',

        'DOCTRINE_MODEL_DIR' => __DIR__ . '/../doctrine/',

        'HOST_INTERNAL_APP' => 'phxapp1.webpt.com',

        // This directory must be writable by the web server. Relative path from \EMRCore\DoctrineConnector\Adapter\Adapter directory.
        'DOCTRINE_PROXY_DIR' => '/../../../../../data/model/proxies/',
    ),

    DeployConfig::CONFIG_KEY => array(
        'database' => array(
            'version_model' => 'EMRDelegator\Model\Version',
        ),
    ),

    'logout-urls' => array(
        'app'               => 'http://HOST_INTERNAL_APP/user/sso/logout/?companyId=9999999999',
        'interceptor'       => 'http://HOST_INTERNAL_APP/s/interceptor/service/logout.json?companyId=9999999999',
    ),

    'factories' => array(

        'EMRCore\Session\SessionFactory' => array(
            'registry' => array(

                'authorization' => array(
                    'params' => array(
                        'cookie_key' => 'DELEGATOR_AUTHORIZATION_SESS_ID_v1_073',
                        'allowed_applications' => array(
                            Application::APPLICATION_PT_EMR => Application::APPLICATION_PT_EMR,
                            Application::APPLICATION_ADMIN => Application::APPLICATION_ADMIN,
                        ),
                    ),
                ),

                'application' => array(
                    'params' => array(
                        'allowed_applications' => array(
                            Application::APPLICATION_PT_EMR => Application::APPLICATION_PT_EMR,
                            Application::APPLICATION_ADMIN => Application::APPLICATION_ADMIN,
                        ),
                    ),
                ),
            ),
        ),
    ),

    'evictionEnabled' => true,

    ControllerValidatorConfig::CONFIG_KEY => array(
        'EMRCore\Session\Instance\RequireAuthorizationSessionDiInterface' => array(
            'factory' => array(
                'class' => 'EMRCore\Session\SessionFactory',
                'method' => 'get',
                'params' => array(
                    'instance' => 'authorization',
                ),
            ),
            'invokable' => array(
                'method' => 'isAuthorized',
            ),
        ),
    ),

    'SimpleLogger' => array(
        'Handler' => array(
            'StreamHandler' => array(
                'options' => array(
                    'stream' => '/webpt/log/EMRDelegator.log',
                    'level' => LogLevel::ERROR,
                ),
            ),
        ),
    ),
);
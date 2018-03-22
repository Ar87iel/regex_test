<?php
use EMRCore\Zend\ServiceManager\Initializer\SqlConnector as SqlConnectorInitializer;

return array(

    'tokens' => array(

        // Multi-tenant writer connection.
        'DB_MULTI_TENANT_WRITER_HOST' => 'localhost',
        'DB_MULTI_TENANT_WRITER_PORT' => '3306',
        'DB_MULTI_TENANT_WRITER_USERNAME' => 'root',
        'DB_MULTI_TENANT_WRITER_PASSWORD' => '',
        'DB_MULTI_TENANT_WRITER_SCHEMA' => 'app_webpt_com',

        // Auth writer connection.
        'DB_AUTH_WRITER_HOST' => 'localhost',
        'DB_AUTH_WRITER_PORT' => '3306',
        'DB_AUTH_WRITER_USERNAME' => 'root',
        'DB_AUTH_WRITER_PASSWORD' => '',
        'DB_AUTH_WRITER_SCHEMA' => 'auth_webpt_com',
    ),

    // SqlConnector initializer uses driver parameters to instantiate the (adapter) dependency.
    SqlConnectorInitializer::CONFIG_KEY => array(

        // writer for legacy database
        'Console\Etl\SqlConnector\Legacy\ReaderWriterDiInterface' => array(
            'adapter' => 'SQL_ADAPTER',
            'params' => array(
                'driver' => 'DB_DRIVER',
                'host' => 'DB_MULTI_TENANT_WRITER_HOST',
                'port' => 'DB_MULTI_TENANT_WRITER_PORT',
                'username' => 'DB_MULTI_TENANT_WRITER_USERNAME',
                'password' => 'DB_MULTI_TENANT_WRITER_PASSWORD',
                'database' => 'DB_MULTI_TENANT_WRITER_SCHEMA',
            ),
        ),

        // writer for legacy database
        'Console\Etl\SqlConnector\Sso\ReaderWriterDiInterface' => array(
            'adapter' => 'SQL_ADAPTER',
            'params' => array(
                'driver' => 'DB_DRIVER',
                'host' => 'DB_AUTH_WRITER_HOST',
                'port' => 'DB_AUTH_WRITER_PORT',
                'username' => 'DB_AUTH_WRITER_USERNAME',
                'password' => 'DB_AUTH_WRITER_PASSWORD',
                'database' => 'DB_AUTH_WRITER_SCHEMA',
            ),
        ),

        // writer for sso database
        'Console\Etl\SqlConnector\Delegator\ReaderWriterDiInterface' => array(
            'adapter' => 'SQL_ADAPTER',
            'params' => array(
                'driver' => 'DB_DRIVER',
                'host' => 'DB_WRITER_HOST',
                'port' => 'DB_WRITER_PORT',
                'username' => 'DB_WRITER_USERNAME',
                'password' => 'DB_WRITER_PASSWORD',
                'database' => 'DB_WRITER_SCHEMA',
            ),
        ),
    ),
);

<?php

use EMRCore\Config\Controller\Config as ControllerConfig;

return array(

    ControllerConfig::CONFIG_KEY => array(
        'invokables' => array(
            'Console\Etl\Controller\CompaniesImport' => 'Console\Etl\Controller\CompaniesImportController',
            'Console\Etl\Controller\FacilitiesImport' => 'Console\Etl\Controller\FacilitiesImportController',
            'Console\Etl\Controller\UserHasFacilityImport' => 'Console\Etl\Controller\UserHasFacilityImportController',
            'Console\Etl\Controller\AgreementsImport' => 'Console\Etl\Controller\AgreementsImportController',
            'Console\Controller\System' => 'Console\Controller\SystemController',
            'Console\Controller\DatabaseManager' => 'Console\Controller\DatabaseManagerController',
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'etl-companies-import-route' => array(
                    'options' => array(
                        'route' => 'etl companiesimport',
                        'defaults' => array(
                            'controller' => 'Console\Etl\Controller\CompaniesImport',
                            'action' => 'execute'
                        ),
                    ),
                ),
                'etl-facilities-import-route' => array(
                    'options' => array(
                        // add [ and ] if optional ( ex : [<doname>] )
                        'route' => 'etl facilitiesimport',// [--verbose|-v] <source_db_reader_diInterface> <dest_db_writer_diInterface>',
                        'defaults' => array(
                            'controller' => 'Console\Etl\Controller\FacilitiesImport',
                            'action' => 'execute'
                        ),
                    ),
                ),
                'etl-user-has-facilities-import-route' => array(
                    'options' => array(
                        // add [ and ] if optional ( ex : [<doname>] )
                        'route' => 'etl userhasfacilityimport',// [--verbose|-v] <source_db_reader_diInterface> <dest_db_writer_diInterface>',
                        'defaults' => array(
                            'controller' => 'Console\Etl\Controller\UserHasFacilityImport',
                            'action' => 'execute'
                        ),
                    ),
                ),
                'etl-agreements-import-route' => array(
                    'options' => array(
                        // add [ and ] if optional ( ex : [<doname>] )
                        'route' => 'etl agreements',
                        'defaults' => array(
                            'controller' => 'Console\Etl\Controller\AgreementsImport',
                            'action' => 'execute'
                        ),
                    ),
                ),
            ),
        ),
    ),
);
<?php
use EMRCore\Config\Controller\Config as ControllerConfig;
/**
 * Service module configuration.
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2012 WebPT, INC
 */
return array(
    ControllerConfig::CONFIG_KEY => array(
        'invokables' => array(
            'Service\Controller\Index' => 'Service\Controller\IndexController',
            'Service\Controller\Cluster' => 'Service\Controller\ClusterController',
            'Service\Controller\Company' => 'Service\Controller\CompanyController',
            'Service\Controller\Facility' => 'Service\Controller\FacilityController',
            'Service\Controller\UserHasFacility' => 'Service\Controller\UserHasFacilityController',
            'Service\Controller\Logout' => 'Service\Controller\LogoutController',
            'Service\Controller\FacilityWithCompany' => 'Service\Controller\FacilityWithCompanyController',
            'Service\Controller\Agreement' => 'Service\Controller\AgreementController',
            'Service\Controller\UserHasAgreement' => 'Service\Controller\UserHasAgreementController',
            'Service\Controller\SessionRegistry' => 'Service\Controller\SessionRegistryController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'companySearch' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/service/company/search.json',
                    'defaults' => array(
                        'controller' => 'Service\Controller\Company',
                        'action' => 'search',
                        'format' => 'json'
                    ),
                ),
            ),
            'logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/service/logout.json',
                    'defaults' => array(
                        'controller' => 'Service\Controller\Logout',
                        'action' => 'logout',
                        'format' => 'json'
                    ),
                ),
            ),
            'service' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/service/',
                    'defaults' => array(
                        'controller' => 'Service\Controller\Index',
                        'action' => 'index',
                    ),
                    'child_routes' => array(
                        'rest-service' => array(
                            'type' => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route' => ':controller[.:format]',
                                'defaults' => array(
                                    'action' => 'index',
                                ),
                                'constraints' => array(
                                    'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    'format' => '[a-zA-Z]+',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'get-user-has-agreement' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/service/user-has-agreement/get-user-agreement.json',
                    'defaults' => array(
                        'controller' => 'Service\Controller\UserHasAgreement',
                        'action' => 'getUserAgreement ',
                        'format' => 'json',
                    ),
                ),
            ),
            'migrationStatusUpdate' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/service/company/set-migration-status.json',
                    'defaults' => array(
                        'controller' => 'Service\Controller\Company',
                        'action' => 'setMigrationStatus',
                        'format' => 'json'
                    ),
                ),
            ),
            'upateCompanyCluster' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/service/company/update-cluster.json',
                    'defaults' => array(
                        'controller' => 'Service\Controller\Company',
                        'action' => 'updateCluster',
                        'format' => 'json'
                    ),
                ),
            ),
            'upateGetUserHasFacilityByToken' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/service/user-has-facility/get-by-token.json',
                    'defaults' => array(
                        'controller' => 'Service\Controller\UserHasFacility',
                        'action' => 'getByToken',
                        'format' => 'json'
                    ),
                ),
            ),
            'getDefaultCompanyByIdentityIdAction' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/service/user-has-facility/get-default-company-id.json',
                    'defaults' => array(
                        'controller' => 'Service\Controller\UserHasFacility',
                        'action' => 'getDefaultCompanyByIdentityId',
                        'format' => 'json'
                    ),
                ),
            ),
             'getListGhostBrowse' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/service/user-has-facility/get-list-ghostBrowse.json',
                    'defaults' => array(
                        'controller' => 'Service\Controller\UserHasFacility',
                        'action' => 'getListGhostBrowse',
                        'format' => 'json'
                    ),
                ),
            ),
            'getClusterIdsWithCompanyIdsAndFacilityIds' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/service/cluster.json/get-clusterIds-with-companyIds-and-facilityIds',
                    'defaults' => array(
                        'controller' => 'Service\Controller\Cluster',
                        'action' => 'getClusterIdsWithCompanyIdsAndFacilityIds',
                        'format' => 'json'
                    ),
                ),
            ),
            'getClusterIdsWithCompanyIds' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/service/cluster.json/get-clusterIds-with-companyIds',
                    'defaults' => array(
                        'controller' => 'Service\Controller\Cluster',
                        'action' => 'getClusterIdsWithCompanyIds',
                        'format' => 'json'
                    ),
                ),
            ),
            'evictSession' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/service/session-registry/delete-sessions.json',
                    'defaults' => array(
                        'controller' => 'Service\Controller\SessionRegistry',
                        'action' => 'delete-sessions',
                        'format' => 'json'
                    ),
                ),
            ),
        ),
    ),
);
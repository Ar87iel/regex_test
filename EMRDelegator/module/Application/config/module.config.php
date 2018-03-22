<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2012 WebPT, INC
 */
use EMRCore\Config\Controller\Config as ControllerConfig;

return array(

    'tokens' => array(
        'CLUSTER_ASSIGNMENT_COOKIE_NAME' => 'CLUSTER',
        'CLUSTER_ASSIGNMENT_COOKIE_DOMAIN' => 'webpt.com',
    ),

    ControllerConfig::CONFIG_KEY => array(
        'invokables' => array(
            'Application\Controller\Authorization' => 'Application\Controller\AuthorizationController',
            'Application\Controller\Announcements' => 'Application\Controller\AnnouncementsController',
            'Application\Controller\Agreement' => 'Application\Controller\AgreementController',
            'Application\Controller\Delegation' => 'Application\Controller\DelegationController',
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Redirect' => 'Application\Controller\RedirectController',
            'Application\Controller\SuperUserDelegation' => 'Application\Controller\SuperUserDelegationController',
            'Application\Controller\Eviction' => 'Application\Controller\EvictionController',
            'Application\Controller\AgreementDisplay' => 'Application\Controller\AgreementDisplayController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'default' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default_controller_action' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(),
                        ),
                    ),
                    'authorization' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => 'authorization[/]',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Authorization',
                                'action' => 'authorize'
                            ),
                        ),
                    ),
                    'announcements' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => 'announcements/acknowledged',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Announcements',
                                'action' => 'acknowledged'
                            ),
                        ),
                    ),
                    'agreements' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => 'agreement/agreed',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Agreement',
                                'action' => 'agreed'
                            ),
                        ),
                    ),
                    'cancel_eviction' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => 'eviction/cancel',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Eviction',
                                'action' => 'cancel'
                            ),
                        ),
                    ),
                    'evict' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => 'eviction/evict',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Eviction',
                                'action' => 'evict'
                            ),
                        ),
                    ),
                    'redirect' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => 'redirect/',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Redirect',
                                'action' => 'redirect'
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'layout/layout' => realpath(__DIR__ . '/../view/layout/layout.phtml'),
        ),
        'template_path_stack' => array(
            'application' => realpath(__DIR__ . '/../view'),
        ),
    ),

    'cookie_settings' => array(
        'cluster_assignment' => array(
            'name' => 'CLUSTER_ASSIGNMENT_COOKIE_NAME',
            'domain' => 'CLUSTER_ASSIGNMENT_COOKIE_DOMAIN',
        ),
    ),
);

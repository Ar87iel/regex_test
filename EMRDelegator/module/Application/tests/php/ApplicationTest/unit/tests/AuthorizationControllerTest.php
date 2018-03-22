<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 4/3/13 2:08 PM
 */

namespace ApplicationTest\unit\tests;
use Application\Controller\AuthorizationController;
use EMRCore\Service\Auth\Token\Dto\AuthorizeReturn;
use EMRDelegator\Model\SessionRegistry;
use EMRDelegator\Service\Session\Exception\SessionRegistryNotFound;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Literal as LiteralRoute;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Mvc\Router\SimpleRouteStack;
use Zend\Mvc\Controller\PluginManager;
use EMRCore\Service\Auth\Token\Exception\Authentication;
use EMRCoreTest\Helper\Reflection as Helper;
use EMRCore\Service\Auth\Application\Dto\Application;
use config;

class AuthorizationControllerTest extends PHPUnit_Framework_TestCase{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $forwardMock;
    /**
     * @var \Application\Controller\AuthorizationController
     */
    private $controller;

    /**
     * @var \Zend\Http\PhpEnvironment\Request
     */
    private $request;


    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $redirectMock;

    /**
     * @var \Zend\Mvc\Router\SimpleRouteStack
     */
    private $router;

    /**
     * @var \Zend\Mvc\Router\RouteMatch
     */
    private $routeMatch;

    /**
     * @var \Zend\Mvc\MvcEvent
     */
    private $mvcEvent;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $serviceLocatorMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $pluginManagerMock;

    public function setUp() {

        $this->controller = new AuthorizationController();
        $this->controller->setLogger($this->getMock('Logger', array(), array(), '', false));
        $this->request = new Request();
        $this->router = new SimpleRouteStack();
        $this->routeMatch = new RouteMatch(array( 'controller' => 'authorization', 'action' => 'authorize' ));
        $this->mvcEvent = new MvcEvent();
        $this->mvcEvent->setRouter($this->router);
        $this->mvcEvent->setRouteMatch($this->routeMatch);
        $this->mvcEvent->setRequest($this->request);
        $this->controller->setEvent($this->mvcEvent);

        $this->redirectMock = $this->getMock('Zend\Mvc\Controller\Plugin\Redirect');
        $this->forwardMock = $this->getMock('Zend\Mvc\Controller\Plugin\Forward', array(), array(), '', false);
        $this->serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $this->pluginManagerMock = $this->getMock('Zend\Mvc\Controller\PluginManager',array('get'));

        $this->controller->setServiceLocator($this->serviceLocatorMock);
        $this->controller->setPluginManager($this->pluginManagerMock);

        $logger = $this->getMock('Logger', array(), array(), '', false);
        $this->controller->setLogger($logger);

        $this->router->addRoute('default', LiteralRoute::factory(array(
            'route' => '/',
            'defaults' => array(
                'controller' => 'Application\Controller\AuthorizationController',
            ),
        )));

        $this->router->addRoute('default/redirect', LiteralRoute::factory(array(
            'route' => '/redirect/',
            'defaults' => array(
                'controller' => 'Application\Controller\RedirectController',
            ),
        )));
    }

    public function testAuthorizeRedirectsToSsoVerifyOnEmptyToken() {
        $testUrl = 'foo';
        $config = array(
            'slices' => array(
                'sso' => array(
                    'base' => 'https://slices.sso.localhost/s/auth/',
                    'verify' => 'https://slices.sso.localhost/s/auth/verify'
                ),
                'delegator' => array(
                    'base' => 'https://slices.sharding.emrdelegator.localhost',
                ),
            ),
        );
        $this->pluginManagerMock->expects($this->once())->method('get')->will($this->returnValue($this->redirectMock));
        $this->redirectMock->expects($this->once())->method('toUrl')->with()->will($this->returnValue($testUrl));
        $this->serviceLocatorMock->expects($this->any())->method('get')->with('Config')->will($this->returnValue($config));
        $this->controller->authorizeAction();
    }

    public function testAuthorizeRedirectsToSsoOnTokenAuthenticationException() {
        $testUrl = 'foo';
        $config = array(
            'slices' => array(
                'sso' => array(
                    'base' => 'https://slices.sso.localhost/s/auth/',
                    'verify' => 'https://slices.sso.localhost/s/auth/verify'
                ),
                'delegator' => array(
                    'base' => 'https://slices.sharding.emrdelegator.localhost',
                ),
            ),
        );

        $tokenMock = $this->getMock('\StdClass', array('authorize'));
        $tokenMock->expects($this->once())->method('authorize')->will($this->throwException(new Authentication()));
        $this->request->getQuery()->set('wpt_sso_token', 'foo');

        $this->pluginManagerMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($this->redirectMock));
        $this->redirectMock->expects($this->once())
            ->method('toUrl')
            ->with()
            ->will($this->returnValue($testUrl));
        $this->serviceLocatorMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnvalueMap(array(
                    array('Config', $config),
                    array('EMRCore\Service\Auth\Token\Token', $tokenMock),
            )));
        $this->controller->authorizeAction();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAuthorizeBubblesUnExpectedExceptionsUp() {
        $config = array(
            'slices' => array(
                'sso' => array(
                    'base' => 'https://slices.sso.localhost/s/auth/',
                    'verify' => 'https://slices.sso.localhost/s/auth/verify'
                ),
                'delegator' => array(
                    'base' => 'https://slices.sharding.emrdelegator.localhost',
                ),
            ),
        );

        $tokenMock = $this->getMock('\StdClass', array('authorize'));
        $tokenMock->expects($this->once())
            ->method('authorize')
            ->will($this->throwException(new \InvalidArgumentException()));
        $this->request->getQuery()->set('wpt_sso_token', 'foo');

        $this->pluginManagerMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($this->redirectMock));
        $this->serviceLocatorMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnvalueMap(array(
                array('Config', $config),
                array('EMRCore\Service\Auth\Token\Token', $tokenMock),
            )));
        $this->controller->authorizeAction();
    }

    public function testAuthorizationForwardsToDelegationController() {
        $test = 'foobar1';
        $facilityId = 7;
        $route = 'Application\Controller\Delegation';
        $token = 'foobar1';
        $userId = null;
        $params = array('action'=>'delegate' , 'token' => $token, 'facilityId' => $facilityId);

        $config = array(
            'slices' => array(
                'sso' => array(
                    'base' => 'https://slices.sso.localhost/s/auth/',
                    'verify' => 'https://slices.sso.localhost/s/auth/verify'
                ),
                'delegator' => array(
                    'base' => 'https://slices.sharding.emrdelegator.localhost',
                ),
            ),
        );

        $authorizedUserMock = $this->getMock('EMRCore\Service\Auth\Token\Dto\AuthorizeReturn');
        $tokenMock = $this->getMock('stdClass', array('authorize'));
        $tokenMock->expects($this->once())
            ->method('authorize')
            ->will($this->returnValue($authorizedUserMock));

        $sessionMock = $this->getMock('stdClass', array('hydrateAuthorizationSession'));
        $sessionMock->expects($this->once())
            ->method('hydrateAuthorizationSession')
            ->with($authorizedUserMock);

        $this->request->getQuery()->set('wpt_sso_token', $token);
        $this->request->getQuery()->set('facilityId', $facilityId);

        $this->forwardMock->expects($this->once())
            ->method('dispatch')
            ->with($route,$params)
            ->will($this->returnValue($test));

        $map = array(
            array('redirect', array(), true, $this->redirectMock),
            array('forward', array(), true, $this->forwardMock),
        );

        $this->pluginManagerMock->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($map));

        $this->serviceLocatorMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnvalueMap(array(
                array('Config', $config),
                array('EMRCore\Service\Auth\Token\Token', $tokenMock),
                array('EMRCore\Service\Session\Authorization', $sessionMock),
            )));


        $logger = $this->getMock('Logger', array(), array(), '', false);

        $controllerMock = $this->getMock('Application\Controller\AuthorizationController',array('evictionRequired'));
        $controllerMock->setLogger($this->getMock('Logger', array(), array(), '', false));
        $controllerMock->expects($this->once())->method('evictionRequired')
            ->with($authorizedUserMock)
            ->will($this->returnValue(false));

        /** @var \Application\Controller\AuthorizationController $controllerMock */
        $controllerMock->setEvent($this->mvcEvent);
        $controllerMock->setServiceLocator($this->serviceLocatorMock);
        $controllerMock->setPluginManager($this->pluginManagerMock);
        $controllerMock->setLogger($logger);
        $return = $controllerMock->authorizeAction();

        $this->assertEquals($test, $return);
    }

    public function testAuthorizationForwardsToSuperUserDelegationController() {
        $test = 'foobar2';

        $facilityId = '7';
        $identityId = 4;

        $route = 'Application\Controller\SuperUserDelegation';
        $token = 'foobar2';
        $userId = '123';
        $params = array('action'=>'delegate','token' => $token, 'ghostId'=>$userId, 'facilityId'=>$facilityId);


        $config = array(
            'slices' => array(
                'sso' => array(
                    'base' => 'https://slices.sso.localhost/s/auth/',
                    'verify' => 'https://slices.sso.localhost/s/auth/verify'
                ),
                'delegator' => array(
                    'base' => 'https://slices.sharding.emrdelegator.localhost',
                ),
            ),
        );

        $authorizedUserMock = $this->getMock('EMRCore\Service\Auth\Token\Dto\AuthorizeReturn');
        $tokenMock = $this->getMock('\StdClass', array('authorize'));
        $tokenMock->expects($this->once())
            ->method('authorize')
            ->will($this->returnValue($authorizedUserMock));

        $sessionMock = $this->getMock('stdClass', array('hydrateAuthorizationSession'));
        $sessionMock->expects($this->once())
            ->method('hydrateAuthorizationSession')
            ->with($authorizedUserMock);

        $this->request->getQuery()->set('wpt_sso_token', $token);
        $this->request->getQuery()->set('ghostId', $userId);
        $this->request->getQuery()->set('identityId', $identityId);
        $this->request->getQuery()->set('facilityId', $facilityId);

        $map = array(
            array('redirect', array(), true, $this->redirectMock),
            array('forward', array(), true, $this->forwardMock),
        );
        $this->pluginManagerMock->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($map));

        $this->serviceLocatorMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnvalueMap(array(
                array('Config', $config),
                array('EMRCore\Service\Auth\Token\Token', $tokenMock),
                array('EMRCore\Service\Session\Authorization', $sessionMock),
            )));

        $this->forwardMock->expects($this->once())
            ->method('dispatch')
            ->with($route,$params)
            ->will($this->returnValue($test));

        $logger = $this->getMock('Logger', array(), array(), '', false);

        $controllerMock = $this->getMock('Application\Controller\AuthorizationController',array('evictionRequired'));
        $controllerMock->setLogger($this->getMock('Logger', array(), array(), '', false));
        $controllerMock->expects($this->once())->method('evictionRequired')
            ->with($authorizedUserMock)
            ->will($this->returnValue(false));

        /** @var \Application\Controller\AuthorizationController $controllerMock */
        $controllerMock->setEvent($this->mvcEvent);
        $controllerMock->setServiceLocator($this->serviceLocatorMock);
        $controllerMock->setPluginManager($this->pluginManagerMock);
        $controllerMock->setLogger($logger);
        $return = $controllerMock->authorizeAction();

        $this->assertEquals($test, $return);
    }

    public function testAuthorizationForwardsToEviction() {
        $test = 'return stuff';

        $facilityId = '7';
        $identityId = 4;

        $route = 'Application\Controller\Eviction';
        $token = 'foobar2';
        $userId = '123';
        $params = array('action'=>'display','wpt_sso_token' => $token);


        $config = array(
            'slices' => array(
                'sso' => array(
                    'base' => 'https://slices.sso.localhost/s/auth/',
                    'verify' => 'https://slices.sso.localhost/s/auth/verify'
                ),
                'delegator' => array(
                    'base' => 'https://slices.sharding.emrdelegator.localhost',
                ),
            ),
        );

        $authorizedUserMock = $this->getMock('EMRCore\Service\Auth\Token\Dto\AuthorizeReturn');
        $authorizedUserMock->expects($this->once())->method('getSessionId')->will($this->returnValue($token));

        $tokenMock = $this->getMock('\StdClass', array('authorize'));
        $tokenMock->expects($this->once())
            ->method('authorize')
            ->will($this->returnValue($authorizedUserMock));

        $sessionMock = $this->getMock('stdClass', array('hydrateAuthorizationSession'));
        $sessionMock->expects($this->once())
            ->method('hydrateAuthorizationSession')
            ->with($authorizedUserMock);

        $this->request->getQuery()->set('wpt_sso_token', $token);
        $this->request->getQuery()->set('ghostId', $userId);
        $this->request->getQuery()->set('identityId', $identityId);
        $this->request->getQuery()->set('facilityId', $facilityId);

        $map = array(
            array('redirect', array(), true, $this->redirectMock),
            array('forward', array(), true, $this->forwardMock),
        );
        $this->pluginManagerMock->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($map));

        $this->serviceLocatorMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnvalueMap(array(
                array('Config', $config),
                array('EMRCore\Service\Auth\Token\Token', $tokenMock),
                array('EMRCore\Service\Session\Authorization', $sessionMock),
            )));

        $this->forwardMock->expects($this->once())
            ->method('dispatch')
            ->with($route,$params)
            ->will($this->returnValue($test));

        $logger = $this->getMock('Logger', array(), array(), '', false);

        $controllerMock = $this->getMock('Application\Controller\AuthorizationController',array('evictionRequired'));
        $controllerMock->setLogger($this->getMock('Logger', array(), array(), '', false));
        $controllerMock->expects($this->once())->method('evictionRequired')
            ->with($authorizedUserMock)
            ->will($this->returnValue(true));

        /** @var \Application\Controller\AuthorizationController $controllerMock */
        $controllerMock->setEvent($this->mvcEvent);
        $controllerMock->setServiceLocator($this->serviceLocatorMock);
        $controllerMock->setPluginManager($this->pluginManagerMock);
        $controllerMock->setLogger($logger);
        $return = $controllerMock->authorizeAction();

        $this->assertEquals($test, $return);
    }

    public function testEvictionRequiredReturnsTrue()
    {
        $token = 'asdfzxcv1234';
        $userId = 321;
        $authorizedUser = new AuthorizeReturn();
        $authorizedUser->setUserId($userId);
        $authorizedUser->setSessionId($token);
        $authorizedUser->setApplications(array());

        $sessionRegistryModel = $this->getMock('EMRDelegator\Model\SessionRegistry');
        $sessionRegistryModel->expects($this->once())
            ->method('getSsoToken')
            ->will($this->returnValue('someothertoken'));

        $sessionRegistryService = $this->getMock('EMRDelegator\Service\Session\Registry');
        $sessionRegistryService->expects($this->once())->method('getByIdentityId')
            ->with($userId)
            ->will($this->returnValue($sessionRegistryModel));

        /** @var AuthorizationController|PHPUnit_Framework_MockObject_MockObject $controllerMock */
        $controllerMock = $this->getMock('Application\Controller\AuthorizationController',
            array('getSessionRegistryService'));
        $controllerMock->setLogger($this->getMock('Logger', array(), array(), '', false));
        $controllerMock->expects($this->once())->method('getSessionRegistryService')
            ->will($this->returnValue($sessionRegistryService));

        $controllerMock->setServiceLocator($this->serviceLocatorMock);

        $this->serviceLocatorMock->expects($this->once())
            ->method('get')
            ->will($this->returnCallback(
                function($name)
                {
                    switch ($name)
                    {
                        case 'config':
                            return array(
                                'evictionEnabled' => true
                            );
                            break;
                        default:
                            throw new \RuntimeException("Mocked ServiceManager could not create name [$name].");
                            break;
                    }
                }
            ));

        $result = Helper::invoke($controllerMock,'evictionRequired',array($authorizedUser));
        $this->assertTrue($result);
    }

    public function testEvictionRequiredRegistersTokenReturnsFalse()
    {
        $token = 'asdfzxcv1234';
        $userId = 321;
        $authorizedUser = new AuthorizeReturn();
        $authorizedUser->setUserId($userId);
        $authorizedUser->setSessionId($token);
        $authorizedUser->setApplications(array());

        $sessionRegistryService = $this->getMock('EMRDelegator\Service\Session\Registry');
        $sessionRegistryService->expects($this->once())->method('getByIdentityId')
            ->with($userId)
            ->will($this->throwException(new SessionRegistryNotFound()));

        /** @var AuthorizationController|PHPUnit_Framework_MockObject_MockObject $controllerMock */
        $controllerMock = $this->getMock('Application\Controller\AuthorizationController',
            array('getSessionRegistryService','registerSession'));
        $controllerMock->setLogger($this->getMock('Logger', array(), array(), '', false));
        $controllerMock->expects($this->once())->method('getSessionRegistryService')
            ->will($this->returnValue($sessionRegistryService));
        $controllerMock->expects($this->once())->method('registerSession')->with($authorizedUser);

        $controllerMock->setServiceLocator($this->serviceLocatorMock);

        $this->serviceLocatorMock->expects($this->once())
            ->method('get')
            ->will($this->returnCallback(
                function($name)
                {
                    switch ($name)
                    {
                        case 'config':
                            return array(
                                'evictionEnabled' => true
                            );
                            break;
                        default:
                            throw new \RuntimeException("Mocked ServiceManager could not create name [$name].");
                            break;
                    }
                }
            ));

        $result = Helper::invoke($controllerMock,'evictionRequired',array($authorizedUser));
        $this->assertFalse($result);
    }

    public function testEvictionRequiredRegistersTokenReturnsFalseOnAdmin()
    {
        $token = 'asdfzxcv1234';
        $userId = 321;
        $authorizedUser = new AuthorizeReturn();
        $authorizedUser->setUserId($userId);
        $authorizedUser->setSessionId($token);
        $application = new Application();
        $application->setApplicationId(Application::ADMIN);
        $authorizedUser->setApplications(array($application));

        $controllerMock = $this->getMock('Application\Controller\AuthorizationController',
            array('getSessionRegistryService','registerSession'));
        $controllerMock->setLogger($this->getMock('Logger', array(), array(), '', false));

        $result = Helper::invoke($controllerMock,'evictionRequired',array($authorizedUser));
        $this->assertFalse($result);
    }
}
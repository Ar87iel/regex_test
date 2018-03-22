<?php
/**
 * @category WebPT
 * @package ApplicationTest
 * @copyright Copyright (c) 2012 WebPT, INC
 */
namespace ApplicationTest\Unit;

use ReflectionClass;
use Zend\Http\PhpEnvironment\Response;
use EMRCore\ServiceFactory;
use PHPUnit_Framework_MockObject_Generator;
use Application\Controller\EvictionController;
use Poser;
use EMRCore\Session\SessionFactory;
use PHPUnit_Framework_MockObject_MockObject;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\SimpleRouteStack;
use Zend\Mvc\Router\Http\Literal;
use Zend\View\Model\ViewModel;

class EvictionControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EvictionController
     */
    private $controller;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var SimpleRouteStack
     */
    private $router;
    /**
     * @var RouteMatch
     */
    private $routeMatch;
    /**
     * @var MvcEvent
     */
    private $mvcEvent;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $serviceLocatorMock;
    /**
     * @var ServiceFactory
     */
    private $serviceFactory;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionInterfaceMock;
    /**
     * @var string
     */
    private $authorizeRoute = '/authorize';
    /**
     * @var string
     */
    private $loginRoute = '/s/auth/logout/';

    public function setUp()
    {
        $this->router = new SimpleRouteStack();
        $this->router->addRoute('default/authorization', Literal::factory(array(
            'route' => $this->authorizeRoute,
            'defaults' => array(
                'controller' => 'Application\Controller\AuthorizeController',
            ),
        )));

        $this->mvcEvent = new MvcEvent();
        $this->mvcEvent->setRouter($this->router);

        $this->controller = $this->getMock('Application\Controller\EvictionController', array('getSsoTokenAuthorizeUrl'));
        $this->controller->expects($this->any())
            ->method('getSsoTokenAuthorizeUrl')
            ->will($this->returnValue('/s/auth'));
        $this->controller->setEvent($this->mvcEvent);

        $this->request = new Request();

        $this->sessionInterfaceMock = $this->getMock('EMRCore\Session\Instance\Authorization');

        $this->controller->setAuthorizationSession($this->sessionInterfaceMock);

        $serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->controller->setServiceLocator($serviceLocatorMock);
    }

    /**
     * @param string $controller
     * @param string $action
     */
    private function setRouteMatch($controller, $action)
    {
        $this->routeMatch = new RouteMatch(array( 'controller' => $controller, 'action' => $action ));
        $this->mvcEvent->setRouteMatch($this->routeMatch);
    }

    /**
     *
     */
    private function setCancelRouteMatch()
    {
        $this->setRouteMatch('eviction', 'cancel');
    }

    /**
     *
     */
    private function setDisplayRouteMatch()
    {
        $this->setRouteMatch('eviction', 'display');
    }

    /**
     *
     */
    private function setEvictionRouteMatch()
    {
        $this->setRouteMatch('eviction', 'evict');
    }

    /**
     * @return Response
     */
    private function dispatch()
    {
        return $this->controller->dispatch($this->request);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getRedirectPluginMock()
    {
        $redirectPlugin = $this->getMock('Zend\Mvc\Controller\Plugin\Redirect');

        /** @var PHPUnit_Framework_MockObject_MockObject $pluginManagerMock */
        $pluginManagerMock = $this->getMock('Zend\Mvc\Controller\PluginManager', array( 'get' ));
        $pluginManagerMock->expects($this->any())->method('get')->with('redirect')->will($this->returnValue($redirectPlugin));

        /** @var PluginManager $pluginManagerMock */
        $this->controller->setPluginManager($pluginManagerMock);

        return $redirectPlugin;
    }

    /**
     * @param Response $response
     * @param string $uri
     */
    private function assertRedirectedToUri(Response $response, $uri)
    {
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect());
        $this->assertSame($uri, $response->getHeaders()->get('location')->getFieldValue());
    }

    /**
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject|\Application\Controller\EvictionController
     */
    private function getControllerMock($methods = array())
    {
        return $this->getMock('Application\Controller\EvictionController', $methods);
    }

    /**
     * @param array $methods
     * @param array $arguments
     * @param string $mockClassName
     * @param bool $callOriginalConstructor
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getEvictionServiceMock($methods = array( 'evictUser' ), $arguments = array(), $mockClassName = '', $callOriginalConstructor = false)
    {
        /** @var $sessionService PHPUnit_Framework_MockObject_MockObject */
        $evictionService = $this->getMock('EMRDelegator\Service\Session\Evict', $methods, $arguments, $mockClassName, $callOriginalConstructor);

        $this->controller->getServiceLocator()->expects($this->once())
            ->method('get')
            ->with('EMRDelegator\Service\Session\Evict')
            ->will($this->returnValue($evictionService));

        return $evictionService;
    }

    /**
     * Test if Cancelling eviction redirects to logout
     */
    public function testCancelRedirectsToLogin()
    {
        $url = 'stuff';
        $expectedLogoutUrl = $url.'/logout/';

        $redirectPlugin = $this->getMock('Zend\Mvc\Controller\Plugin\Redirect');
        $redirectPlugin->expects($this->once())
            ->method('toUrl')
            ->with($this->equalTo($expectedLogoutUrl));

        $controller = $this->getControllerMock(array('plugin','getSsoBaseUrl'));
        $controller->expects($this->once())
            ->method('plugin')
            ->with('redirect')
            ->will($this->returnValue($redirectPlugin));
        $controller->expects($this->once())
            ->method('getSsoBaseUrl')
            ->will($this->returnValue($url));

        $response = $controller->cancelAction();
    }

    /**
     * Test that display gets user id
     */
    public function testDisplayReturnsViewModelWithUserId()
    {
        $this->setDisplayRouteMatch();

        $this->sessionInterfaceMock
            ->expects($this->exactly(1))
            ->method('get')
            ->with('userId')
            ->will($this->returnValue(123));

        /** @var ViewModel $response */
        $response = $this->dispatch();
        $responseUserId = $response->getVariable('userId');

        $this->assertEquals(123, $responseUserId);
    }

    /**
     * Test that evict actually evicts user
     */
    public function testEvictCallsEvictUser()
    {
        $mockUserId = 66;

        $this->setEvictionRouteMatch();

        $evictionService = $this->getEvictionServiceMock();
        $evictionService->expects($this->exactly(1))->method('evictUser')->with($mockUserId);

        $this->sessionInterfaceMock->expects($this->exactly(1))
            ->method('get')
            ->with('userId')
            ->will($this->returnValue($mockUserId));

        $this->dispatch();
    }

    /**
     * Test that evict redirects back to authorize
     */
    public function testEvictRedirectsToAuthorize()
    {
        $mockUserId = 66;

        $this->setEvictionRouteMatch();

        $evictionService = $this->getEvictionServiceMock();

        $this->sessionInterfaceMock->expects($this->exactly(1))
            ->method('get')
            ->with('userId')
            ->will($this->returnValue($mockUserId));

        $response = $this->dispatch();

        $this->assertRedirectedToUri($response, $this->authorizeRoute);
    }

    public function testGetSsoBaseUrl() {
        $url = 'http://foo';
        $config = array('slices' => array('sso' => array('base' => $url)));
        $serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));
        $this->controller->setServiceLocator($serviceLocatorMock);

        $reflectionClass = new ReflectionClass('Application\Controller\EvictionController');
        $method = $reflectionClass->getMethod('getSsoBaseUrl');
        $method->setAccessible(true);

        $result = $method->invoke($this->controller);
        $this->assertEquals($url, $result);
    }

}
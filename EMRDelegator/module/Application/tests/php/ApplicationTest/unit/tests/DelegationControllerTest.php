<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * 4/3/13 2:08 PM
 */

namespace ApplicationTest\unit\tests;

use Application\Controller\DelegationController;
use Application\Service\Delegation\Dto\Delegate;
use Application\Service\Delegation\Dto\Delegation as DelegationDto;
use EMRCoreTest\Helper\Reflection;
use EMRDelegator\Model\Agreement;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Zend\Http\Header\Cookie;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Literal as LiteralRoute;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Mvc\Router\SimpleRouteStack;
use Application\Service\Delegation\Dto\Delegate as DelegateDto;
use EMRCoreTest\Helper\Reflection as Helper;

class DelegationControllerTest extends PHPUnit_Framework_TestCase {

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $forwardMock;
    /**
     * @var \Application\Controller\DelegationController
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

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $mockAuthorizationSession;

    /**
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject|\Application\Controller\DelegationController
     */
    protected function getControllerMock($methods = array()) {
        return $this->getMock('Application\Controller\DelegationController', $methods);
    }

    public function setUp() {

        $this->controller = new DelegationController();
        $this->request = new Request();
        $this->router = new SimpleRouteStack();
        $this->routeMatch = new RouteMatch(array( 'controller' => 'delegation', 'action' => 'delegate' ));
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

        $this->router->addRoute('default', LiteralRoute::factory(array(
            'route' => '/',
            'defaults' => array(
                'controller' => 'Application\Controller\DelegationController',
            ),
        )));

        $this->router->addRoute('default/redirect', LiteralRoute::factory(array(
            'route' => '/redirect/',
            'defaults' => array(
                'controller' => 'Application\Controller\RedirectController',
            ),
        )));

        $this->mockAuthorizationSession = $this->getMock('EMRCore\Session\Instance\Authorization');
        $this->controller->setAuthorizationSession($this->mockAuthorizationSession);
    }

    public function testDelegateActionForwardsToAnnouncements() {
        $announcements = array('foo');
        $test = 'bar';

        $controller = $this->getControllerMock(array('getUnseenAnnouncements','forwardToAnnouncements'));
        $controller->expects($this->once())
            ->method('getUnseenAnnouncements')
            ->will($this->returnValue($announcements));
        $controller->expects($this->once())
            ->method('forwardToAnnouncements')
            ->with($announcements)
            ->will($this->returnValue($test));
        $result = $controller->delegateAction();
        $this->assertEquals($test, $result);
    }

    public function testDelegateActionForwardsToAgreements() {
        $agreement = new Agreement();
        $test = 'bar';

        $controller = $this->getControllerMock(array('getUnseenAnnouncements','getOutstandingAgreement','forwardToAgreements'));
        $controller->expects($this->once())
            ->method('getUnseenAnnouncements');
        $controller->expects($this->once())
            ->method('getOutstandingAgreement')
            ->will($this->returnValue($agreement));
        $controller->expects($this->once())
            ->method('forwardToAgreements')
            ->with($agreement)
            ->will($this->returnValue($test));
        $result = $controller->delegateAction();
        $this->assertEquals($test, $result);
    }

    public function testDelegateActionWrapsDelegateMethod() {
        $test = 'foo';
        $facility = 7;
        $userId = 9;
        $delegateDto = new Delegate();
        $delegateDto->setFacilityId($facility);
        $delegateDto->setUserId($userId);

        $controller = $this->getControllerMock(array('getUnseenAnnouncements','getOutstandingAgreement',
            'delegate', 'getUserId', 'getFacilityId'));
        $controller->expects($this->once())
            ->method('getUnseenAnnouncements');
        $controller->expects($this->once())
            ->method('getOutstandingAgreement');
        $controller->expects($this->once())
            ->method('delegate')
            ->with($delegateDto)
            ->will($this->returnValue($test));
        $controller->expects($this->once())
            ->method('getUserId')
            ->will($this->returnValue($userId));
        $controller->expects($this->once())
            ->method('getFacilityId')
            ->will($this->returnValue($facility));

        $result = $controller->delegateAction();
        $this->assertEquals($test, $result);
    }

    public function testDelegateSetsCookieHeaders() {
        $token = 'foo';
        $url = 'http://baz';
        $cookie = 'chocolate chip';
        $test = 'fuz';

        $delegationMock = $this->getMock('stdClass', array('getUrl', 'getCookie'));
        $delegationMock->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($url));
        $delegationMock->expects($this->exactly(2))
            ->method('getCookie')
            ->will($this->returnValue($cookie));

        $delegateMock = $this->getMock('Application\Service\Delegation\Dto\Delegate');
        $delegateMock->expects($this->once())
            ->method('setToken')
            ->with($token);

        $serviceMock = $this->getMock('Application\Service\Delegation\Delegation');
        $serviceMock->expects($this->once())
            ->method('delegate')
            ->with($delegateMock)
            ->will($this->returnValue($delegationMock));

        $locatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $locatorMock->expects($this->once())
            ->method('get')
            ->with('Application\Service\Delegation\Delegation')
            ->will($this->returnValue($serviceMock));

        $headerMock = $this->getMock('stdClass', array('addHeader'));
        $headerMock->expects($this->once())
            ->method('addHeader')
            ->with($cookie);

        $responseMock = $this->getMock('stdClass', array('getHeaders'));
        $responseMock->expects($this->once())
            ->method('getHeaders')
            ->will($this->returnValue($headerMock));

        $controller = $this->getControllerMock(array('getToken', 'getResponse', 'forwardToRedirect'));
        $controller->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($token));
        $controller->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($responseMock));
        $controller->expects($this->once())
            ->method('forwardToRedirect')
            ->with($url)
            ->will($this->returnValue($test));
        $controller->setServiceLocator($locatorMock);

        $result = Reflection::invoke($controller, 'delegate', array($delegateMock));
        $this->assertEquals($test, $result);
    }

    public function testDelegateDoesNotSetCookie() {
        $token = 'foo';
        $url = 'http://baz';
        $test = 'fuz';

        $delegationMock = $this->getMock('stdClass', array('getUrl', 'getCookie'));
        $delegationMock->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($url));
        $delegationMock->expects($this->once())
            ->method('getCookie')
            ->will($this->returnValue(null));
        $delegateMock = $this->getMock('Application\Service\Delegation\Dto\Delegate');
        $delegateMock->expects($this->once())
            ->method('setToken')
            ->with($token);

        $serviceMock = $this->getMock('Application\Service\Delegation\Delegation');
        $serviceMock->expects($this->once())
            ->method('delegate')
            ->with($delegateMock)
            ->will($this->returnValue($delegationMock));

        $locatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $locatorMock->expects($this->once())
            ->method('get')
            ->with('Application\Service\Delegation\Delegation')
            ->will($this->returnValue($serviceMock));

        $controller = $this->getControllerMock(array('getToken', 'getResponse', 'forwardToRedirect'));
        $controller->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($token));
        $controller->expects($this->never())
            ->method('getResponse');
        $controller->expects($this->once())
            ->method('forwardToRedirect')
            ->with($url)
            ->will($this->returnValue($test));
        $controller->setServiceLocator($locatorMock);

        $result = Reflection::invoke($controller, 'delegate', array($delegateMock));
        $this->assertEquals($test, $result);
    }

    public function testGetUnseenAnnouncementsCallBusinessService()
    {
        $userId = 432;
        $returnValue = 'stuff';

        $mockAnnounceService = $this->getMock('EMRDelegator\Service\Announcement\Announcement');
        $mockAnnounceService->expects($this->once())
            ->method('getOutstandingAnnouncements')
            ->with($userId)
            ->will($this->returnValue($returnValue));

        $serviceMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceMock->expects($this->once())
            ->method('get')
            ->with('EMRDelegator\Service\Announcement\Announcement')
            ->will($this->returnValue($mockAnnounceService));

        $controller = $this->getMock('Application\Controller\DelegationController', array('getUserId'));
        $controller->expects($this->once())->method('getUserId')->will($this->returnValue($userId));

        /** @var $controller \Application\Controller\DelegationController */
        $controller->setServiceLocator($serviceMock);

        Helper::invoke($controller, 'getUnseenAnnouncements');
    }

    public function testForwardToAnnouncementWhenAnnoucmentsPresent()
    {
        $announcementsValue = 'stuff';

        $controller = $this->getMock('Application\Controller\DelegationController',
            array('getUnseenAnnouncements', 'forwardToAnnouncements'));
        $controller->expects($this->once())->method('getUnseenAnnouncements')
            ->will($this->returnValue($announcementsValue));
        $controller->expects($this->once())->method('forwardToAnnouncements')
            ->with($announcementsValue);

        /** @var $controller \Application\Controller\DelegationController */
        $controller->delegateAction();
    }

}
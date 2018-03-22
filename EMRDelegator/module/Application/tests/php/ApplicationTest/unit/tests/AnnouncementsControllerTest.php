<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 5/15/13 3:13 PM
 */

namespace ApplicationTest\unit\tests;
use EMRCoreTest\Helper\Reflection;

class AnnouncementsControllerTest extends \PHPUnit_Framework_TestCase {
    /**
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject|\Application\Controller\AnnouncementsController
     */
    protected function getControllerMock($methods = array()) {
        return $this->getMock('Application\Controller\AnnouncementsController', $methods);
    }
    public function testAcknowledgedActionWrapsForwardToDelegation() {
        $token = 'foo';
        $facility = 'bar';
        $test='return';

        $controller = $this->getControllerMock(array('acknowledgeAnnouncements', 'getTokenFromRequest',
            'getFacilityFromRequest', 'forwardToDelegation'));
        $controller->expects($this->once())
            ->method('acknowledgeAnnouncements');
        $controller->expects($this->once())
            ->method('getTokenFromRequest')
            ->will($this->returnValue($token));
        $controller->expects($this->once())
            ->method('getFacilityFromRequest')
            ->will($this->returnValue($facility));
        $controller->expects($this->once())
            ->method('forwardToDelegation')
            ->with($token, $facility)
            ->will($this->returnValue($test));

        $result = $controller->acknowledgedAction();
        $this->assertEquals($test, $result);
    }

    public function testDisplayActionReturnsViewModel() {
        $announcements = array('test');
        $token = 'foo';
        $facility = 'bar';

        $controller = $this->getControllerMock(array('getAnnouncements', 'getTokenFromRouteMatch', 'getFacilityFromRouteMatch'));
        $controller->expects($this->once())
            ->method('getAnnouncements')
            ->will($this->returnValue($announcements));
        $controller->expects($this->once())
            ->method('getTokenFromRouteMatch')
            ->will($this->returnValue($token));
        $controller->expects($this->once())
            ->method('getFacilityFromRouteMatch')
            ->will($this->returnValue($facility));
        /** @var \Zend\View\Model\ViewModel $result */
        $result = $controller->displayAction();
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
        $this->assertEquals($announcements, $result->getVariable('announcements'));
        $this->assertEquals($token, $result->getVariable('token'));
        $this->assertEquals($facility, $result->getVariable('facility'));
    }

    public function testGetUserId() {
        $userId = 73;
        $sessionMock = $this->getMock('EMRCore\Session\Instance\Authorization');
        $sessionMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($userId));

        $controller = $this->getControllerMock(array('getAnnouncements'));
        $controller->setAuthorizationSession($sessionMock);
        $result = Reflection::invoke($controller, 'getUserId');
        $this->assertEquals($userId, $result);
    }

    public function testGetAnnouncements() {
        $announcements = array('foo');
        $controller = $this->getControllerMock(array('getAnnouncementsFromRouteMatch', 'getAnnouncementsFromService'));
        $controller->expects($this->once())
            ->method('getAnnouncementsFromRouteMatch');
        $controller->expects($this->once())
            ->method('getAnnouncementsFromService')
            ->will($this->returnValue($announcements));
        $result = Reflection::invoke($controller, 'getAnnouncements');
        $this->assertEquals($announcements, $result);
    }

    public function testForwardToDelegationPassesRouteParamsToForwardPlugin() {
        $test = 'foo';
        $token = 'bar';
        $facility = 'baz';
        $route = 'Application\Controller\Delegation';

        $params = array(
            'action' => 'delegate',
            'token' => $token,
            'facilityId' => $facility
        );
        $forwardMock = $this->getMock('stdClass', array('dispatch'));
        $forwardMock->expects($this->once())
            ->method('dispatch')
            ->with($route, $params)
            ->will($this->returnValue($test));
        $controller = $this->getControllerMock(array('getForwardPlugin'));
        $controller->expects($this->once())
            ->method('getForwardPlugin')
            ->will($this->returnValue($forwardMock));
        $result = Reflection::invoke($controller, 'forwardToDelegation', array($token, $facility));
        $this->assertEquals($test, $result);
    }

    public function testGetFacilityFromRouteMatch() {
        $test = 'foo';
        $param = 'facilityId';
        $routeMock = $this->getMock('stdClass', array('getParam'));
        $routeMock->expects($this->once())
            ->method('getParam')
            ->with($param)
            ->will($this->returnValue($test));
        $eventMock = $this->getMock('stdClass', array('getRouteMatch'));
        $eventMock->expects($this->once())
            ->method('getRouteMatch')
            ->will($this->returnValue($routeMock));
        $controller = $this->getControllerMock(array('getEvent'));
        $controller->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($eventMock));
        $result = Reflection::invoke($controller, 'getFacilityFromRouteMatch');
        $this->assertEquals($test, $result);
    }

    public function testGetTokenFromRouteMatch() {
        $test = 'foo';
        $param = 'token';
        $routeMock = $this->getMock('stdClass', array('getParam'));
        $routeMock->expects($this->once())
            ->method('getParam')
            ->with($param)
            ->will($this->returnValue($test));
        $eventMock = $this->getMock('stdClass', array('getRouteMatch'));
        $eventMock->expects($this->once())
            ->method('getRouteMatch')
            ->will($this->returnValue($routeMock));
        $controller = $this->getControllerMock(array('getEvent'));
        $controller->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($eventMock));
        $result = Reflection::invoke($controller, 'getTokenFromRouteMatch');
        $this->assertEquals($test, $result);
    }

    public function testGetAnnouncementsFromRouteMatch() {
        $test = 'foo';
        $param = 'announcements';
        $routeMock = $this->getMock('stdClass', array('getParam'));
        $routeMock->expects($this->once())
            ->method('getParam')
            ->with($param)
            ->will($this->returnValue($test));
        $eventMock = $this->getMock('stdClass', array('getRouteMatch'));
        $eventMock->expects($this->once())
            ->method('getRouteMatch')
            ->will($this->returnValue($routeMock));
        $controller = $this->getControllerMock(array('getEvent'));
        $controller->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($eventMock));
        $result = Reflection::invoke($controller, 'getAnnouncementsFromRouteMatch');
        $this->assertEquals($test, $result);
    }

    public function testGetFacilityFromRequest() {
        $test = 'foo';
        $param = 'facility';
        $eventMock = $this->getMock('stdClass', array('getParam'));
        $eventMock->expects($this->once())
            ->method('getParam')
            ->with($param)
            ->will($this->returnValue($test));
        $controller = $this->getControllerMock(array('getEvent'));
        $controller->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($eventMock));
        $result = Reflection::invoke($controller, 'getFacilityFromRequest');
        $this->assertEquals($test, $result);
    }

    public function testGetTokenFromRequest() {
        $test = 'foo';
        $param = 'token';
        $eventMock = $this->getMock('stdClass', array('getParam'));
        $eventMock->expects($this->once())
            ->method('getParam')
            ->with($param)
            ->will($this->returnValue($test));
        $controller = $this->getControllerMock(array('getEvent'));
        $controller->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($eventMock));
        $result = Reflection::invoke($controller, 'getTokenFromRequest');
        $this->assertEquals($test, $result);
    }

    public function testAcknowledgeAnnouncements() {
        $userId = 43;
        $serviceMock = $this->getMock('stdClass', array('addAcknowledgement'));
        $serviceMock->expects($this->once())
            ->method('addAcknowledgement')
            ->with($userId);


        $locatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $locatorMock->expects($this->once())
            ->method('get')
            ->with('EMRDelegator\Service\IdentityAnnouncements\IdentityAnnouncements')
            ->will($this->returnValue($serviceMock));

        $controller = $this->getControllerMock(array('getUserId'));
        $controller->expects($this->once())
            ->method('getUserId')
            ->will($this->returnValue($userId));
        $controller->setServiceLocator($locatorMock);
        Reflection::invoke($controller, 'acknowledgeAnnouncements');
    }

    public function testGetAnnouncementsFromService() {
        $userId = 43;
        $announcements = array('foo');
        $serviceMock = $this->getMock('stdClass', array('getOutstandingAnnouncements'));
        $serviceMock->expects($this->once())
            ->method('getOutstandingAnnouncements')
            ->with($userId)
            ->will($this->returnValue($announcements));


        $locatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $locatorMock->expects($this->once())
            ->method('get')
            ->with('EMRDelegator\Service\Announcement\Announcement')
            ->will($this->returnValue($serviceMock));

        $controller = $this->getControllerMock(array('getUserId'));
        $controller->expects($this->once())
            ->method('getUserId')
            ->will($this->returnValue($userId));
        $controller->setServiceLocator($locatorMock);
        $result = Reflection::invoke($controller, 'getAnnouncementsFromService');
        $this->assertEquals($announcements, $result);
    }
}

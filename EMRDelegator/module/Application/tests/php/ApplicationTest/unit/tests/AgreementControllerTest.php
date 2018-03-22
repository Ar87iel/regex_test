<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 5/15/13 5:38 PM
 */

namespace ApplicationTest\unit\tests;
use Application\Controller\AgreementController;
use EMRCoreTest\Helper\Reflection;
use EMRDelegator\Service\Agreement\Dao\Dto\SignData;
use ReflectionClass;
use Zend\View\Model\ViewModel;

class AgreementControllerTest extends \PHPUnit_Framework_TestCase {
    /**
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject|\Application\Controller\AgreementController
     */
    protected function getControllerMock($methods = array()) {
        return $this->getMock('Application\Controller\AgreementController', $methods);
    }

    public function testAgreedActionWrapsForwardToDelegation() {
        $agreementId = 42;
        $test = 'foo';
        $token = 'baz';
        $facility = 'biz';
        $ghostId = 0;

        $controller = $this->getControllerMock(array(
            'getAgreementIdFromRequest',
            'signAgreement',
            'forwardToDelegation',
            'getToken',
            'getFacilityId',
            'getGhostId',
        ));
        $controller->expects($this->once())
            ->method('getAgreementIdFromRequest')
            ->will($this->returnValue($agreementId));
        $controller->expects($this->once())
            ->method('signAgreement')
            ->with($agreementId);
        $controller->expects($this->once())
            ->method('forwardToDelegation')
            ->with($token, $facility, $ghostId)
            ->will($this->returnValue($test));
        $controller->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($token));
        $controller->expects($this->once())
            ->method('getFacilityId')
            ->will($this->returnValue($facility));
        $controller->expects($this->once())
            ->method('getGhostId')
            ->will($this->returnValue($ghostId));

        $result = Reflection::invoke($controller, 'agreedAction');
        $this->assertEquals($test, $result);
    }

    public function testDisplayActionReturnsViewModel() {

        $logoutUrl = 'http://logout';
        $token = 'biz';
        $facility = 9;
        $identityId = 8;
        $ghostId = 3;

        $agreementType = $this->getMock('EMRDelegator\Model\AgreementType');
        $agreementType->expects($this->once())
            ->method('getTypeKey')
            ->will($this->returnValue('X'));

        $agreement = $this->getMock('EMRDelegator\Model\Agreement');
        $agreement->expects($this->once())
            ->method('getAgreementType')
            ->will($this->returnValue($agreementType));

        $controller = $this->getControllerMock(array(
            'getAgreement',
            'getLogoutUrl',
            'getToken',
            'getFacilityId',
            'getUserId',
            'getGhostId'));
        $controller->expects($this->once())
            ->method('getUserId')
            ->will($this->returnValue($identityId));
        $controller->expects($this->once())
            ->method('getAgreement')
            ->will($this->returnValue($agreement));
        $controller->expects($this->once())
            ->method('getLogoutUrl')
            ->will($this->returnValue($logoutUrl));
        $controller->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($token));
        $controller->expects($this->once())
            ->method('getFacilityId')
            ->will($this->returnValue($facility));
        $controller->expects($this->once())
            ->method('getGhostId')
            ->will($this->returnValue($ghostId));

        $viewModel = new ViewModel();
        $viewModel->setTemplate('application/agreement/agreement.phtml');
        $viewModel->setVariable('agreement', $agreement);
        $viewModel->setVariable('logoutUrl', $logoutUrl);
        $viewModel->setVariable('token', $token);
        $viewModel->setVariable('facilityId', $facility);
        $viewModel->setVariable('ghostId', $ghostId);

        $result = $controller->displayAction();
        $this->assertEquals($viewModel, $result);
    }

    public function testDisplayActionGetsBaaUserInfo() {

        $logoutUrl = 'http://logout';
        $token = 'biz';
        $facility = 9;
        $identityId = 8;
        $userData = 'user stuff';
        $ghostId = 4;

        $agreementType = $this->getMock('EMRDelegator\Model\AgreementType');
        $agreementType->expects($this->once())
            ->method('getTypeKey')
            ->will($this->returnValue('BAA'));

        $agreement = $this->getMock('EMRDelegator\Model\Agreement');
        $agreement->expects($this->once())
            ->method('getAgreementType')
            ->will($this->returnValue($agreementType));
        $convertedAgreement = 'converted to html';

        $agreementService = $this->getMock('EMRDelegator\Service\Agreement\Agreement');
        $agreementService->expects($this->once())
            ->method('convertAgreementToHtmlFromJson')
            ->with($agreement)
            ->will($this->returnValue($convertedAgreement));
        $agreementService->expects($this->once())
            ->method('getBaaUserInfo')
            ->with($identityId)
            ->will($this->returnValue($userData));

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->once())
            ->method('get')
            ->with('EMRDelegator\Service\Agreement\Agreement')
            ->will($this->returnValue($agreementService));

        $viewModel = $this->getMock('Zend\View\Model\ViewModel');
        $viewModel->expects($this->at(0))
            ->method('setTemplate')
            ->with('application/agreement/baAgreement.phtml');
        $viewModel->expects($this->at(1))
            ->method('setVariable')
            ->with('userData', $userData);
        $viewModel->expects($this->at(2))
            ->method('setVariable')
            ->with('agreement', $convertedAgreement);
        $viewModel->expects($this->at(3))
            ->method('setVariable')
            ->with('logoutUrl', $logoutUrl);
        $viewModel->expects($this->at(4))
            ->method('setVariable')
            ->with('token', $token);
        $viewModel->expects($this->at(5))
            ->method('setVariable')
            ->with('facilityId', $facility);
        $viewModel->expects($this->at(6))
            ->method('setVariable')
            ->with('ghostId', $ghostId);

        $controller = $this->getControllerMock(array(
            'getAgreement',
            'getLogoutUrl',
            'getToken',
            'getFacilityId',
            'getUserId',
            'getViewModel',
            'getGhostId'));
        $controller->expects($this->once())
            ->method('getViewModel')
            ->will($this->returnValue($viewModel));
        $controller->expects($this->once())
            ->method('getUserId')
            ->will($this->returnValue($identityId));
        $controller->expects($this->once())
            ->method('getAgreement')
            ->will($this->returnValue($agreement));
        $controller->expects($this->once())
            ->method('getLogoutUrl')
            ->will($this->returnValue($logoutUrl));
        $controller->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($token));
        $controller->expects($this->once())
            ->method('getFacilityId')
            ->will($this->returnValue($facility));
        $controller->expects($this->once())
            ->method('getGhostId')
            ->will($this->returnValue($ghostId));

        $controller->setServiceLocator($serviceLocator);

        $result = $controller->displayAction();
        $this->assertEquals($viewModel, $result);
    }

    public function testGetAgreementReturnsRouteMatchAgreement() {
        $agreement = 'foo';
        $controller = $this->getControllerMock(array('getAgreementFromRouteMatch'));
        $controller->expects($this->once())
            ->method('getAgreementFromRouteMatch')
            ->will($this->returnValue($agreement));
        $result = Reflection::invoke($controller, 'getAgreement');
        $this->assertEquals($agreement, $result);
    }

    public function testGetAgreementReturnsServiceAgreement() {
        $agreement = 'foo';
        $userId = 42;

        $serviceMock = $this->getMock('EMRDelegator\Service\Agreement\Agreement');
        $serviceMock->expects($this->once())
            ->method('getOutstanding')
            ->with($userId)
            ->will($this->returnValue($agreement));

        $locatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $locatorMock->expects($this->once())
            ->method('get')
            ->with('EMRDelegator\Service\Agreement\Agreement')
            ->will($this->returnValue($serviceMock));

        $controller = $this->getControllerMock(array('getAgreementFromRouteMatch', 'getUserId'));
        $controller->expects($this->once())
            ->method('getAgreementFromRouteMatch');
        $controller->expects($this->once())
            ->method('getUserId')
            ->will($this->returnValue($userId));
        $controller->setServiceLocator($locatorMock);

        $result = Reflection::invoke($controller, 'getAgreement');
        $this->assertEquals($agreement, $result);
    }

    public function testGetSsoUrl() {
        $base = 'http://foo';
        $url = "$base/logout/";

        $config = array('slices' => array('sso' => array('base' => $base)));
        $serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));
        $controller = $this->getControllerMock(array('getUserId'));
        $controller->setServiceLocator($serviceLocatorMock);

        $result = Reflection::invoke($controller, 'getLogoutUrl');
        $this->assertEquals($url, $result);
    }

    public function testGetTokenWrapsGetParamFromAny() {
        $method = 'getToken';
        $name = 'token';
        $test = 'baz';
        $controller = $this->getControllerMock(array('getParamFromAny'));
        $controller->expects($this->once())
            ->method('getParamFromAny')
            ->with($name)
            ->will($this->returnValue($test));
        $result = Reflection::invoke($controller, $method);
        $this->assertEquals($test, $result);
    }

    public function testGetFacilityWrapsGetParamFromAny() {
        $method = 'getFacilityId';
        $name = 'facilityId';
        $test = 'baz';
        $controller = $this->getControllerMock(array('getParamFromAny'));
        $controller->expects($this->once())
            ->method('getParamFromAny')
            ->with($name)
            ->will($this->returnValue($test));
        $result = Reflection::invoke($controller, $method);
        $this->assertEquals($test, $result);
    }

    public function testGetAgreementFromRouteMatch() {
        $test = 'foo';
        $routeMock = $this->getMock('stdClass', array('getParam'));
        $routeMock->expects($this->once())
            ->method('getParam')
            ->with(AgreementController::DISPLAY_AGREEMENT_PARAM_NAME, null)
            ->will($this->returnValue($test));
        $eventMock = $this->getMock('stdClass', array('getRouteMatch'));
        $eventMock->expects($this->once())
            ->method('getRouteMatch')
            ->will($this->returnValue($routeMock));
        $controller = $this->getControllerMock(array('getEvent'));
        $controller->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($eventMock));
        $result = Reflection::invoke($controller, 'getAgreementFromRouteMatch');
        $this->assertEquals($test, $result);
    }

    public function testGetUserId() {
        $userId = 2;
        $sessionMock = $this->getMock('EMRCore\Session\Instance\Authorization');
        $sessionMock->expects($this->once())
            ->method('get')
            ->with('userId')
            ->will($this->returnValue($userId));
        $controller = $this->getControllerMock(array('getLogoutUrl'));
        $controller->setAuthorizationSession($sessionMock);
        $result = Reflection::invoke($controller, 'getUserId');
        $this->assertEquals($userId, $result);
    }

    public function testGetAgreementIdFromRequest() {
        $test = 2;
        $pluginMock = $this->getMock('stdClass', array('fromPost'));
        $pluginMock->expects($this->once())
            ->method('fromPost')
            ->with('agreementId')
            ->will($this->returnValue($test));
        $controller = $this->getControllerMock(array('getParamsPlugin'));
        $controller->expects($this->once())
            ->method('getParamsPlugin')
            ->will($this->returnValue($pluginMock));
        $result = Reflection::invoke($controller, 'getAgreementIdFromRequest');
        $this->assertEquals($test, $result);
    }

    public function testSignAgreementDoesNothing() {
        $locatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $locatorMock->expects($this->never())
            ->method('get');

        $controller = $this->getControllerMock(array('getUserId'));
        $controller->setServiceLocator($locatorMock);
        Reflection::invoke($controller, 'signAgreement', array(0));
    }

    public function testSignAgreementCallsServiceSignMethod() {
        $agreementId = 7;
        $userId = 42;
        $ip = 'ip.address';

        $signData = new SignData();
        $signData->setAgreementId($agreementId);
        $signData->setUserId($userId);
        $signData->setIpAddress($ip);

        $serviceMock = $this->getMock('stdClass', array('sign'));
        $serviceMock->expects($this->once())
            ->method('sign')
            ->with($signData);

        $locatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $locatorMock->expects($this->once())
            ->method('get')
            ->with('EMRDelegator\Service\Agreement\Agreement')
            ->will($this->returnValue($serviceMock));

        $controller = $this->getControllerMock(array('getUserId', 'getRemoteIp'));
        $controller->expects($this->once())
            ->method('getUserId')
            ->will($this->returnValue($userId));
        $controller->expects($this->once())
            ->method('getRemoteIp')
            ->will($this->returnValue($ip));
        $controller->setServiceLocator($locatorMock);

        Reflection::invoke($controller, 'signAgreement', array($agreementId));
    }

    public function testGetRemoteIp() {
        $test = 2;

        $requestMock =$this->getMock('stdClass', array('getServer'));
        $requestMock->expects($this->once())
            ->method('getServer')
            ->with('REMOTE_ADDR')
            ->will($this->returnValue($test));

        $eventMock = $this->getMock('Zend\Mvc\MvcEvent', array('getRequest'));
        $eventMock->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($requestMock));

        $controller = $this->getControllerMock(array('getLogoutUrl'));
        $controller->setEvent($eventMock);
        $result = Reflection::invoke($controller, 'getRemoteIp');
        $this->assertEquals($test, $result);
    }

    public function testForwardToDelegation() {
        $token = 'foo';
        $facility = 'bar';
        $route = 'Application\Controller\Delegation';
        $test = 'biz';
        $ghostId = 0;

        $params = array(
            'action' =>'delegate',
            'token' => $token,
            'facilityId' => $facility,
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

        $result = Reflection::invoke($controller, 'forwardToDelegation', array($token, $facility, $ghostId));
        $this->assertEquals($test,$result);
    }

    public function testGetForwardPluginReturnsForwardPlugin() {
        $test = 'biz';
        $pluginMock = $this->getMock('stdClass', array('get'));
        $pluginMock->expects($this->once())
            ->method('get')
            ->with('forward')
            ->will($this->returnValue($test));
        $controller = $this->getControllerMock(array('getPluginManager'));
        $controller->expects($this->once())
            ->method('getPluginManager')
            ->will($this->returnValue($pluginMock));

        $result = Reflection::invoke($controller, 'getForwardPlugin');
        $this->assertEquals($test, $result);
    }

    public function testGetParamsPluginWrapsPluginManager() {
        $test = 'foo';
        $managerMock = $this->getMock('stdClass', array('get'));
        $managerMock->expects($this->once())
            ->method('get')
            ->with('params')
            ->will($this->returnValue($test));
        $controller = $this->getControllerMock(array('getPluginManager'));
        $controller->expects($this->once())
            ->method('getPluginManager')
            ->will($this->returnValue($managerMock));
        $result = Reflection::invoke($controller, 'getParamsPlugin');
        $this->assertEquals($test, $result);
    }

    public function testGetParamFromAnyChecksRoute() {
        $name = 'foo';
        $test = 'blah';
        $pluginMock = $this->getMock('stdClass', array('fromRoute'));
        $pluginMock->expects($this->once())
            ->method('fromRoute')
            ->with($name, null)
            ->will($this->returnValue($test));
        $controller = $this->getControllerMock(array('getParamsPlugin'));
        $controller->expects($this->once())
            ->method('getParamsPlugin')
            ->will($this->returnValue($pluginMock));
        $result = Reflection::invoke($controller, 'getParamFromAny', array($name));
        $this->assertEquals($test, $result);
    }

    public function testGetParamFromAnyChecksRouteThenChecksPost() {
        $name = 'foo';
        $test = 'blah';
        $pluginMock = $this->getMock('stdClass', array('fromRoute', 'fromPost'));
        $pluginMock->expects($this->once())
            ->method('fromRoute')
            ->with($name, null)
            ->will($this->returnValue(null));
        $pluginMock->expects($this->once())
            ->method('fromPost')
            ->with($name, null)
            ->will($this->returnValue($test));
        $controller = $this->getControllerMock(array('getParamsPlugin'));
        $controller->expects($this->exactly(2))
            ->method('getParamsPlugin')
            ->will($this->returnValue($pluginMock));
        $result = Reflection::invoke($controller, 'getParamFromAny', array($name));
        $this->assertEquals($test, $result);
    }
}

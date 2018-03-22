<?php
/**
 * @category WebPT 
 * @package EMRDelegator
 * @author: kevinkucera
 * 5/24/13 1:05 PM
 */

namespace ServiceTest\Unit;

use EMRCore\Zend\Module\Service\Response\Content;
use EMRCoreTest\Helper\Reflection as Helper;
use Zend\Mvc\Controller\Plugin\Params;

class LogoutControllerTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetSsoTokenThrowsExceptionIfTokenNotPresent()
    {
        $requiredParamName = 'wpt_sso_token';

        $params = $this->getMock('Zend\Mvc\Controller\Plugin\Params');
        $params->expects($this->once())->method('fromQuery')
            ->with($requiredParamName)
            ->will($this->returnValue(null));

        $controllerMock = $this->getMock('\Service\Controller\LogoutController',array('getParamsPlugin'));
        $controllerMock->expects($this->once())->method('getParamsPlugin')
            ->will($this->returnValue($params));

        Helper::invoke($controllerMock,'getSsoToken');
    }

    public function testGetSsoTokenReturnsToken()
    {
        $requiredParamName = 'wpt_sso_token';
        $token = 'foo';

        $params = $this->getMock('Zend\Mvc\Controller\Plugin\Params');
        $params->expects($this->once())->method('fromQuery')
            ->with($requiredParamName)
            ->will($this->returnValue($token));

        $controllerMock = $this->getMock('\Service\Controller\LogoutController',array('getParamsPlugin'));
        $controllerMock->expects($this->once())->method('getParamsPlugin')
            ->will($this->returnValue($params));

        $result = Helper::invoke($controllerMock,'getSsoToken');
        $this->assertEquals($result, $token);
    }

    public function testGetPrepareResponseReturnsContert()
    {
        $contentResponse = new Content();

        $serviceLocator = $this->getMock('\Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->once())->method('get')
            ->with('ServiceResponseContent')
            ->will($this->returnValue($contentResponse));

        $controllerMock = $this->getMock('\Service\Controller\LogoutController',array('getServiceLocator'));
        $controllerMock->expects($this->once())->method('getServiceLocator')
            ->will($this->returnValue($serviceLocator));

        $result = Helper::invoke($controllerMock,'getPreparedResponse');
        $this->assertInstanceOf('\EMRCore\Zend\Module\Service\Response\Content',$result);
    }

    public function testLogoutActionCallsDelete()
    {
        $token = 'abc123';
        $expectedResult = 'response';

        $logoutService = $this->getMock('EMRDelegator\Service\Session\Logout');
        $logoutService->expects($this->once())
            ->method('logout')
            ->with($token);

        $controllerMock = $this->getMock('\Service\Controller\LogoutController',array(
            'getSsoToken',
            'getLogoutService',
            'getPreparedResponse'
        ));
        $controllerMock->expects($this->once())->method('getSsoToken')->will($this->returnValue($token));
        $controllerMock->expects($this->once())->method('getPreparedResponse')->will($this->returnValue($expectedResult));
        $controllerMock->expects($this->once())->method('getLogoutService')->will($this->returnValue($logoutService));


        /** @var \Service\Controller\LogoutController $controllerMock */
        $response = $controllerMock->logoutAction();
        $this->assertEquals($expectedResult, $response);
    }

}
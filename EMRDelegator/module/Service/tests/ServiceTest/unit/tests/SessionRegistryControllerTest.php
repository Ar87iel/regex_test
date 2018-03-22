<?php
/**
 * @category WebPT 
 * @package EMRDelegator
 * @author: kevinkucera
 * 10/28/13 12:41 PM
 */

namespace ServiceTest\Unit;

use Service\Controller\SessionRegistryController;
use EMRCore\Zend\Module\Service\Response\Content;

class SessionRegistryControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject|\EMRDelegator\Service\Session\Registry
     */
    protected function getRegistryServiceMock($methods = array())
    {
        return $this->getMock('EMRDelegator\Service\Session\Registry', $methods);
    }

    /**
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject|\Zend\Mvc\MvcEvent
     */
    protected function getEventMock($methods = array())
    {
        return $this->getMock('Zend\Mvc\MvcEvent', $methods);
    }

    /**
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject|\Zend\Http\Request
     */
    protected function getRequestMock($methods = array())
    {
        return $this->getMock('Zend\Http\Request', $methods);
    }

    public function setUp()
    {

    }

    public function testDeleteSessionCallsDeleteAndReturnsSuccess()
    {
        $ssoToken = 'token-me';
        $tokens = json_encode(array($ssoToken));

        $registryService = $this->getRegistryServiceMock();
        $registryService->expects($this->once())
            ->method('deleteBySsoToken')
            ->with($ssoToken);

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('EMRDelegator\Service\Session\Registry', $registryService),
                array('ServiceResponseContent', new Content)
            )));

        $request = $this->getRequestMock();
        $request->expects($this->once())
            ->method('getPost')
            ->with('wpt_sso_tokens')
            ->will($this->returnValue($tokens));

        $event = $this->getEventMock();
        $event->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $controller = new SessionRegistryController();
        $controller->setServiceLocator($serviceLocator);
        $controller->setEvent($event);

        $result = $controller->deleteSessionsAction();
        $this->assertInstanceOf('EMRCore\Zend\Module\Service\Response\Content', $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDeleteSessionThrowsInvalidArgument()
    {
        $tokens = 'invalid stuff';
        $logger = $this->getMock('Logger',array(), array(), '', false);

        $request = $this->getRequestMock();
        $request->expects($this->once())
            ->method('getPost')
            ->with('wpt_sso_tokens')
            ->will($this->returnValue($tokens));

        $event = $this->getEventMock();
        $event->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $controller = new SessionRegistryController();
        $controller->setLogger($this->getMock('Logger', array(), array(), '', false));
        $controller->setEvent($event);
        $controller->setLogger($logger);

        $result = $controller->deleteSessionsAction();
    }

}
<?php
/**
 * @category WebPT 
 * @package EMRDelegator
 * @author: kevinkucera
 * 10/14/13 9:25 AM
 */

namespace ServiceTest\Unit;


class AgreementControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Service\Controller\AgreementController
     */
    private function getControllerMock($methods = array())
    {
        return $this->getMock('Service\Controller\AgreementController', $methods);
    }

    public function testGet()
    {
        $type = 'type1';
        $agreement = 'agree';
        $expected = 'OK';
        $marshaled = 'marshaled agreement';
        $content = array('agreement' => $marshaled);

        $agreementService = $this->getMock('EMRDelegator\Service\Agreement\Agreement');
        $agreementService->expects($this->once())
            ->method('getLatestAgreementByType')
            ->with($type)
            ->will($this->returnValue($agreement));

        $controller = $this->getControllerMock(array(
            'getAgreementService',
            'getContentResponse',
            'getMarshaledAgreement'
        ));
        $controller->expects($this->once())
            ->method('getAgreementService')
            ->will($this->returnValue($agreementService));
        $controller->expects($this->once())
            ->method('getMarshaledAgreement')
            ->with($agreement)
            ->will($this->returnValue($marshaled));
        $controller->expects($this->once())
            ->method('getContentResponse')
            ->with($content)
            ->will($this->returnValue($expected));

        $result = $controller->get($type);
        $this->assertEquals($expected, $result);
    }
}
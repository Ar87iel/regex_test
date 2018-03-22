<?php
/**
 * @category WebPT 
 * @package EMRAdmin
 * @author: kevinkucera
 * 10/4/13 9:42 AM
 */

namespace EMRAdminTest\unit\tests\Service\Reminder\Dao;

use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\Reminder\Dao\Esb as EsbDao;
use EMRModel\Reminder\Quota as QuotaModel;

class QuotaDaoEsbTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject|\EMRAdmin\Service\Reminder\Dao\Esb
     */
    private function getEsbMock($methods = array())
    {
        return $this->getMock('EMRAdmin\Service\Reminder\Dao\Esb', $methods);
    }

    /**
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject|\EMRCore\Config\Service\PrivateService\Esb\Dto\Route
     */
    private function getRouteMock($methods = array())
    {
        return $this->getMock('EMRCore\Config\Service\PrivateService\Esb\Dto\Route', $methods);
    }

    public function testGetFacilityQuotas()
    {
        $facilityId = 7;
        $uri = 'foo';
        $method = 'get';
        $params = array('facilityId' => $facilityId);
        $payload = 'stuff';

        $response = $this->createMock('EMRCore\Zend\module\Service\src\Response\Dto\Success');
        $response->expects($this->once())
            ->method('getPayload')
            ->will($this->returnValue($payload));

        $route = $this->getRouteMock(array(
            'getUri',
            'getMethod'
        ));
        $route->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue($uri));
        $route->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue($method));

        $esb = $this->getEsbMock(array(
            'getRoute',
            'call'
        ));
        $esb->expects($this->once())
            ->method('getRoute')
            ->with(EsbDao::ROUTE_GET_QUOTAS, EsbDao::ROUTE_NAMESPACE)
            ->will($this->returnValue($route));
        $esb->expects($this->once())
            ->method('call')
            ->with($uri, $method, $params)
            ->will($this->returnValue($response));

        $result = $esb->getFacilityQuotas($facilityId);
        $this->assertEquals($payload, $result);
    }

    public function testSetFacilityQuotas()
    {
        $facilityId = 8;
        $quotaAmount = 100;
        $startDate = '2013-01-01';
        $uri = 'foo';
        $method = 'get';
        $params = array(
            'facilityId' => $facilityId,
            'quota' => $quotaAmount,
            'date' => $startDate,
        );
        $payload = 'stuff';

        $quota = $this->createMock('EMRModel\Reminder\Quota');
        $quota->expects($this->once())
            ->method('getFacilityId')
            ->will($this->returnValue($facilityId));
        $quota->expects($this->once())
            ->method('getQuota')
            ->will($this->returnValue($quotaAmount));
        $quota->expects($this->once())
            ->method('getStartDate')
            ->with('Y-m-d')
            ->will($this->returnValue($startDate));

        $response = $this->createMock('EMRCore\Zend\module\Service\src\Response\Dto\Success');
        $response->expects($this->once())
            ->method('getPayload')
            ->will($this->returnValue($payload));

        $route = $this->getRouteMock(array(
            'getUri',
            'getMethod'
        ));
        $route->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue($uri));
        $route->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue($method));

        $esb = $this->getEsbMock(array(
            'getRoute',
            'call'
        ));
        $esb->expects($this->once())
            ->method('getRoute')
            ->with(EsbDao::ROUTE_SAVE_QUOTA, EsbDao::ROUTE_NAMESPACE)
            ->will($this->returnValue($route));
        $esb->expects($this->once())
            ->method('call')
            ->with($uri, $method, $params)
            ->will($this->returnValue($response));

        $result = $esb->setFacilityQuota($quota);
        $this->assertEquals($payload, $result);
    }

}
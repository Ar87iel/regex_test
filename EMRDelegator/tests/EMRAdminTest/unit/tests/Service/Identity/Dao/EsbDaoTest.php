<?php
namespace EMRAdminTest\unit\tests\Service\Identity\Dao;

use EMRCore\Service\Identity\Dto\SearchCriteria;
use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\Identity\Dao\Esb;

/**
 * @category WebPT
 * @package EMRAdmin
 * @author: kevinkucera
 * 9/30/13 4:08 PM
 */
class EsbDaoTest extends PHPUnit_Framework_TestCase
{

    /**
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject|\EMRAdmin\Service\Identity\Dao\Esb
     */
    private function getEsbDaoMock($methods = array())
    {
        return $this->getMock('EMRAdmin\Service\Identity\Dao\Esb',$methods);
    }

    public function testSearchIdentity()
    {
        $uri = '/qwer/stuff';

        $searchCriteria = new SearchCriteria();
        $marshallerParams = array('asdf');
        $method = 'GET';
        $identities = array(1,2);
        $payload = (object)array('identities'=>$identities);

        $esbResult = $this->createMock('EMRCore\Zend\module\Service\src\Response\Dto\Success');
        $esbResult->expects($this->once())
            ->method('getPayload')
            ->will($this->returnValue($payload));

        $route = $this->createMock('EMRCore\Config\Service\PrivateService\Esb\Dto\Route');
        $route->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue($uri));
        $route->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue($method));

        $marshaller = $this->createMock('EMRCore\Service\Identity\Marshaller\SearchCriteriaToArray');
        $marshaller->expects($this->once())
            ->method('marshall')
            ->with($searchCriteria)
            ->will($this->returnValue($marshallerParams));

        $dao = $this->getEsbDaoMock(array(
            'getRoute',
            'getMarshaller',
            'attachPagination',
            'call',
        ));
        $dao->expects($this->once())
            ->method('getRoute')
            ->with(Esb::ROUTE_IDENTITY_SEARCH)
            ->will($this->returnValue($route));
        $dao->expects($this->once())
            ->method('getMarshaller')
            ->will($this->returnValue($marshaller));
        $dao->expects($this->once())
            ->method('attachPagination')
            ->with($marshallerParams)
            ->will($this->returnValue($marshallerParams));
        $dao->expects($this->once())
            ->method('call')
            ->with($uri, $method, $marshallerParams)
            ->will($this->returnValue($esbResult));

        $result = $dao->searchIdentity($searchCriteria);
        $this->assertEquals($identities, $result);
    }

}
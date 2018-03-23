<?php
/**
 * @category WebPT 
 * @package EMRAdmin
 * @author: kevinkucera
 * 5/31/13 1:40 PM
 */
namespace EMRAdminTest\unit\tests\Service\CompanyMigration\Dao;

use EMRCore\EsbFactory;
use EMRCoreTest\Helper\Reflection as Helper;
use EMRAdmin\Service\CompanyMigration\Dao\Esb as CompanyEsb;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 *
 * @category WebPT
 * @package
 */
class EsbTest extends PHPUnit_Framework_TestCase
{

    public function testCallEsb()
    {

        $uri = 'asdf';
        $method = 'GET';
        $params = array('foo' => 'bar');

        // A fake parser.
        $parser = null;

        // A fake response.
        $esbResponse = json_encode(array(
            'content' => array(
                'response' => array(
                    'stuff' => 'things',
                ),
            ),
        ));

        /** @var ServiceLocatorInterface|PHPUnit_Framework_MockObject_MockObject $serviceLocator */
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        // Stub service locator calls to return the mocks.
        $serviceLocator->expects($this->any())
            ->method('get')
            ->with('EMRCore\Zend\module\Service\src\Response\Parser\Json')
            ->will($this->returnValue($parser));

        // Mock the client wrapper and ensure that execute is called. This is how the ESB request is sent.
        $clientWrapper = $this->getMock('EMRCore\Zend\Http\ClientWrapper');
        $clientWrapper->expects($this->once())
            ->method('setResponseParser')
            ->with($parser);
        $clientWrapper->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($esbResponse));

        /** @var EsbFactory|PHPUnit_Framework_MockObject_MockObject $esbFactory */
        $esbFactory = $this->getMock('EMRCore\EsbFactory', array(), array(), '', false);
        $esbFactory->expects($this->once())
            ->method('getClient')
            ->with($uri, $method, $params)
            ->will($this->returnValue($clientWrapper));

        $companyEsb = new CompanyEsb();
        $companyEsb->setServiceLocator($serviceLocator);
        $companyEsb->setEsbFactory($esbFactory);

        $result = Helper::invoke($companyEsb, 'callEsb', array($uri, $method, $params));

        $this->assertEquals($esbResponse, $result);
    }


    public function testQueueMigration() {
        $uri = '/foo';
        $method = 'GET';
        $companyId = 1;
        $clusterId = 2;
        $identityId = 3;
        $params = array('companyId' => $companyId, 'clusterId' => $clusterId, 'identityId' => $identityId);
        $response = 'foo';
        $test = 'blah';

        $routeMock = $this->getMock('stdClass', array('getUri', 'getMethod'));
        $routeMock->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue($uri));
        $routeMock->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue($method));

        $marshallerMock = $this->getMock('stdClass', array('marshall'));
        $marshallerMock->expects($this->once())
            ->method('marshall')
            ->with($response)
            ->will($this->returnValue($test));

        $locatorMock = $this->getMock('stdClass', array('get'));
        $locatorMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($marshallerMock));

        $esbMock = $this->getMock('EMRAdmin\Service\CompanyMigration\Dao\Esb',
            array('getRoute', 'validateCompanyId', 'validateClusterId', 'validateIdentityId', 'callEsb', 'getServiceLocator'));
        $esbMock->expects($this->once())
            ->method('getRoute')
            ->with(CompanyEsb::ROUTE_QUEUE_MIGRATION)
            ->will($this->returnValue($routeMock));
        $esbMock->expects($this->once())
            ->method('validateCompanyId')
            ->will($this->returnValue($companyId));
        $esbMock->expects($this->once())
            ->method('validateClusterId')
            ->will($this->returnValue($clusterId));
        $esbMock->expects($this->once())
            ->method('validateIdentityId')
            ->will($this->returnValue($identityId));
        $esbMock->expects($this->once())
            ->method('callEsb')
            ->with($uri, $method, $params)
            ->will($this->returnValue($response));
        $esbMock->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($locatorMock));

        /** @var CompanyEsb $esbMock */
        $result = $esbMock->queueMigration($companyId, $clusterId, $identityId);
        $this->assertEquals($test, $result);
    }

}
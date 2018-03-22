<?php

namespace EMRAdminTest\unit\tests\Service\Cluster\Marshaller;

use EMRAdmin\Service\Cluster\Dto\Cluster;
use EMRAdmin\Service\Cluster\Marshaller\SuccessToSaveClusterResponse;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use PHPUnit_Framework_TestCase;
use stdClass;

class SuccessToSaveClusterResponseTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SuccessToSaveClusterResponse
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new SuccessToSaveClusterResponse;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidItemType()
    {
        $this->marshaller->marshall(new stdClass);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToMissingClusterData()
    {
        $success = new Success;
        $success->setPayload((object) array(
                    'cluster' => array(),
        ));

        $this->marshaller->marshall($success);
    }

    public function testMarshalsSaveClusterResponse()
    {
        $marshaller = $this->getMock('EMRAdmin\Service\Cluster\Marshaller\SuccessToSaveClusterResponse', array(
            'setClusterInformation',
        ));

        $marshaller->expects($this->once())->method('setClusterInformation')->withAnyParameters();

        $serviceLocator = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');

        $serviceLocator->expects($this->once())->method('get')
                ->with($this->equalTo('EMRAdmin\Service\Cluster\Dto\Cluster'))
                ->will($this->returnValue(new Cluster));

        /** @var SuccessToSaveClusterResponse $marshaller */
        $marshaller->setServiceLocator($serviceLocator);

        $success = new Success();
        $success->setPayload((object) array(
                    'cluster' => (object) array(
                        'stuff' => 'things',
                    ),
        ));

        $response = $marshaller->marshall($success);

        $this->assertInstanceOf('EMRAdmin\Service\Cluster\Dto\Cluster', $response);
    }

}
<?php

namespace EMRAdminTest\unit\tests\Service\Cluster\Marshaller;

use EMRAdmin\Service\Cluster\Dto\Cluster;
use EMRAdmin\Service\Cluster\Dto\ClusterCollection;
use EMRAdmin\Service\Cluster\Marshaller\ClusterCollectionToArray;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use stdClass;

class ClusterCollectionToArrayTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $serviceLocator;

    /**
     * @var ClusterCollectionToArray
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new ClusterCollectionToArray;

        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $this->marshaller->setServiceLocator($this->serviceLocator);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidItemType()
    {
        $this->marshaller->marshall(new stdClass);
    }

    public function testMarshalsCollection()
    {
        $cluster = new Cluster;

        $collection = new ClusterCollection;
        $collection->push($cluster);

        $clusterMarshaller = $this->getMock('EMRAdmin\Service\Cluster\Marshaller\ClusterToArray');

        $clusterMarshaller->expects($this->once())->method('marshall')
                ->with($this->equalTo($cluster))->will($this->returnValue($cluster));

        $this->serviceLocator->expects($this->once())->method('get')
                ->with($this->equalTo('EMRAdmin\Service\Cluster\Marshaller\ClusterToArray'))
                ->will($this->returnValue($clusterMarshaller));

        $marshalled = $this->marshaller->marshall($collection);

        $this->assertCount(1, $marshalled);
        $this->assertSame($cluster, array_shift($marshalled));
    }

}
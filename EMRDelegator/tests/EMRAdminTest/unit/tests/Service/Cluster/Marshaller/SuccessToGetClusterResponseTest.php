<?php
namespace EMRAdminTest\unit\tests\Service\Cluster\Marshaller;

use EMRAdmin\Service\Cluster\Dto\Cluster;
use EMRAdmin\Service\Cluster\Marshaller\SuccessToGetClusterResponse;
use EMRCore\PrototypeFactory;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use EMRCoreTest\Helper\Reflection;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class SuccessToGetClusterResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalPayloadToClusterDueToInvalidPayloadType()
    {
        $marshaller = new SuccessToGetClusterResponse;

        Reflection::invoke($marshaller, 'marshalPayloadToCluster', array(new Cluster, array()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidResponseType()
    {
        $marshaller = new SuccessToGetClusterResponse;

        $marshaller->marshall(array());
    }

    /**
     * This test proves that with the correct payload type an internal marshal function helper is called
     * with the expected parameters.
     */
    public function testMarshalsResponse()
    {
        $payload = (object) array(
            'cluster' => (object) array(),
        );

        $success = new Success;
        $success->setPayload($payload);

        $cluster = new Cluster;

        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $prototypeFactory->expects($this->once())
            ->method('createAndInitialize')
            ->with($this->equalTo('EMRAdmin\Service\Cluster\Dto\Cluster'))
            ->will($this->returnValue($cluster));

        $marshaller = $this->getMock('EMRAdmin\Service\Cluster\Marshaller\SuccessToGetClusterResponse', array(
            'marshalPayloadToCluster',
        ));

        $marshaller->expects($this->once())
            ->method('marshalPayloadToCluster')
            ->with($this->equalTo($cluster), $this->equalTo($payload->cluster));

        /** @var SuccessToGetClusterResponse $marshaller */
        $marshaller->setPrototypeFactory($prototypeFactory);

        $marshaller->marshall($success);
    }
}
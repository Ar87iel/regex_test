<?php

namespace EMRAdminTest\unit\tests\Service\Cluster\Marshaller;

use EMRAdmin\Service\Cluster\Dto\Cluster;
use EMRAdmin\Service\Cluster\Dto\ClusterCollection;
use EMRAdmin\Service\Cluster\Marshaller\SuccessToGetListResponse;
use EMRCore\PrototypeFactory;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use EMRCoreTest\lib\PhpUnit\SingletonTestCaseHelper;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use stdClass;

class SuccessToGetListResponseTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SuccessToGetListResponse
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new SuccessToGetListResponse;
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
    public function testNotMarshalsDueToClustersPayloadIsMissing()
    {
        $success = new Success;
        $success->setPayload(array());
        $this->marshaller->marshall($success);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToClustersPayloadIsNotAnArray()
    {
        $success = new Success;
        $success->setPayload(array(
            'clusters' => (object) array(
                'clusterId' => 1,
            ),
        ));
        $this->marshaller->marshall($success);
    }

    /**
     * @param int $id
     * @param string $name
     * @param int $facilityMax
     * @param int $facilityCurrent
     * @param bool $acceptingNewCompanies
     * @param string $onlineStatus
     * @param string $comment
     * @return mixed[]
     */
    private function createClusterRecord($id, $name = null, $facilityMax = null, $facilityCurrent = null, $acceptingNewCompanies = null, $onlineStatus = null, $comment = null, $createdAt = null, $lastModified = null )
    {
        return (object) array(
                    'clusterId' => $id,
                    'clusterName' => $name,
                    'facilityMax' => $facilityMax,
                    'facilityCurrent' => $facilityCurrent,
                    'acceptingNewCompanies' => $acceptingNewCompanies,
                    'onlineStatus' => $onlineStatus,
                    'comment' => $comment,
                    'createdAt' => $createdAt,
                    'lastModified' => $lastModified,
        );
    }

    public function testMarshalsGetListResponse()
    {
        $clusterId1 = 1;
        $clusterId2 = 2;

        $payload = (object) array(
            'clusters' => array(
                $this->createClusterRecord($clusterId1),
                $this->createClusterRecord($clusterId2),
            ),
        );

        $success = new Success;
        $success->setPayload($payload);

        // Mock the prototype factory so that the marshaller can create new instances for the marshalled collection.
        $prototypeFactoryClass = 'EMRCore\PrototypeFactory';
        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMock($prototypeFactoryClass, array(), array(), '', false);

        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name)
                {
                    if ($name === 'EMRAdmin\Service\Cluster\Dto\ClusterCollection')
                    {
                        return new ClusterCollection;
                    }

                    if ($name === 'EMRAdmin\Service\Cluster\Dto\Cluster')
                    {
                        return new Cluster;
                    }

                    throw new InvalidArgumentException("Mocked prototypeFactory cannot create name [$name].");
                }));

        $this->marshaller->setPrototypeFactory($prototypeFactory);

        $response = $this->marshaller->marshall($success);

        $this->assertInstanceOf('EMRAdmin\Service\Cluster\Dto\ClusterCollection', $response);

        $expected = array(
            $clusterId1,
            $clusterId2,
        );

        $this->assertSame($expected, $response->pluck('id')->toArray());
    }

}
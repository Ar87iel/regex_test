<?php

namespace EMRAdminTest\unit\tests\Service\Cluster\Marshaller;

use EMRAdmin\Service\Cluster\Dto\Cluster;
use EMRAdmin\Service\Cluster\Marshaller\SaveClusterRequestToArray;
use PHPUnit_Framework_TestCase;

class SaveClusterRequestToArrayTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Cluster
     */
    private $request;

    public function setUp()
    {
        $this->request = new Cluster();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidItemType()
    {
        $marshaller = new SaveClusterRequestToArray;
        $marshaller->marshall(array());
    }

    public function testMarshalGetsArray()
    {
        $marshaller = new SaveClusterRequestToArray;

        /** @var SaveClusterRequestToArray $marshaller */
        $response = $marshaller->marshall($this->request);
        $this->assertInternalType('array', $response);
    }

}
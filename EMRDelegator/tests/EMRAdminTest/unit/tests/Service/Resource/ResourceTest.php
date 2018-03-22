<?php

/**
 * Test Resource Business service.
 * @category WebPT
 * @package EMRAdmin
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRAdminTest\unit\tests\Service\Resource;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\Resource\Resource as ResourceService;
use EMRAdmin\Service\Resource\Dao\Resource as ResourceDao;
use EMRModel\Resource\Resource as ResourceModel;

class ResourceTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ResourceService $resourceService
     */
    private $resourceService;

    public function setUp ()
    {

        $this->resourceService = new ResourceService;
    }

    public function testGetList ()
    {

        //A fake list of resource
        $resources = array(
            new ResourceModel,
            new ResourceModel,
        );

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $daoMock
         */
        $daoMock = $this->getMock("EMRAdmin\Service\Resource\Dao\Resource", array("getAll"));
        $daoMock->expects($this->once())->method("getAll")->will($this->returnValue($resources));

        $this->resourceService->setResourceDao($daoMock);
        $response = $this->resourceService->getList();

        $this->assertTrue(is_array($response));
        $this->assertInstanceOf("EMRModel\Resource\Resource", $response[0]);
    }
}
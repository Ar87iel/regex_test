<?php

/**
 * Test Resource Dao.
 * @category WebPT
 * @package EMRAdmin
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRAdminTest\unit\tests\Service\Resource\Dao;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\Resource\Dao\Resource as ResourceDao;
use EMRModel\Resource\Resource as ResourceModel;

class ResourceTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ResourceDao $dao
     */
    private $dao;

    /**
     * Set up the test.
     */
    public function setUp ()
    {

        $this->dao = new ResourceDao;

    }

    /**
     * Test get a list of resources.

     */
    public function testGetAll ()
    {

        //A fake list of resources
        $resources = array(
            new ResourceModel(),
            new ResourceModel(),
        );

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $repository
         */
        $respository = $this->getMock('EMRModel\Resource\Resource', array('findAll'));
        $respository->expects($this->once())->method('findAll')->will($this->returnValue($resources));

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $entityManager
         */
        $entityManager = $this->getMock('\Doctrine\ORM\EntityManager', array('getRepository'), array(), '', false);
        $entityManager->expects($this->once())->method('getRepository')->with('EMRModel\Resource\Resource')->will($this->returnValue($respository));

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $defaultReaderWriter
         */
        $defaultReaderWriter = $this->getMock('\EMRCore\DoctrineConnector\Adapter\Adapter', array('getEntityManager'));
        $defaultReaderWriter->expects($this->once())->method('getEntityManager')->withAnyParameters()->will($this->returnValue($entityManager));

        $this->dao->setDefaultMasterSlave($defaultReaderWriter);

        $response = $this->dao->getAll();

        $this->assertTrue(is_array($response));
        $this->assertInstanceOf("EMRModel\Resource\Resource", $response[0]);
    }
}
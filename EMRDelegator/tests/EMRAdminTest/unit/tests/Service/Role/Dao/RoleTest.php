<?php

/**
 * Test Resource Dao.
 * @category WebPT
 * @package EMRAdmin
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRAdminTest\unit\tests\Service\Role\Dao;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\Role\Dao\Role as RoleDao;
use EMRModel\Role\Role as RoleModel;
use Doctrine\Common\Collections\ArrayCollection;
use EMRModel\User\RoleResource as RoleResourceModel;

class RoleTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var RoleDao $dao
     */
    private $dao;

    /**
     * Set up the test.
     */
    public function setUp ()
    {

        $this->dao = new RoleDao;

    }

    /**
     * Load a Role.
     */
    public function testLoadRole ()
    {

        $id = 1;

        $criteria = array('id' => $id);

        $role = new RoleModel();
        $role->setName("asd");
        $role->setDescription("asdasd");


        /**
         * @var PHPUnit_Framework_MockObject_MockObject $repository
         */
        $respository = $this->getMock('EMRModel\Role\Role', array('findOneBy'));
        $respository->expects($this->once())->method('findOneBy')->with($this->equalTo($criteria))->will($this->returnValue($role));

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $entityManager
         */
        $entityManager = $this->getMock('\Doctrine\ORM\EntityManager', array('getRepository'), array(), '', false);
        $entityManager->expects($this->once())->method('getRepository')->with('EMRModel\Role\Role')->will($this->returnValue($respository));

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $defaultReaderWriter
         */
        $defaultReaderWriter = $this->getMock('\EMRCore\DoctrineConnector\Adapter\Adapter', array('getEntityManager'));
        $defaultReaderWriter->expects($this->once())->method('getEntityManager')->withAnyParameters()->will($this->returnValue($entityManager));

        $this->dao->setDefaultMasterSlave($defaultReaderWriter);

        $role = $this->dao->loadRole($id);
    }

    /**
     * Save a Role.
     */
    public function testSaveRole ()
    {

        $role = new RoleModel;
        $role->setName("asd");
        $role->setDescription("asdasd");
        $role->setModifyResource("asd");
        $role->setUseResource("asd");

        $resource = new RoleResourceModel;
        $resource->setResourceId("asd");
        $resource->setRole($role);

        $resources = new ArrayCollection();
        $resources->add($resource);

        $role->setResources($resources);


        $roleMerged = new RoleModel;
        $roleMerged->setName("asd");
        $roleMerged->setDescription("asdasd");
        $roleMerged->setModifyResource("asd");
        $roleMerged->setUseResource("asd");
        $roleMerged->setResources($resources);
        $roleMerged->setId(1);

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $entityManager
         */
        $entityManager = $this->getMock('\Doctrine\ORM\EntityManager', array(
                                                                            'persist',
                                                                            'flush',
                                                                            'merge',
                                                                            'contains',
                                                                            'remove'
                                                                       ), array(), '', false);
        $entityManager->expects($this->any())->method('flush')->will($this->returnValue($role));
        $entityManager->expects($this->any())->method('merge')->will($this->returnValue($roleMerged));

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $defaultReaderWriter
         */
        $defaultReaderWriter = $this->getMock('\EMRCore\DoctrineConnector\Adapter\Adapter', array('getEntityManager'));
        $defaultReaderWriter->expects($this->any())->method('getEntityManager')->withAnyParameters()->will($this->returnValue($entityManager));

        $this->dao->setDefaultMasterSlave($defaultReaderWriter);

        $this->dao->saveRole($role);
    }

    /**
     * Test Get list of roles with associated resources.
     */
    public function testGetList ()
    {

        //A fake role.
        $role = new RoleModel;
        $role->setName("asd");
        $role->setDescription("asdasd");
        $role->setModifyResource("asd");
        $role->setUseResource("asd");

        $name = "asd";
        $resource = new RoleResourceModel;
        $resource->setResourceId("asd");
        $resource->setRole($role);

        $resources = new ArrayCollection();
        $resources->add($resource);

        $role->setResources($resources);

        $roles = array($role);

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $repository
         */
        $repository = $this->getMock('EMRModel\Role\Role', array('searchByName'));
        $repository->expects($this->once())->method('searchByName')->will($this->returnValue($roles));

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $entityManager
         */
        $entityManager = $this->getMock('\Doctrine\ORM\EntityManager', array('getRepository'), array(), '', false);
        $entityManager->expects($this->once())->method('getRepository')->with('EMRModel\Role\Role')->will($this->returnValue($repository));

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $defaultReaderWriter
         */
        $defaultReaderWriter = $this->getMock('\EMRCore\DoctrineConnector\Adapter\Adapter', array('getEntityManager'));
        $defaultReaderWriter->expects($this->any())->method('getEntityManager')->withAnyParameters()->will($this->returnValue($entityManager));

        $this->dao->setDefaultMasterSlave($defaultReaderWriter);

        $this->dao->searchByName($name);
    }

    /**
     * Test delete a role from the database.
     */
    public function testDelete ()
    {

        //A fake role.
        $role = new RoleModel;
        $role->setName("asd");
        $role->setDescription("asdasd");
        $role->setModifyResource("asd");
        $role->setUseResource("asd");

        $resource = new RoleResourceModel;
        $resource->setResourceId("asd");
        $resource->setRole($role);

        $resources = new ArrayCollection();
        $resources->add($resource);

        $role->setResources($resources);

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $entityManager
         */
        $entityManager = $this->getMock('\Doctrine\ORM\EntityManager', array('remove', 'flush'), array(), '', false);
        $entityManager->expects($this->any())->method('flush')->will($this->returnValue($role));

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $defaultReaderWriter
         */
        $defaultReaderWriter = $this->getMock('\EMRCore\DoctrineConnector\Adapter\Adapter', array('getEntityManager'));
        $defaultReaderWriter->expects($this->any())->method('getEntityManager')->withAnyParameters()->will($this->returnValue($entityManager));

        $this->dao->setDefaultMasterSlave($defaultReaderWriter);

        $this->dao->delete($role);
    }


}
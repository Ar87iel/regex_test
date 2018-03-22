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
use EMRAdmin\Service\User\Dao\UserRole as UserRoleDao;
use EMRModel\User\UserRole as UserRoleModel;
use EMRModel\Role\Role as RoleModel;

class UserRoleTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var UserRoleDao $dao
     */
    private $dao;

    /**
     * Set up the test.
     */
    public function setUp ()
    {

        $this->dao = new UserRoleDao;

    }


    /**
     * Test delete a UserRole relation from the database.
     */
    public function testDelete ()
    {

        $userRole = new UserRoleModel;

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $entityManager
         */
        $entityManager = $this->getMock('\Doctrine\ORM\EntityManager', array('remove', 'flush'), array(), '', false);
        $entityManager->expects($this->any())->method('flush')->will($this->returnValue($userRole));

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $defaultReaderWriter
         */
        $defaultReaderWriter = $this->getMock('\EMRCore\DoctrineConnector\Adapter\Adapter', array('getEntityManager'));
        $defaultReaderWriter->expects($this->any())->method('getEntityManager')->withAnyParameters()->will($this->returnValue($entityManager));

        $this->dao->setDefaultMasterSlave($defaultReaderWriter);

        $this->dao->delete($userRole);
    }

    /**
     * Test get a user role by Role.
     */
    public function testGetUserRoleByRole ()
    {

        $role = new RoleModel();
        $role->setName("asd");
        $role->setDescription("asdasd");

        $userRole = new UserRoleModel;
        $userRole->setRole($role);

        $userRoles = array($userRole);

        $criteria = array('role' => $role);


        /**
         * @var PHPUnit_Framework_MockObject_MockObject $repository
         */
        $repository = $this->getMock('EMRModel\User\UserRole', array('findBy'));
        $repository->expects($this->once())->method('findBy')->with($this->equalTo($criteria))->will($this->returnValue($userRoles));

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $entityManager
         */
        $entityManager = $this->getMock('\Doctrine\ORM\EntityManager', array('getRepository'), array(), '', false);
        $entityManager->expects($this->once())->method('getRepository')->with('EMRModel\User\UserRole')->will($this->returnValue($repository));

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $defaultReaderWriter
         */
        $defaultReaderWriter = $this->getMock('\EMRCore\DoctrineConnector\Adapter\Adapter', array('getEntityManager'));
        $defaultReaderWriter->expects($this->once())->method('getEntityManager')->withAnyParameters()->will($this->returnValue($entityManager));

        $this->dao->setDefaultMasterSlave($defaultReaderWriter);

        $this->dao->getUserRoleByRoleModel($role);
    }


}
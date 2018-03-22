<?php

namespace EMRAdminTest\integration\tests\Service\User\Dao;

use EMRAdmin\Service\User\Dao\UserRole as UserRoleDao;
use EMRCoreTest\Helper\Reflection;
use EMRModel\User\UserRole as UserRoleModel;
use EMRModel\Role\Role;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class UserRoleTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $readerWriter;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManager;

    /**
     * @var UserDao
     */
    private $dao;

    public function setUp()
    {
        $this->repository = $this->getMock('Doctrine\ORM\EntityRepository', array(), array(), '', false);

        $this->entityManager = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);

        $this->entityManager->expects($this->any())->method('merge')
                ->will($this->returnCallback(function($entity)
                                {
                                    return $entity;
                                }));

        $this->readerWriter = $this->getMock('EMRCore\DoctrineConnector\Adapter\Adapter');

        $this->readerWriter->expects($this->any())->method('getEntityManager')
                ->will($this->returnValue($this->entityManager));

        $this->dao = new UserRoleDao();
    }

    public function testCreateSchedulerAcl()
    {
        $roleModel = new Role;

        $sched = new UserRoleModel();
        $sched->setUserId(2);
        $sched->setRole($roleModel);

        $this->dao->setDefaultMasterSlave($this->readerWriter);
        $this->dao->saveUserRole($sched);
    }
}

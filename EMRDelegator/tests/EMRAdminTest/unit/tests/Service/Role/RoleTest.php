<?php

/**
 * Test Resource Business service.
 * @category WebPT
 * @package EMRAdmin
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRAdminTest\unit\tests\Service\Role;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Collections\ArrayCollection;
use EMRAdmin\Service\Role\Role as RoleService;
use EMRCore\Service\Entity\ChangeEvent;
use EMRModel\Role\Role as RoleModel;
use EMRModel\User\RoleResource as RoleResourceModel;
use EMRModel\User\UserRole as UserRoleModel;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class RoleTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var RoleService $roleService
     */
    private $roleService;

    /**
     * Set the environment for the tests
     */
    public function setUp ()
    {

        $this->roleService = new RoleService;
    }

    /**
     * Test Get a Role Service.
     */
    public function testGetRole ()
    {

        $id = 1;

        //A fake Role Model
        $role = new RoleModel;
        $role->setName('asd');
        $role->setDescription('asdasd');
        $role->setModifyResource('asd');
        $role->setUseResource('asd');

        $resource = new RoleResourceModel;
        $resource->setResourceId('asd');
        $resource->setRole($role);

        $resources = new ArrayCollection();
        $resources->add($resource);

        $role->setResources($resources);

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $daoMock
         */
        $daoMock = $this->getMock('EMRAdmin\Service\Role\Dao\Role', array('loadRole'));
        $daoMock->expects($this->once())->method('loadRole')->with($this->equalTo($id))->will($this->returnValue($role));

        $this->roleService->setRoleDao($daoMock);

        $response = $this->roleService->get($id);
        $this->assertInstanceOf('EMRModel\Role\Role', $response);
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $response->getResources());
    }

    /**
     * Test Save a Role on the Business Service.
     */
    public function testSave ()
    {

        //A fake Role Model
        $role = new RoleModel;
        $role->setName('asd');
        $role->setDescription('asdasd');
        $role->setModifyResource('asd');
        $role->setUseResource('asd');

        $resource = new RoleResourceModel;
        $resource->setResourceId('asd');
        $resource->setRole($role);

        $resources = new ArrayCollection();
        $resources->add($resource);

        $role->setResources($resources);

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $daoMock
         */
        $daoMock = $this->getMock('EMRAdmin\Service\Role\Dao\Role', array('saveRole'));
        $daoMock->expects($this->once())->method('saveRole')->with($this->equalTo($role))->will($this->returnValue($role));

        $changeEvent = $this->getMock('EMRCore\Service\Entity\ChangeEvent');
        $changeEvent->expects($this->once())->method('sendForEntityFromAdmin');

        $userRoleModelTest = new UserRoleModel();
        $userRoleModelTest->setUserId(1);

        $roleServiceMock = $this->getMock('EMRAdmin\Service\User\UserRole');
        $roleServiceMock->expects($this->once())->method('getUserRoleByRole')->will($this->returnValue(array($userRoleModelTest)));

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->any())->method('get')->will($this->returnCallback(function ($name)
        use ($changeEvent, $roleServiceMock)
        {

            if ($name == 'EMRCore\Service\Entity\ChangeEvent')
            {
                return $changeEvent;
            } elseif ($name == 'EMRAdmin\Service\User\UserRole')
            {
                return $roleServiceMock;
            }

            throw new InvalidArgumentException("Mocked ServiceManager cannot create name ['$name'].");


        }));
        $this->roleService->setServiceLocator($serviceLocator);

        $this->roleService->setRoleDao($daoMock);

        $this->roleService->save($role);
    }

    /**
     * Test get a list of role service.
     */
    public function testGetByName()
    {
        $name = 'asd';

        //A fake Role Model
        $role = new RoleModel;
        $role->setName('asd');
        $role->setDescription('asdasd');
        $role->setModifyResource('asd');
        $role->setUseResource('asd');

        $resource = new RoleResourceModel;
        $resource->setResourceId('asd');
        $resource->setRole($role);

        $resources = new ArrayCollection();
        $resources->add($resource);

        $role->setResources($resources);

        $roles = array($role);
        /**
         * @var PHPUnit_Framework_MockObject_MockObject $daoMock
         */
        $daoMock = $this->getMock('EMRAdmin\Service\Role\Dao\Role', array('searchByName'));
        $daoMock->expects($this->once())->method('searchByName')->will($this->returnValue($roles));

        $this->roleService->setRoleDao($daoMock);

        $this->roleService->getByName($name);
    }

    /**
     * Test get a list of role service.
     */
    public function testGetList ()
    {
        //A fake Role Model
        $role = new RoleModel;
        $role->setName('asd');
        $role->setDescription('asdasd');
        $role->setModifyResource('asd');
        $role->setUseResource('asd');

        $resource = new RoleResourceModel;
        $resource->setResourceId('asd');
        $resource->setRole($role);

        $resources = new ArrayCollection();
        $resources->add($resource);

        $role->setResources($resources);

        $roles = array($role);
        /**
         * @var PHPUnit_Framework_MockObject_MockObject $daoMock
         */
        $daoMock = $this->getMock('EMRAdmin\Service\Role\Dao\Role', array('getList'));
        $daoMock->expects($this->once())->method('getList')->will($this->returnValue($roles));

        $this->roleService->setRoleDao($daoMock);

        $this->roleService->getList();
    }

    /**
     * Test send a request to the DAO to delete a ROLE that doesn't exist.
     * @expectedException \EMRAdmin\Service\Role\Exception\RoleNotFound
     */
    public function testDeleteRoleNotFound ()
    {

        $id = 1;

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $daoMock
         */
        $daoMock = $this->getMock('EMRAdmin\Service\Role\Dao\Role', array('loadRole'));
        $daoMock->expects($this->once())->method('loadRole')->with($this->equalTo($id))->will($this->returnValue(null));

        $this->roleService->setRoleDao($daoMock);

        $this->roleService->delete($id);
    }


    /**
     * Test send a request to the DAO to delete a ROLE
     */
    public function testDelete ()
    {

        $id = 1;


        //A fake Role Model
        $role = new RoleModel;
        $role->setName('asd');
        $role->setDescription('asdasd');
        $role->setModifyResource('asd');
        $role->setUseResource('asd');

        $resource = new RoleResourceModel;
        $resource->setResourceId('asd');
        $resource->setRole($role);

        $resources = new ArrayCollection();
        $resources->add($resource);

        $role->setResources($resources);

        $userRole = new UserRoleModel();
        $userRole->setRole($role);
        $userRole->setUserId(1);

        $userRoles = array($userRole);

        /**
         * @var PHPUnit_Framework_MockObject_MockObject $daoMock
         */
        $daoMock = $this->getMock('EMRAdmin\Service\Role\Dao\Role', array('loadRole', 'delete'));
        $daoMock->expects($this->once())->method('loadRole')->with($this->equalTo($id))->will($this->returnValue($role));
        $daoMock->expects($this->once())->method('delete')->with($this->equalTo($role))->will($this->returnValue($role));

        $userRoleService = $this->getMock('EMRAdmin\Service\User\UserRole');
        $userRoleService->expects($this->once())->method('getUserRoleByRole')->with($this->equalTo($role))->will($this->returnValue($userRoles));

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->any())->method('get')->will($this->returnCallback(function ($name) use ($userRoleService)
        {

            if ($name == 'EMRAdmin\Service\User\UserRole')
            {
                return $userRoleService;
            }

            throw new InvalidArgumentException('Mocked ServiceManager cannot create name [$name].');


        }));
        $this->roleService->setServiceLocator($serviceLocator);
        $this->roleService->setRoleDao($daoMock);

        $this->roleService->delete($id);
    }

}
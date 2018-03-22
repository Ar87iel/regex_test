<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alexosuna
 * Date: 9/5/13
 * Time: 8:23 AM
 * To change this template use File | Settings | File Templates.
 */

namespace EMRAdminTest\unit\tests\Service\User;

use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\User\UserRole as UserRoleService;
use EMRAdmin\Service\User\Dto\UserRoleCollection;
use EMRModel\User\UserRole as UserRoleModel;
use EMRModel\Role\Role as RoleModel;
use EMRAdmin\Service\User\Dao\UserRole as DaoUserRole;


class UserRoleTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var PrototypeFactory
     */
    private $prototypeFactory;

    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    private $DaoUserRole;

    function setUp()
    {
        $this->serviceLocator = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);
        $this->DaoUserRole = $this->createMock("EMRAdmin\Service\User\Dao\UserRole");
    }

    /**
     * test save user role follows the correct work flow when is invoked
     */
    public function testSaveUserRole()
    {
        $roleModel = new RoleModel;

        $roleModel->setId(2);
        $roleModel->setName('Role Model');

        $userRoleModel = new UserRoleModel;
        $userRoleModel->setUserId(2);
        $userRoleModel->setRole($roleModel);

        $userRolesArray = array(
            $userRoleModel,
        );

        $userRoleCollection = new UserRoleCollection;
        $userRoleCollection->setUserRoleCollection($userRolesArray);

        $this->DaoUserRole->expects($this->once())->method("getUserRoleById")->will($this->returnValue($userRolesArray));

        $this->DaoUserRole->expects($this->once())->method("saveUserRole")->will($this->returnValue($userRoleModel));

        $this->DaoUserRole->expects($this->any())->method("delete")->will($this->returnValue(true));

        $ServiceRole = $this->createMock("EMRAdmin\Service\Role\Role");
        $ServiceRole->expects($this->once())->method("get")->will($this->returnValue($roleModel));
        
        $eventService = $this->createMock('EMRCore\Service\Entity\ChangeEvent');
        $eventService->expects($this->once())->method('sendForEntityFromAdmin')->will($this->returnValue(true));

        $this->serviceLocator->expects($this->any())->method('get')
            ->will($this->returnCallback(function ($name) use ($ServiceRole, $eventService){
                switch($name)
                {
                    case "EMRAdmin\Service\Role\Role":
                        return $ServiceRole;
                        break;
                    case "EMRCore\Service\Entity\ChangeEvent":
                        return $eventService;
                        break;
                    default:
                        throw new InvalidArgumentException("Mocked ServiceManager cannot create name [$name].");
                }
            }));

        $userRoleService = new UserRoleService;
        $userRoleService->setServiceLocator($this->serviceLocator);
        $userRoleService->setEsb($this->DaoUserRole);

        $response = $userRoleService->saveUserRole($userRoleCollection);

        $this->assertTrue(is_array($response));
        $this->assertInstanceOf('EMRModel\User\UserRole', $response[0]);
    }

    /**
     * Proves that the getUserRoleList will call the getUserRoleList() method from the Dao
     */
    public function testGetUserRoleList()
    {

        $this->DaoUserRole->expects($this->once())->method('getUserRoleList');
        $userRoleService = new UserRoleService();
        $userRoleService->setEsb($this->DaoUserRole);

        $userRoleService->getUserRoleList();
    }


}
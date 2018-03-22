<?php
namespace EMRAdminTest\unit\tests\Service\User;

use EMRAdmin\Service\User\Dto\Permission;
use EMRAdmin\Service\User\Dto\PermissionCollection;
use EMRAdmin\Service\User\Permission as PermissionService;
use EMRModel\Facility\Module as Module;
use EMRAdmin\Service\Facility\FacilityHasModules\Dto\FacilityHasModules as FacilityHasModulesDto;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

/**
 * Class PermissionTest
 * @package EMRAdminTest\unit\tests\Service\User
 * @group UserPermissions
 */
class PermissionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests that the PermissionService returns the correct
     * list of Permissions
     */
    public function testGetsList()
    {

        $service = $this->getPermissionService();
        $collection = $service->getList();
        $this->assertInstanceOf('EMRAdmin\Service\User\Dto\PermissionCollection', $collection);

        $expected = $this->getAllPermissionsNames();

        $this->assertSame($expected, $collection->pluck('name')->toArray());

        $expected = $this->getAllPermissionsValues();

        $this->assertSame($expected, $collection->pluck('value')->toArray());
    }

    /**
     * Returns an array with the values of all the
     * permissions
     *
     * @return array
     */
    private function getAllPermissionsValues(){
        return array(
            Permission::PERMISSION_DENIED_VALUE,
            Permission::VIEW_PATIENTS_VALUE,
            Permission::EDIT_PATIENTS_VALUE,
            Permission::VIEW_USERS_VALUE,
            Permission::EDIT_USERS_VALUE,
            Permission::START_NOTES_VALUE,
            Permission::FORWARD_NOTES_VALUE,
            Permission::FINISH_NOTES_VALUE,
            Permission::CALENDAR_ADMIN_VALUE,
            Permission::FACILITY_ADMIN_VALUE,
            Permission::COMPANY_ADMIN_VALUE,
            Permission::EDIT_DELETE_PATIENT_TRANSACTIONS_VALUE,
            Permission::MOBILE_INTAKE_USER_VALUE,
            Permission::MOBILE_INTAKE_ADMIN_VALUE,
        );
    }
    /**
     * Returns an array with the names of all the
     * permissions
     *
     * @return array
     */
    private function getAllPermissionsNames(){
        return array(
            Permission::PERMISSION_DENIED_NAME,
            Permission::VIEW_PATIENTS_NAME,
            Permission::EDIT_PATIENTS_NAME,
            Permission::VIEW_USERS_NAME,
            Permission::EDIT_USERS_NAME,
            Permission::START_NOTES_NAME,
            Permission::FORWARD_NOTES_NAME,
            Permission::FINISH_NOTES_NAME,
            Permission::CALENDAR_ADMIN_NAME,
            Permission::FACILITY_ADMIN_NAME,
            Permission::COMPANY_ADMIN_NAME,
            Permission::EDIT_DELETE_PATIENT_TRANSACTIONS_NAME,
            Permission::MOBILE_INTAKE_USER_NAME,
            Permission::MOBILE_INTAKE_ADMIN_NAME,
        );
    }

    /**
     * Creates and initializes the PermissionService
     *
     * @return PermissionService
     */
    private function getPermissionService(){
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(function($name) {

                if ($name === 'EMRAdmin\Service\User\Dto\Permission')
                {
                    return new Permission;
                }

                if ($name === 'EMRAdmin\Service\User\Dto\PermissionCollection')
                {
                    return new PermissionCollection;
                }

                throw new InvalidArgumentException("Mock PrototypeFactory cannot provide [$name].");
            }));

        $service = new PermissionService;
        $service->setPrototypeFactory($prototypeFactory);
        return $service;
    }

    /**
     * Tests that the PermissionService returns the correct list
     * of permissions based on the modules that an User facilities
     * have enabled
     *
     * If the user has the Mobile Intake Module it will return the full
     * list of permissions, if not, Mobile Intake permissions will be
     * removed from the array
     */
    public function testGetListForUserWithMobile(){
        $permissionService = $this->getPermissionService();
        $user = $this->injectPermissionDependencies($permissionService);


        /**
         * Test With Mobile Intake Module
         */
        $this->preparePermissionsWithMobile($permissionService);

        $collection = $permissionService->getListForUser($user);
        $this->assertInstanceOf('EMRAdmin\Service\User\Dto\PermissionCollection', $collection);

        $expected = $this->getAllPermissionsNames();
        $this->assertSame($expected, $collection->pluck('name')->toArray());

        $expected = $this->getAllPermissionsValues();
        $this->assertSame($expected, $collection->pluck('value')->toArray());

        /**
         * Test Without Mobile Intake Module
         */
        $this->preparePermissionsWithoutMobile($permissionService);

        $collection = $permissionService->getListForUser($user);
        $this->assertInstanceOf('EMRAdmin\Service\User\Dto\PermissionCollection', $collection);

        $expected = $this->getAllPermissionsNames();

        /**
         * Remove the User and Admin names since we will assume they are no
         * longer present on the permissions.
         */
        $mobileUserKey = array_search(Permission::MOBILE_INTAKE_USER_NAME, $expected);
        $mobileAdminKey = array_search(Permission::MOBILE_INTAKE_ADMIN_NAME, $expected);
        unset($expected[$mobileUserKey]);
        unset($expected[$mobileAdminKey]);

        $this->assertSame($expected, $collection->pluck('name')->toArray());

        /**
         * Remove the User and Admin values since we will assume they are no
         * longer present on the permissions.
         */
        $expected = $this->getAllPermissionsValues();
        $mobileUserKey = array_search(Permission::MOBILE_INTAKE_USER_VALUE, $expected);
        $mobileAdminKey = array_search(Permission::MOBILE_INTAKE_ADMIN_VALUE, $expected);
        unset($expected[$mobileUserKey]);
        unset($expected[$mobileAdminKey]);

        $this->assertSame($expected, $collection->pluck('value')->toArray());

    }

    /**
     * Injects the PermissionService with User and Modules dependencies
     *
     * @param $permissionService
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function injectPermissionDependencies($permissionService){
        $modulesDao = $this->getMock('EMRAdmin\Service\Facility\Module\Dao\Modules');

        /**
         * We create a User stub that will return an array with the
         * number 1 when asked for it's facilities
         */
        $user = $this->getMock('EMRAdmin\Service\User\Dto\User');
        $user->expects($this->any())
            ->method('getFacilities')
            ->will($this->returnValue(array(1)));
        return $user;
    }

    /**
     * Injects the PermissionService with the Mobile Intake Module
     *
     * @param PermissionService $permissionService
     */
    private function preparePermissionsWithMobile(PermissionService $permissionService){
        $moduleDto = new FacilityHasModulesDto();
        $moduleDto->setId(1);
        $moduleDto->setFacilityId(1);
        $moduleDto->setModuleId(18);

        $facilityHasModuleService = $this->getMock('EMRAdmin\Service\Facility\FacilityHasModules\FacilityHasModules');
        $facilityHasModuleService->expects($this->once())
            ->method('getFacilityHasModulesByFacilityId')
            ->with($this->equalTo(1))
            ->willReturn(array($moduleDto));
        $permissionService->setFacilityHasModulesService($facilityHasModuleService);
    }

    /**
     * Injects the PermissionService with a Module different than the Mobile
     *
     * @param PermissionService $permissionService
     */
    private function preparePermissionsWithoutMobile(PermissionService $permissionService){
        $moduleDto = new FacilityHasModulesDto();
        $moduleDto->setId(8);
        $moduleDto->setFacilityId(1);
        $moduleDto->setModuleId(8);

        $facilityHasModuleService = $this->getMock('EMRAdmin\Service\Facility\FacilityHasModules\FacilityHasModules');
        $facilityHasModuleService->expects($this->once())
            ->method('getFacilityHasModulesByFacilityId')
            ->with($this->equalTo(1))
            ->willReturn(array($moduleDto));
        $permissionService->setFacilityHasModulesService($facilityHasModuleService);
    }

    /**
     * Tests that the PermissionService returns the correct
     * list of Permissions
     */
    public function testGetListPermission()
    {
        $permissionList = [
            1 => 'view patient',
            2 => 'Insurance Manager',
        ];
        $expected = [
            'view patient',
            'Insurance Manager'
        ];
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will(static::returnCallback(function ($name) {

                if ($name === Permission::class) {
                    return new Permission;
                }

                if ($name === PermissionCollection::class) {
                    return new PermissionCollection;
                }

                throw new InvalidArgumentException("Mock PrototypeFactory cannot provide [$name].");
            }));

        $service = new PermissionService($permissionList);
        $service->setPrototypeFactory($prototypeFactory);
        $collection = $service->getList();

        static::assertSame($expected, $collection->pluck('name')->toArray());
    }
}

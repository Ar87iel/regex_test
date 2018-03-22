<?php

namespace EMRAdminTest\unit\tests\Service\User;

use EMRAdmin\Service\User\PermissionEntityCollectionToArray;
use EMRAdmin\Service\User\PermissionFactory;
use EMRAdmin\Service\User\Permission;
use EmrDomain\User\PermissionEntity;
use EmrPersistenceSdk\Service\Permission as PermissionSdk;
use EmrPersistenceSdk\Sdk;
use PHPUnit_Framework_TestCase;
use Wpt\FeatureFlip\FeatureFlipInterface;
use Wpt\UserPermissions\PermissionConstants;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayObject;

/**
 * Responsible to test all behavior on PermissionFactory class.
 */
class PermissionFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Provide data to verify createservice.
     *
     * @return String[]
     */
    public function filterProvider()
    {
        $blackList = [
            'permissions_black_list' => [
                PermissionConstants::INSURANCE_ADMIN_TAG,
                PermissionConstants::ANALYTICS_PERMISSION_TAG,
            ],
        ];

        $emptyBlackList = [
            'permissions_black_list' => [],
        ];

        return [
            'create service with blacklist' => [$blackList, $this->getEntity(), false],
            'create service without blacklist' => [[], $this->getEntity(), false],
            'create service with exception' => [[], null, false],
            'create service with blacklist FF on' => [$blackList, $this->getEntity(), true],
            'create service with empty black list' => [$emptyBlackList, $this->getEntity(), true],
        ];
    }

    /**
     * Verifies if createService method return an instance of Permissions.
     *
     * @param bool $blackList
     * @param ArrayObject $entity
     * @param bool $featureFlipActivated
     * @dataProvider filterProvider
     */
    public function testCreateService($blackList, $entity, $featureFlipActivated)
    {
        $permissionFactory = new PermissionFactory();
        $serviceLocator = new ServiceManager();
        $serviceLocator->setService('config', $blackList);

        $featureFlip = $this->getMock(FeatureFlipInterface::class);
        $featureFlip->expects(static::any())
            ->method('enabled')
            ->will(static::returnValue($featureFlipActivated));

        $serviceLocator->setService('FeatureFlip', $featureFlip);

        $permissionEntity = new PermissionEntityCollectionToArray();
        $serviceLocator->setService(PermissionEntityCollectionToArray::class, $permissionEntity);

        $permissionSdk = $this->getMockBuilder(PermissionSdk::class)
            ->disableOriginalConstructor()
            ->getMock();
        $permissionSdk->expects(static::any())
            ->method("getPermissionCatalog")
            ->will(static::returnValue($entity));

        $sdk = $this->getMockBuilder(Sdk::class)
            ->disableOriginalConstructor()
            ->getMock();
        $sdk->expects(static::any())
            ->method("getPermissionService")
            ->will(static::returnValue($permissionSdk));

        $serviceLocator->setService(Sdk::class, $sdk);

        $instance = $permissionFactory->createService($serviceLocator);

        static::assertInstanceOf(
            Permission::class,
            $instance,
            'The instance of ' . Permission::class . ' returned was not created correctly.'
        );
    }

    /**
     * Return collection of permissionentity
     *
     * @return ArrayObject
     */
    private function getEntity()
    {
        $permission1 = new PermissionEntity();
        $permission1->setCode(1);
        $permission1->setDescription('view patient');
        $permission2 = new PermissionEntity();
        $permission2->setCode(2);
        $permission2->setDescription('Insurance Manager');
        $permissionEntities = new ArrayObject();
        $permissionEntities->append($permission1);
        $permissionEntities->append($permission2);

        return $permissionEntities;
    }
}

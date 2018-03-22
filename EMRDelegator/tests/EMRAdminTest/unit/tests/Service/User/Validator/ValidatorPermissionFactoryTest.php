<?php

namespace EMRAdminTest\unit\tests\Service\User\Validator;

use EMRAdmin\Service\User\Validator\ValidatorPermissionFactory;
use PHPUnit_Framework_TestCase;
use Wpt\UserPermissions\PermissionConstants;
use Zend\ServiceManager\ServiceManager;
use Zend\Validator\ValidatorInterface;

/**
 * Responsible to test all behavior on ValidatorPermissionFactory.
 */
class ValidatorPermissionFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Provide data to verify creation service.
     *
     * @return string[]
     */
    public function createServiceProvider()
    {
        $config = [
            'permissions_black_list' => [PermissionConstants::INSURANCE_ADMIN_TAG]
        ];

        $configEnable = [
            'permissions_black_list' => []
        ];

        return [
            'With insurance manager, engine disabled' => [$config],
            'With insurance manager, engine enabled' => [$configEnable],
            'Without insurance manager, engine disabled' => [[]],
        ];
    }

    /**
     * Verifies correct acreation service.
     *
     * @param string[] $configuration
     *
     * @dataProvider createServiceProvider
     */
    public function testCreateService($configuration)
    {
        $factory = new ValidatorPermissionFactory();
        $serviceManager = new ServiceManager();
        $serviceManager->setService('config', $configuration);

        $instance = $factory->createService($serviceManager);

        static::assertInstanceOf(
            ValidatorInterface::class,
            $instance,
            'The instance returned should be ValidatorInterface'
        );
    }
}

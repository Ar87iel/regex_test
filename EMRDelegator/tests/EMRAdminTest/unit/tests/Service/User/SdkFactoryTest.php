<?php

namespace EMRAdminTest\unit\tests\Service\User;

use EMRAdmin\Service\User\SdkFactory;
use EmrPersistenceSdk\Sdk;
use \PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceManager;

/**
 * Responsible to test all behavior on SdkFactory class.
 */
class SdkFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Verifies if createService method return an instance of Permissions.
     * */
    public function testCreateService()
    {
        $serviceLocator = new ServiceManager();
        $serviceLocator->setService('config', $this->getConfiguration());
        $sdkFactory = new SdkFactory();
        $instance = $sdkFactory->createService($serviceLocator);
        static::assertInstanceOf(
            Sdk::class,
            $instance,
            'The instance of ' . Sdk::class . ' returned was not created correctly.'
        );
    }

    /**
     * return configuration emr persitence api
     * @return array
     */
    private function getConfiguration()
    {
        return [
            'Wpt' => [
                'EmrPersistenceApi' => [
                    'Sdk' => [
                        'base_url' => 'http://192.168.50.102/',
                        'config' => [],
                    ],
                ],
            ],
        ];
    }
}

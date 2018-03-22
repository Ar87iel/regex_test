<?php

namespace EMRAdminTest\unit\tests\src\FeatureFlip;

use EMRCore\Session\Instance\Application;
use PHPUnit_Framework_TestCase;
use EMRAdmin\FeatureFlip\DummyFeatureRequester;
use EMRAdmin\FeatureFlip\LaunchDarklyFactory;
use EMRCore\Session\SessionInterface;
use Wpt\FeatureFlip\FeatureFlipInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Test suite for LaunchDarklyFactory class
 */
class LaunchDarklyFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceManager
     */
    private $serviceLocator;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->serviceLocator = new ServiceManager();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        unset($this->serviceLocator);
    }

    /**
     * Verifies if CreateService create and instance type of FeatureFlipInterface
     */
    public function testCreateService()
    {
        $this->serviceLocator->setService('config', [
            'launch-darkly' => [
                'api-key' => 'abc',
                'options' => [
                    'feature_requester_class' => new DummyFeatureRequester(),
                ],
            ],
        ]);
        $this->serviceLocator->setService(
            Application::class,
            $this->getMock(SessionInterface::class)
        );

        $service = (new LaunchDarklyFactory())->createService($this->serviceLocator);

        static::assertInstanceOf(FeatureFlipInterface::class, $service);
    }
}

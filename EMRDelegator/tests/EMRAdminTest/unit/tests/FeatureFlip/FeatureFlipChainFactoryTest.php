<?php

namespace EMRAdminTest\unit\tests\src\FeatureFlip;

use EMRCore\Session\Instance\Application;
use EMRAdmin\FeatureFlip\DummyFeatureRequester;
use EMRAdmin\FeatureFlip\FeatureFlipChainFactory;
use EMRAdmin\FeatureFlip\LaunchDarklyFactory;
use EMRCore\Session\SessionInterface;
use PHPUnit_Framework_TestCase;
use Psr\Log\NullLogger;
use Wpt\FeatureFlip\FeatureFlipInterface;
use Wpt\FeatureFlipAppConfig\FeatureFlipFactory;
use Wpt\LaunchDarkly\UserProviderInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Checks FeatureFlipChainFactory functionality
 */
class FeatureFlipChainFactoryTest extends PHPUnit_Framework_TestCase
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
            'Wpt\LaunchDarkly\UserProvider',
            $this->getMock(UserProviderInterface::class)
        );

        $featureFlip = (new FeatureFlipChainFactory())->createService($this->serviceLocator);

        self::assertInstanceOf(FeatureFlipInterface::class, $featureFlip);
    }

    /**
     * Verifies fallback integration on createService method.
     */
    public function testFallbackIntegration()
    {
        $this->serviceLocator->setService('config', [
            FeatureFlipChainFactory::class => [
                FeatureFlipFactory::class,
                LaunchDarklyFactory::class,
            ],
            'launch-darkly' => [
                'options' => [
                    'feature_requester_class' => DummyFeatureRequester::class,
                    'logger' => new NullLogger(),
                ],
            ],
            'features' => [
                'foo' => false,
            ],
        ]);
        $this->serviceLocator->setService(
            Application::class,
            $this->getMock(SessionInterface::class)
        );

        DummyFeatureRequester::$requests = [];
        $featureFlip = (new FeatureFlipChainFactory())->createService($this->serviceLocator);

        static::assertInstanceOf(FeatureFlipInterface::class, $featureFlip);
    }
}

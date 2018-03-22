<?php

namespace EMRAdminTest\unit\tests\Service\User\Ghost;

use EMRAdmin\Service\User\Ghost\GhostAdvanced;
use EMRAdmin\Service\User\Ghost\GhostAdvancedFactory;
use EMRAdmin\Service\User\Ghost\Ghost;
use PHPUnit\Framework\TestCase;
use Wpt\FeatureFlip\FeatureFlipInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Responsible to test all behavior on PermissionFactory class.
 */
class GhostAdvancedFactoryTest extends TestCase
{
    /* @var $sut GhostAdvancedFactory */
    public $sut;
    /* @var $sut ServiceManager */
    public $serviceLocator;
    /* @var $sut FeatureFlipInterface | \PHPUnit_Framework_MockObject_MockObject */
    public $featureFlipMock;
    /* @var $sut GhostAdvanced | \PHPUnit_Framework_MockObject_MockObject */
    public $GhostAdvancedMock;

    public function setup()
    {
        $this->featureFlipMock = $this->getMockBuilder(FeatureFlipInterface::class)
                                      ->disableOriginalConstructor()
                                      ->createMock();
        $this->GhostAdvancedMock = $this->getMockBuilder(GhostAdvanced::class)
                                      ->disableOriginalConstructor()
                                      ->createMock();
        $this->serviceLocator        = $serviceLocator = new ServiceManager();
        $serviceLocator->setService(GhostAdvancedFactory::WPT_FEATURE_FLIP, $this->featureFlipMock);
        $serviceLocator->setService(GhostAdvanced::class, $this->GhostAdvancedMock);
        $this->sut = new GhostAdvancedFactory();
    }

    public function tearDown()
    {
        unset($this->sut);
        unset($this->serviceLocator);
        unset($this->featureFlipMock);
    }

    public function testCreateServiceFeatureFlipOff()
    {
        $this->featureFlipMock->method('enabled')->willReturn(false);
        self::assertInstanceOf(Ghost::class, $this->sut->createService($this->serviceLocator));
    }

    public function testCreateServiceFeatureFlipOn()
    {
        $this->featureFlipMock->method('enabled')->willReturn(true);
        self::assertInstanceOf(GhostAdvanced::class, $this->sut->createService($this->serviceLocator));
    }
}

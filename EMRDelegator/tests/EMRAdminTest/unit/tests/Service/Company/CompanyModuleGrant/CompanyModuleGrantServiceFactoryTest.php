<?php

namespace EMRAdminTest\Service\Company\CompanyModuleGrant;

use EMRAdmin\Service\Company\CompanyModuleGrant\CompanyModuleGrantServiceInterface;
use EMRAdmin\Service\Company\CompanyModuleGrant\CompanyModuleGrantServiceFactory;
use EmrPersistenceSdk\Sdk;
use EmrPersistenceSdk\Service\CompanyModulesServiceInterface;
use EmrPersistenceSdk\Service\CompanyModuleGrantServiceInterface as SdkCompanyModuleGrantServiceInterface;
use PHPUnit_Framework_TestCase;
use Psr\Log\LoggerInterface;
use Wpt\FeatureFlip\FeatureFlipInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Responsible to test CompanyHasModuleServiceFactory
 */
class CompanyModuleGrantServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CompanyModuleGrantServiceFactory
     */
    private $factory;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->factory = new CompanyModuleGrantServiceFactory();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();

        unset($this->factory);
    }

    /**
     * Provides data with featureflip values and expected return instances
     *
     * @return array
     */
    public function providerFeatureFlipValues()
    {
        $serviceLocator = $this->setServiceLocator();

        $serviceLocatorWithLogger = $this->setServiceLocator();
        $serviceLocatorWithLogger->setService('SimpleLogger', $this->createMock(LoggerInterface::class));

        return [
            'company-modules enabled, logger not found' => [true, $serviceLocator],
            'company-modules disabled, logger not found' => [false, $serviceLocator],
            'company-modules enabled, logger found' => [true, $serviceLocatorWithLogger],
            'company-modules disabled, logger found' => [false, $serviceLocatorWithLogger],
        ];
    }

    /**
     * Checks the factory is returning the correct instance
     *
     * @dataProvider providerFeatureFlipValues
     *
     * @param bool                                   $enabledValue
     * @param ServiceLocatorInterface|ServiceManager $serviceLocator
     */
    public function testCreateService($enabledValue, ServiceLocatorInterface $serviceLocator)
    {
        $featureFlip = $this->createMock(FeatureFlipInterface::class);
        $featureFlip->expects(static::any())->method('enabled')->willReturn($enabledValue);

        $serviceLocator->setService('Wpt\FeatureFlip', $featureFlip);

        $result = $this->factory->createService($serviceLocator);

        static::assertInstanceOf(CompanyModuleGrantServiceInterface::class, $result);
    }

    /**
     * Instantiates a ServiceManager instance with necessary dependencies
     *
     * @return ServiceLocatorInterface|ServiceManager
     */
    private function setServiceLocator()
    {
        /** @var ServiceLocatorInterface $serviceLocator */
        $serviceLocator = new ServiceManager();
        $serviceLocator->setAllowOverride(true);

        $sdk = $this->getMockBuilder(Sdk::class)->disableOriginalConstructor()->createMock();

        $companyModuleService = $this->createMock(CompanyModulesServiceInterface::class);

        $sdkCompanyModuleGrantService = $this->createMock(SdkCompanyModuleGrantServiceInterface::class);

        $sdk->expects(static::any())
            ->method('getCompanyModuleService')
            ->will(static::returnValue($companyModuleService));
        $sdk->expects(static::any())
            ->method('getCompanyModuleGrantService')
            ->will(static::returnValue($sdkCompanyModuleGrantService));

        $serviceLocator->setService(Sdk::class, $sdk);

        return $serviceLocator;
    }
}

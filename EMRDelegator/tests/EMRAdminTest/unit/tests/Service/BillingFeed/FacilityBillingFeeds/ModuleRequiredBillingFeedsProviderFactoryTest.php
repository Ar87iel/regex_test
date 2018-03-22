<?php

namespace EMRAdminTest\Service\BillingFeed\FacilityBillingFeeds;

use EMRAdmin\Service\BillingFeed\FacilityBillingFeeds\ModuleRequiredBillingFeedsProviderFactory;
use Zend\ServiceManager\ServiceManager;

class ModuleRequiredBillingFeedsProviderFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ServiceManager */
    private $serviceLocator;
    
    /** @var ModuleRequiredBillingFeedsProviderFactory */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->serviceLocator = new ServiceManager();
        
        $this->sut = new ModuleRequiredBillingFeedsProviderFactory();
    }
    
    public function testCreateService()
    {
        $this->serviceLocator->setService(
            'EMRAdmin\Service\Facility\Module\Modules',
            $this->createMock('\EMRAdmin\Service\Facility\Module\ModulesInterface')
        );
        
        $this->serviceLocator->setService(
            'EMRAdmin\Service\BillingFeed\BillingFeed',
            $this->createMock('\EMRAdmin\Service\BillingFeed\BillingFeedInterface')
        );
        
        $actual = $this->sut->createService($this->serviceLocator);
        
        self::assertInstanceOf(
            '\EMRAdmin\Service\BillingFeed\FacilityBillingFeeds\ModuleRequiredBillingFeedsProvider',
            $actual
        );
    }
}

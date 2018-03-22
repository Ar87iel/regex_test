<?php

namespace EMRAdminTest\Service\BillingFeed\FacilityBillingFeeds;

use EMRAdmin\Service\BillingFeed\FacilityBillingFeeds\FacilityBillingFeedsProviderFactory;
use Zend\ServiceManager\ServiceManager;

class FacilityBillingFeedsProviderFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ServiceManager */
    private $serviceLocator;
    
    /** @var FacilityBillingFeedsProviderFactory */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->serviceLocator = new ServiceManager();
        
        $this->sut = new FacilityBillingFeedsProviderFactory();
    }
    
    public function testCreateService()
    {
        $this->serviceLocator->setService(
            'EMRAdmin\Service\BillingFeed\BillingFeed',
            $this->createMock('\EMRAdmin\Service\BillingFeed\BillingFeedInterface')
        );
        
        $actual = $this->sut->createService($this->serviceLocator);
        
        self::assertInstanceOf(
            '\EMRAdmin\Service\BillingFeed\FacilityBillingFeeds\FacilityBillingFeedsProvider',
            $actual
        );
    }
}

<?php

namespace EMRAdminTest\Service\BillingFeed\FacilityBillingFeeds;

use EMRAdmin\Service\BillingFeed\Dto\BillingFeed;
use EMRAdmin\Service\BillingFeed\Dto\BillingFeedCollection;
use EMRAdmin\Service\BillingFeed\FacilityBillingFeeds\FacilityBillingFeedsProvider;

class FacilityBillingFeedsProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject|\EMRAdmin\Service\BillingFeed\BillingFeedInterface */
    private $billingFeedService;
    
    /** @var FacilityBillingFeedsProvider */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->billingFeedService = $this->getMock('\EMRAdmin\Service\BillingFeed\BillingFeedInterface');
        
        $this->sut = new FacilityBillingFeedsProvider($this->billingFeedService);
    }
    
    public function testGetBillingFeedsByFacilityId()
    {
        $this->billingFeedService->method('getList')->willReturn(new BillingFeedCollection(array(
            $expected = BillingFeed::create(8, 'AdvancedMD'),
        )));
        
        $actual = $this->sut->getBillingFeedsByFacilityId(1234);
        
        self::assertInternalType('array', $actual);
        self::assertArrayHasKey($expected->getId(), $actual);
        self::assertEquals($expected->getName(), $actual[$expected->getId()]);
    }
}

<?php

namespace EMRAdminTest\Service\BillingFeed\FacilityBillingFeeds;

use EMRAdmin\Service\BillingFeed\Dto\BillingFeed;
use EMRAdmin\Service\BillingFeed\Dto\BillingFeedCollection;
use EMRAdmin\Service\BillingFeed\FacilityBillingFeeds\ModuleRequiredBillingFeedsProvider;

class ModuleRequiredBillingFeedsProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject|\EMRAdmin\Service\Facility\Module\ModulesInterface */
    private $moduleService;
    
    /** @var  \PHPUnit_Framework_MockObject_MockObject|\EMRAdmin\Service\BillingFeed\BillingFeedInterface */
    private $billingFeedService;
    
    /** @var ModuleRequiredBillingFeedsProvider */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->moduleService = $this->getMock('\EMRAdmin\Service\Facility\Module\ModulesInterface');
        $this->billingFeedService = $this->getMock('\EMRAdmin\Service\BillingFeed\BillingFeedInterface');
        
        $this->sut = new ModuleRequiredBillingFeedsProvider(
            $this->moduleService,
            $this->billingFeedService
        );
    }

    public function testGetBillingFeedsByFacilityId()
    {
        $this->moduleService->method('hasBillingFeedModule')->willReturn(true);
        
        $this->billingFeedService->method('getList')->willReturn(new BillingFeedCollection(array(
            $expected = BillingFeed::create(8, 'AdvancedMD'),
        )));

        $actual = $this->sut->getBillingFeedsByFacilityId(1234);

        self::assertInternalType('array', $actual);
        self::assertArrayHasKey($expected->getId(), $actual);
        self::assertEquals($expected->getName(), $actual[$expected->getId()]);
    }

    public function testNotGetBillingFeedsByFacilityIdDueToInvalidFacilityId()
    {
        $actual = $this->sut->getBillingFeedsByFacilityId(0);

        self::assertNull($actual);
    }

    public function testNotGetBillingFeedsByFacilityIdDueToMissingModule()
    {
        $this->moduleService->method('hasBillingFeedModule')->willReturn(false);

        $actual = $this->sut->getBillingFeedsByFacilityId(1234);

        self::assertNull($actual);
    }
}

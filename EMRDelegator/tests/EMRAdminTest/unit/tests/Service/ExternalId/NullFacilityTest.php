<?php

namespace EMRAdminTest\unit\tests\Service\ExternalId;

use EMRAdmin\Service\ExternalId\NullFacility;

class NullFacilityTest extends \PHPUnit_Framework_TestCase
{
    /** @var  NullFacility */
    private $sut;

    protected function setUp()
    {
        parent::setUp();
        $this->sut = new NullFacility();
    }

    public function testSetIntegrationToken()
    {
        self::assertNull($this->sut->setIntegrationToken(23, '32432', '23432'));
    }

    public function testGetintegrationToken()
    {
        self::assertNull($this->sut->getIntegrationToken(345));
    }
}

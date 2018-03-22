<?php

namespace EMRAdminTest\unit\tests\Service\ExtenralId\Dto;

use EMRAdmin\Service\Facility\Dto\Facility;

class FacilityTest extends \PHPUnit_Framework_TestCase
{
    public function testSettingAndGettingIntegrationToken()
    {
        $token = 'sdfdsfsdf';
        $facilityDto = new Facility();
        self::assertNull($facilityDto->getIntegrationToken());

        $facilityDto->setIntegrationToken($token);
        self::assertEquals($token, $facilityDto->getIntegrationToken());
    }
}


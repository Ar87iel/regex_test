<?php

namespace EMRAdminTest\unit\tests\Service\ExtenralId\Dto;

use EMRAdmin\Service\Facility\Dto\SaveFacilityRequest;

class SaveFacilityRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testSettingAndGettingIntegrationToken()
    {
        $token = 'sdfdsfsdf';
        $request = new SaveFacilityRequest();
        self::assertNull($request->getIntegrationToken());

        $request->setIntegrationToken($token);
        self::assertEquals($token, $request->getIntegrationToken());
    }
}


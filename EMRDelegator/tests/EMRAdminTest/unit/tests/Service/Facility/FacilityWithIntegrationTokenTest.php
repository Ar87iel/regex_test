<?php

namespace EMRAdminTest\unit\tests\Service;

use EMRAdmin\Service\ExternalId\FacilityIntegrationTokenInterface;
use EMRAdmin\Service\Facility\Dao\Esb;
use EMRAdmin\Service\Facility\Dto\Facility;
use EMRAdmin\Service\Facility\Dto\SaveFacilityRequest;
use EMRAdmin\Service\Facility\Dto\SaveFacilityResponse;
use EMRAdmin\Service\Facility\FacilityWithIntegrationToken;

class FacilityWithIntegrationTokenTest extends \PHPUnit_Framework_TestCase
{
    /** @var  FacilityWithIntegrationToken */
    private $sut;
    /** @var  Esb | \PHPUnit_Framework_MockObject_MockObject */
    private $esb;
    /** @var  FacilityIntegrationTokenInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $facilityExtIdService;

    public function testGetFacility()
    {
        $facilityId = 123456;
        $integrationToken = 'dsjfkhgsk';

        $this->esb->expects(self::any())
            ->method('getFacilityById')
            ->with($facilityId)
            ->willReturn(new Facility());

        $this->facilityExtIdService->expects(self::any())
            ->method('getIntegrationToken')
            ->with($facilityId)
            ->willReturn($integrationToken);

        $result = $this->sut->getFacilityById($facilityId);
        self::assertInstanceOf('EMRAdmin\Service\Facility\Dto\Facility', $result);
        self::assertEquals($integrationToken, $result->getIntegrationToken());
    }

    public function testsaveFacility()
    {
        $criteria = new SaveFacilityRequest;
        $criteria->setId($id = 23432);
        $criteria->setIntegrationToken($integrationToken = 'dsfsdfdsf');
        $criteria->setBfmExternalId($externalId = 'sfsdfsd');

        $response = new SaveFacilityResponse();
        $response->setId($id);

        $this->esb->expects(self::once())
            ->method('saveFacility')
            ->with($criteria)
            ->willReturn($response);

        $this->facilityExtIdService->expects(self::once())
            ->method('setIntegrationToken')
            ->with($id, $integrationToken, $externalId);

        $result = $this->sut->saveFacility($criteria);
        self::assertInstanceOf('\EMRAdmin\Service\Facility\Dto\SaveFacilityResponse', $result);
    }

    public function testGetActiveLicenseCounts()
    {
        $facilityId = 3423432;
        $companyId = 435353;
        $licenseArray = array();

        $this->esb->expects(self::any())
            ->method('getActiveLicenseCounts')
            ->with($facilityId, $companyId)
            ->willReturn($licenseArray);

        $result = $this->sut->getActiveLicenseCounts($facilityId, $companyId);
        self::assertEquals($licenseArray, $result);
    }


    protected function setUp()
    {
        parent::setUp();
        $this->esb = $this->getMock('EMRAdmin\Service\Facility\Dao\Esb');
        $this->facilityExtIdService = $this->getMock('EMRAdmin\Service\ExternalId\FacilityIntegrationTokenInterface');
        $this->sut = new FacilityWithIntegrationToken($this->facilityExtIdService, $this->esb);
    }

    public function testsaveFacilityDoesNotCallExtIdServiceIfResponseDoesNotContainAnId()
    {
        $criteria = new SaveFacilityRequest;
        $criteria->setId($id = 23432);
        $criteria->setIntegrationToken($integrationToken = 'dsfsdfdsf');
        $criteria->setBfmExternalId($externalId = 'sfsdfsd');

        $this->esb->expects(self::once())
            ->method('saveFacility')
            ->with($criteria)
            ->willReturn(new SaveFacilityResponse());

        $this->facilityExtIdService->expects(self::never())
            ->method('setIntegrationToken');

        $result = $this->sut->saveFacility($criteria);
        self::assertInstanceOf('\EMRAdmin\Service\Facility\Dto\SaveFacilityResponse', $result);
    }

}


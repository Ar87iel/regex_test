<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/09/13 11:27 AM
 */
namespace ConsoleTest\Unit;

use Console\Etl\Service\FacilitiesImport\Dao\Dto\FacilityResult;
use EMRCoreTest\Helper\Reflection as Helper;
use Console\Etl\Service\FacilitiesImport\FacilitiesImport;
use Console\Etl\Service\FacilitiesImport\Dao\Dto\DelegatorFacility;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\ClassMethods;

class FacilitiesImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FacilitiesImport
     */
    private $importService;

    public function setup()
    {
        $this->importService = new FacilitiesImport();
    }

    public function testImportFromLegacyCallsWriteForEachFacility()
    {
        $facilityInfo1 = array('facilityId'=>1, 'companyId'=>10, 'name'=>'My Facility 1');
        $facilityInfo2 = array('facilityId'=>2, 'companyId'=>20, 'name'=>'My Facility 2');

        $facilities = array($facilityInfo1, $facilityInfo2);

        $facilitiesResult = new HydratingResultSet(new ClassMethods(), new FacilityResult());
        $facilitiesResult->initialize($facilities);

        $dao = $this->getMock('Console\Etl\Service\FacilitiesImport\Dao\Dao');
        $dao->expects($this->once())->method('getFacilities')
            ->will($this->returnValue($facilitiesResult));

        $dao->expects($this->once())->method('turnOffKeyConstraints');
        $dao->expects($this->once())->method('turnOnKeyConstraints');

        $service = $this->getMock('Console\Etl\Service\FacilitiesImport\FacilitiesImport',
            array('writeFacilityToDelegator'));
        $service->expects($this->exactly(2))->method('writeFacilityToDelegator');

        /** @var \Console\Etl\Service\FacilitiesImport\FacilitiesImport $service */
        $service->setFacilitiesImportDao($dao);
        $service->importFromLegacy();
    }

    /**
     * @expectedException \Console\Etl\Service\FacilitiesImport\Exception\NoFacilitiesFound
     */
    public function testImportFromLegacyThrowsExceptionWhenNoFacilities()
    {
        $result = new HydratingResultSet();

        $dao = $this->getMock('Console\Etl\Service\FacilitiesImport\Dao\Dao');
        $dao->expects($this->once())->method('getFacilities')
            ->will($this->returnValue($result));

        $this->importService->setFacilitiesImportDao($dao);
        $this->importService->importFromLegacy();
    }

    public function testWriteCompanyToDelegatorCallsDaoCreateFacility()
    {
        $facilityInfo = new FacilityResult();
        $facilityInfo->setCompanyId(10);
        $facilityInfo->setFacilityId(1);
        $facilityInfo->setName('My Facility');

        $delegatorFacility = new DelegatorFacility();

        $service = $this->getMock('Console\Etl\Service\FacilitiesImport\FacilitiesImport',
            array('getDelegatorFacility'));
        $service->expects($this->once())->method('getDelegatorFacility')
            ->with($facilityInfo->getFacilityId(), $facilityInfo->getName(), $facilityInfo->getCompanyId())
            ->will($this->returnValue($delegatorFacility));

        $dao = $this->getMock('Console\Etl\Service\FacilitiesImport\Dao\Dao');
        $dao->expects($this->once())->method('createFacility')
            ->with($delegatorFacility);

        /** @var \Console\Etl\Service\FacilitiesImport\FacilitiesImport $service */
        $service->setFacilitiesImportDao($dao);

        Helper::invoke($service,'writeFacilityToDelegator', array($facilityInfo));
    }

}
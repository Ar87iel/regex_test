<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/09/13 11:27 AM
 */
namespace ConsoleTest\Unit;

use Console\Etl\Service\UserHasFacilityImport\Dao\Dto\FacilityHasUser;
use EMRCoreTest\Helper\Reflection as Helper;
use Console\Etl\Service\UserHasFacilityImport\UserHasFacilityImport;
use Console\Etl\Service\UserHasFacilityImport\Dao\Dto\DelegatorUserHasFacility;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\ClassMethods;

class UserHasFacilityImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserHasFacilityImport
     */
    private $importService;

    public function setup()
    {
        $this->importService = new UserHasFacilityImport();
    }

    public function testImportFromLegacyCallsWriteForEachRelationship()
    {
        $relation1 = array('facilityId'=>1, 'userId'=>10, 'defaultClinic'=>1);
        $relation2 = array('facilityId'=>2, 'userId'=>20, 'defaultClinic'=>0);

        $relationships = array($relation1, $relation2);

        $results = new HydratingResultSet(new ClassMethods(), new FacilityHasUser());
        $results->initialize($relationships);

        $dao = $this->getMock('Console\Etl\Service\UserHasFacilityImport\Dao\Dao');
        $dao->expects($this->once())->method('getUserHasFacility')
            ->will($this->returnValue($results));

        $dao->expects($this->once())->method('truncateDelegatorUserHasFacility');
        
        $service = $this->getMock('Console\Etl\Service\UserHasFacilityImport\UserHasFacilityImport',
            array('writeRelationshipToDelegator', 'facilityExists'));
        $service->expects($this->exactly(2))->method('facilityExists')
            ->withAnyParameters()
            ->will($this->returnValue(true));
        $service->expects($this->exactly(2))->method('writeRelationshipToDelegator');

        /** @var \Console\Etl\Service\UserHasFacilityImport\UserHasFacilityImport $service */
        $service->setUserHasFacilityImportDao($dao);
        $service->importFromLegacy();
    }

    public function testImportFromLegacyDoesNotCallWriteWhenFacilityDoesNotExist()
    {
        $relation1 = array('facilityId'=>1, 'userId'=>10, 'defaultClinic'=>1);
        $relation2 = array('facilityId'=>2, 'userId'=>20, 'defaultClinic'=>0);

        $relationships = array($relation1, $relation2);

        $results = new HydratingResultSet(new ClassMethods(), new FacilityHasUser());
        $results->initialize($relationships);

        $dao = $this->getMock('Console\Etl\Service\UserHasFacilityImport\Dao\Dao');
        $dao->expects($this->once())->method('getUserHasFacility')
            ->will($this->returnValue($results));

        $dao->expects($this->once())->method('truncateDelegatorUserHasFacility');

        $service = $this->getMock('Console\Etl\Service\UserHasFacilityImport\UserHasFacilityImport',
            array('writeRelationshipToDelegator', 'facilityExists'));
        $service->expects($this->exactly(2))->method('facilityExists')
            ->withAnyParameters()
            ->will($this->returnValue(false));
        $service->expects($this->never())->method('writeRelationshipToDelegator');

        /** @var \Console\Etl\Service\UserHasFacilityImport\UserHasFacilityImport $service */
        $service->setUserHasFacilityImportDao($dao);
        $service->importFromLegacy();
    }

    /**
     * @expectedException \Console\Etl\Service\UserHasFacilityImport\Exception\NoRelationshipsFound
     */
    public function testImportFromLegacyThrowsExceptionWhenNoFacilities()
    {
        $result = new HydratingResultSet();

        $dao = $this->getMock('Console\Etl\Service\UserHasFacilityImport\Dao\Dao');
        $dao->expects($this->once())->method('getUserHasFacility')
            ->will($this->returnValue($result));

        $this->importService->setUserHasFacilityImportDao($dao);
        $this->importService->importFromLegacy();
    }

    public function testWriteCompanyToDelegatorCallsDaoCreateFacility()
    {
        $facilityInfo = new FacilityHasUser();
        $facilityInfo->setFacilityId(10);
        $facilityInfo->setUserId(1);
        $facilityInfo->setDefaultClinic(1);

        $userHasFacility = new DelegatorUserHasFacility();

        $service = $this->getMock('Console\Etl\Service\UserHasFacilityImport\UserHasFacilityImport',
            array('getDelegatorUserHasFacility'));
        $service->expects($this->once())->method('getDelegatorUserHasFacility')
            ->with($facilityInfo->getUserId(), $facilityInfo->getFacilityId(), $facilityInfo->getDefaultClinic())
            ->will($this->returnValue($userHasFacility));

        $dao = $this->getMock('Console\Etl\Service\UserHasFacilityImport\Dao\Dao');
        $dao->expects($this->once())->method('createUserHasFacility')
            ->with($userHasFacility);

        /** @var \Console\Etl\Service\UserHasFacilityImport\UserHasFacilityImport $service */
        $service->setUserHasFacilityImportDao($dao);

        Helper::invoke($service,'writeRelationshipToDelegator', array($facilityInfo));
    }

    public function testGetDelegatorUserHasFacility()
    {
        /** @var DelegatorUserHasFacility $result */
        $result = Helper::invoke($this->importService,'getDelegatorUserHasFacility', array(1,2,1));
        $this->assertInstanceOf('Console\Etl\Service\UserHasFacilityImport\Dao\Dto\DelegatorUserHasFacility',$result);
        $this->assertEquals(1,$result->getIdentityId());
        $this->assertEquals(2,$result->getFacilityId());
        $this->assertTrue($result->getIsDefault());
    }


    public function testFacilityExistsCallsDao()
    {
        $facilityInfo = new FacilityHasUser();
        $facilityInfo->setFacilityId(4325);

        $dao = $this->getMock('Console\Etl\Service\UserHasFacilityImport\Dao\Dao');
        $dao->expects($this->once())->method('facilityExists')->with($facilityInfo->getFacilityId());

        $this->importService->setUserHasFacilityImportDao($dao);
        Helper::invoke($this->importService,'facilityExists', array($facilityInfo));
    }


}
<?php

/**
 * @category WebPT
 * @package EMRDelegatorTest
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRDelegatorTest\integration\tests\Service\Dao\PatientHasFacility;

use Doctrine\ORM\Query;
use EMRCore\DoctrineConnector\Adapter\Adapter;
use EMRCore\DoctrineConnector\DoctrineConnectorFactory;
use EMRCore\PrototypeFactory;
use EMRCore\SqlConnector\SqlConnectorFactory;
use EMRCoreTest\lib\PhpUnit\Framework\DatabaseTestCase;
use EMRCoreTest\lib\SqlConnector\DefaultReaderWriter;
use EMRDelegator\Model\Facility as FacilityModel;
use EMRDelegator\Model\PatientHasFacility as PatientHasFacilityModel;
use EMRDelegator\Service\PatientHasFacility\Dao\PatientHasFacility as PatientHasFacilityDao;

class PatientHasFacilityTest extends DatabaseTestCase
{
    /**
     * @var PatientHasFacilityDao
     */
    private $dao;

    /**
     * Creates test tables.
     */
    private function createTables()
    {
        $this->executeSql(include __DIR__ . '/../../../../sql/common/create_table_cluster.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/create_table_company.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/create_table_facility.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/create_table_patienthasfacility.sql.php');

    }

    /**
     * Drops test tables.
     */
    private function dropTables()
    {
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_patienthasfacility.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_facility.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_company.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_cluster.sql.php');
    }

    /**
     * Performs before class set up.
     */
    public function insertRecords()
    {
        $this->executeSql(include __DIR__ . '/../../../../sql/common/insert_clusters.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/insert_companies.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/insert_facilities.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/insert_patienthasfacilities.sql.php');
    }

    /**
     * Performs after class tear down.
     */
    public function tearDown()
    {
        $this->dropTables();
        parent::tearDown();
    }

    /**
     * Performs PhpUnit set up.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->dao = new PatientHasFacilityDao;

        $this->dao->setDefaultReaderWriter($this->getMasterSlaveDoctrineAdapter());
        $this->dropTables();
        $this->createTables();
        $this->insertRecords();
    }

    /**
     * Tests facility creation.
     */
    public function testPatientHasFacilityCreate()
    {
        /**
         * @var PatientHasFacilityModel $criteria
         */
        $criteria = new PatientHasFacilityModel();
        $criteria->setIdentityId(999);
        $criteria->setPatientId(999);
        $criteria->setIsDefault('N');

        /**
         * @var FacilityModel $facilityModel
         */
        $facilityModel = $this->getMasterSlaveDoctrineAdapter()->getEntityManager()->getRepository('EMRDelegator\Model\Facility')->find(1);
        $criteria->setFacility($facilityModel);

        /**
         * @var PatientHasFacilityModel $newModel
         */
        $newModel = $this->dao->create($criteria);

        $this->assertEquals($newModel->getFacility()->getFacilityId(), $criteria->getFacility()->getFacilityId());
    }

    /**
     * Tests facility creation.
     */
    public function testPatientHasFacilityDelete()
    {
        /**
         * @var PatientHasFacilityModel $criteria
         */
        $criteria = new PatientHasFacilityModel();
        $criteria->setIdentityId(888);
        $criteria->setPatientId(888);
        $criteria->setIsDefault('N');

        /**
         * @var FacilityModel $facilityModel
         */
        $facilityModel = $this->getMasterSlaveDoctrineAdapter()->getEntityManager()->getRepository('EMRDelegator\Model\Facility')->find(1);
        $criteria->setFacility($facilityModel);

        /**
         * @var PatientHasFacilityModel $newModel
         * @var PatientHasFacilityModel $PatientHasFacilityModelToDelete
         */
        $newModel = $this->dao->create($criteria);
        $PatientHasFacilityModelToDelete = $this->dao->load($newModel->getRecordId());
        $this->assertEquals($PatientHasFacilityModelToDelete->getFacility()->getFacilityId(), $newModel->getFacility()->getFacilityId());

        $this->dao->delete($PatientHasFacilityModelToDelete);
        $facilityDeleted = $this->dao->load($newModel->getRecordId());
        $this->assertNull($facilityDeleted);
    }

    /**
     * Tests facility load.
     */
    public function testPatientHasFacilityLoad()
    {
        /**
         * @var PatientHasFacilityModel $PatientHasFacilityModel
         */
        $PatientHasFacilityModel = $this->getMasterSlaveDoctrineAdapter()->getEntityManager()->getRepository('EMRDelegator\Model\PatientHasFacility')->find(1);
        $this->assertEquals($PatientHasFacilityModel->getRecordId(), 1);
    }

    /**
     * Tests PatientHasFacility not found.
     */
    public function testPatientHasFacilityLoadNotFound()
    {
        /**
         * @var PatientHasFacilityModel $PatientHasFacilityModel
         */
        $facilityModel = $this->getMasterSlaveDoctrineAdapter()->getEntityManager()->getRepository('EMRDelegator\Model\PatientHasFacility')->find(-1);
        $this->assertNull($facilityModel);
    }

    /**
     * Test counting Patients in a facility
     */
    public function testPatientCountByFacilityId()
    {
        $PatientCount = $this->dao->getPatientCountByFacilityId(1);
        $this->assertEquals(2, $PatientCount);
    }
}

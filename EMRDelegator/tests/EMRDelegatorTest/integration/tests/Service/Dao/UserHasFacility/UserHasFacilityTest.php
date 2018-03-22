<?php

/**
 * @category WebPT
 * @package EMRDelegatorTest
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRDelegatorTest\integration\tests\Service\Dao\UserHasFacility;

use Doctrine\ORM\Query;
use EMRCore\DoctrineConnector\Adapter\Adapter;
use EMRCore\DoctrineConnector\DoctrineConnectorFactory;
use EMRCore\PrototypeFactory;
use EMRCore\SqlConnector\SqlConnectorFactory;
use EMRCoreTest\lib\PhpUnit\Framework\DatabaseTestCase;
use EMRCoreTest\lib\SqlConnector\DefaultReaderWriter;
use EMRDelegator\Model\Facility as FacilityModel;
use EMRDelegator\Model\UserHasFacility as UserHasFacilityModel;
use EMRDelegator\Service\Facility\Dao\Facility as FacilityDao;
use EMRDelegator\Service\UserHasFacility\Dao\UserHasFacility as UserHasFacilityDao;

class UserHasFacilityTest extends DatabaseTestCase
{
    /**
     * The adapter we are using.
     * @var Adapter
     */
    private static $defaultReaderWriter;

    /**
     * @var DefaultReaderWriter
     */
    private static $adapter;

    /**
     * @var UserHasFacilityDao
     */
    private $dao;

    /**
     * @var FacilityDao
     */
    private $facilityDao;

    /**
     * Creates test tables.
     */
    private function createTables()
    {
        $this->executeSql(include __DIR__ . '/../../../../sql/common/create_table_cluster.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/create_table_company.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/create_table_facility.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/create_table_userhasfacility.sql.php');
    }

    private function insertRecords() {
        $this->executeSql(include __DIR__ . '/../../../../sql/common/insert_clusters.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/insert_companies.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/insert_facilities.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/insert_userhasfacilities.sql.php');
    }

    /**
     * Drops test tables.
     */
    private function dropTables()
    {
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_userhasfacility.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_facility.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_company.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_cluster.sql.php');
    }

    /**
     * Performs before class set up.
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
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
        $this->dropTables();
        $this->createTables();
        $this->insertRecords();

        $this->dao = new UserHasFacilityDao();
        $this->facilityDao = new FacilityDao();

        $this->dao->setDefaultReaderWriter(DoctrineConnectorFactory::get('default_reader_writer'));
        $this->facilityDao->setDefaultReaderWriter(DoctrineConnectorFactory::get('default_reader_writer'));
    }

    /**
     * Tests facility creation.
     */
    public function testUserHasFacilityCreate()
    {
        /**
         * @var UserHasFacilityModel $criteria
         */
        $criteria = new UserHasFacilityModel();
        $criteria->setIdentityId(999);
        $criteria->setIsDefault(false);

        /**
         * @var FacilityModel $facilityModel
         */
        $facilityModel = $this->facilityDao->load(1);
        $criteria->setFacility($facilityModel);

        /**
         * @var UserHasFacilityModel $newModel
         */
        $newModel = $this->dao->create($criteria);

        $this->assertEquals($newModel->getFacility()->getFacilityId(), $criteria->getFacility()->getFacilityId());
    }

    /**
     * Tests facility creation.
     */
    public function testUserHasFacilityDelete()
    {
        /**
         * @var UserHasFacilityModel $criteria
         */
        $criteria = new UserHasFacilityModel();
        $criteria->setIdentityId(888);
        $criteria->setIsDefault(false);

        /**
         * @var FacilityModel $facilityModel
         */
        $facilityModel = $this->facilityDao->load(1);
        $criteria->setFacility($facilityModel);

        /**
         * @var UserHasFacilityModel $newModel
         * @var UserHasFacilityModel $userHasFacilityModelToDelete
         */
        $newModel = $this->dao->create($criteria);
        $userHasFacilityModelToDelete = $this->dao->load($newModel->getRecordId());

        $this->assertEquals($userHasFacilityModelToDelete->getFacility()->getFacilityId(), $newModel->getFacility()->getFacilityId());

        $this->dao->delete($userHasFacilityModelToDelete);
        $facilityDeleted = $this->dao->load($newModel->getRecordId());
        $this->assertNull($facilityDeleted);
    }

    /**
     * Tests facility load.
     */
    public function testUserHasFacilityLoad()
    {
        /**
         * @var UserHasFacilityModel $userHasFacilityModel
         */
        $userHasFacilityModel = $this->dao->load(1);
        $this->assertEquals($userHasFacilityModel->getRecordId(), 1);
    }

    /**
     * Tests userHasFacility not found.
     */
    public function testUserHasFacilityLoadNotFound()
    {
        /**
         * @var UserHasFacilityModel $userHasFacilityModel
         */
        $facilityModel = $this->dao->load(-1);
        $this->assertNull($facilityModel);
    }

    /**
     * Test counting users in a facility
     */
    public function testUserCountByFacilityId()
    {
        $userCount = $this->dao->getUserCountByFacilityId(1);
        $this->assertEquals(2, $userCount);
    }

    /**
     * Test that delete by identity deletes all relationships
     */
    public function testDeleteByIdentityId()
    {
        // create relationships
        $identityId = 777;
        $facilityIds = array(1,2,3,4,5);
        $recordIds = array();
        foreach($facilityIds as $facilityId){
            /**
             * @var UserHasFacilityModel $criteria
             */
            $criteria = new UserHasFacilityModel();
            $criteria->setIdentityId($identityId);
            $criteria->setIsDefault(false);
            $criteria->setFacility($this->facilityDao->getReference($facilityId));

            /**
             * @var UserHasFacilityModel $newModel
             */
            $newModel = $this->dao->create($criteria);
            $this->assertGreaterThan(0,$newModel->getRecordId());
            $recordIds[] = $newModel->getRecordId();
        }

        // delete all relationships by identityId
        $this->dao->deleteByIdentityId($identityId);

        // verify relationships have been removed
        foreach($recordIds as $recordId){
            $userHasFacility = $this->dao->load($recordId);
            $this->assertNull($userHasFacility);
        }
    }
}

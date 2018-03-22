<?php

/**
 * @category WebPT
 * @package EMRDelegatorTest
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRDelegatorTest\integration\tests\Service\Dao\Facility;

use Doctrine\ORM\Query;
use EMRCore\DoctrineConnector\DoctrineConnectorFactory;
use EMRCore\PrototypeFactory;
use EMRCore\SqlConnector\SqlConnectorFactory;
use EMRCoreTest\lib\PhpUnit\Framework\DatabaseTestCase;
use EMRDelegator\Model\Company as CompanyModel;
use EMRDelegator\Model\Facility as FacilityModel;
use EMRDelegator\Service\Facility\Dao\Facility as FacilityDao;

class FacilityTest extends DatabaseTestCase
{
    /**
     * @var FacilityDao
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
        $this->executeSql(include __DIR__ . '/../../../../sql/common/create_table_userhasfacility.sql.php');

    }

    /**
     * Drops test tables.
     */
    private function dropTables()
    {
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_userhasfacility.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_patienthasfacility.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_facility.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_company.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_cluster.sql.php');
    }

    /**
     * Performs before class set up.
     */
    protected function insertRecords()
    {
        $this->executeSql(include __DIR__ . '/../../../../sql/common/insert_clusters.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/insert_companies.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/insert_facilities.sql.php');
    }

    /**
     * Performs after class tear down.
     */
    public function tearDown()
    {
        self::dropTables();
        parent::tearDown();
    }

    /**
     * Performs PhpUnit set up.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->dao = new FacilityDao;
        $this->dao->setDefaultReaderWriter($this->getMasterSlaveDoctrineAdapter());
        $this->dropTables();
        $this->createTables();
        $this->insertRecords();
    }

    /**
     * Tests facility creation.
     */
    public function testFacilityCreate()
    {
        /**
         * @var FacilityModel $newFacilityCriteria
         */
        $criteria = new FacilityModel();
        $criteria->setName('mynewfacility');

        $companyModel = DoctrineConnectorFactory::get('default_reader')->getEntityManager()->getRepository('EMRDelegator\Model\Company')->find(1);
        $criteria->setCompany($companyModel);

        /**
         * @var FacilityModel $newModel
         */
        $newModel = $this->dao->create($criteria);

        $this->assertEquals($newModel->getName(), 'mynewfacility');
    }

    /**
     * Tests facility creation.
     */
    public function testFacilityDelete()
    {
        /**
         * @var FacilityModel $criteria
         */
        $criteria = new FacilityModel();
        $criteria->setName('deletethisfacility');

        $companyModel = DoctrineConnectorFactory::get('default_reader')->getEntityManager()->getRepository('EMRDelegator\Model\Company')->find(1);
        $criteria->setCompany($companyModel);

        /**
         * @var FacilityModel $newModel
         */
        $newModel = $this->dao->create($criteria);
        $facilityModelToDelete = $this->dao->load($newModel->getFacilityId());
        $this->assertEquals($facilityModelToDelete->getFacilityId(), $newModel->getFacilityId());

        $this->dao->delete($facilityModelToDelete);
        $facilityDeleted = $this->dao->load($newModel->getFacilityId());
        $this->assertNull($facilityDeleted);
    }

    /**
     * Tests facility load.
     */
    public function testFacilityLoad()
    {
        /**
         * @var FacilityModel $facilityModel
         */
        $facilityModel = $this->getMasterSlaveDoctrineAdapter()->getEntityManager()->getRepository('EMRDelegator\Model\Facility')->find(1);
        $this->assertEquals($facilityModel->getFacilityId(), 1);
    }

    /**
     * Tests facility load all.
     */
    public function testFacilityLoadAll()
    {
        /**
         * @var FacilityModel[] $facilityModels
         */
        $facilityModels = $this->getMasterSlaveDoctrineAdapter()->getEntityManager()->getRepository('EMRDelegator\Model\Facility')->findAll();

        $this->assertEquals(5, count($facilityModels));
    }

    /**
     * Tests facility not found.
     */
    public function testFacilityLoadNotFound()
    {
        /**
         * @var FacilityModel $facilityModel
         */
        $facilityModel = $this->getMasterSlaveDoctrineAdapter()->getEntityManager()->getRepository('EMRDelegator\Model\Facility')->find(-1);
        $this->assertNull($facilityModel);
    }

    /**
     * Tests search by facility name.
     */
    public function testSearchByName()
    {
        // search for prefix common to all three

        /**
         * @var FacilityModel[] $facilityEntities
         */
        $facilityEntities = $this->dao->searchByName('myfacility');
        $this->assertEquals(5, count($facilityEntities));
        $this->assertEquals('myfacility1', $facilityEntities[0]->getName());
        $this->assertEquals($facilityEntities[0]->getCompany()->getCompanyId(), 1);
        $this->assertEquals('myfacility2', $facilityEntities[1]->getName());
        $this->assertEquals($facilityEntities[1]->getCompany()->getCompanyId(), 2);
        $this->assertEquals('myfacility3_1', $facilityEntities[2]->getName());
        $this->assertEquals($facilityEntities[2]->getCompany()->getCompanyId(), 3);
    }

    /**
     * Tests search by facility name not found.
     */
    public function testSearchByNameNotFound()
    {
        /**
         * @var FacilityModel[] $facilityEntities
         */
        $facilityEntities = $this->dao->searchByName('thisfacilityNOTFOUND');
        $this->assertEquals(0, count($facilityEntities));
    }

    /**
     * Tests facility update.
     */
    public function testFacilityUpdate()
    {
        /**
         * @var FacilityModel $facilityModel
         */
        $facilityModel = $this->getMasterSlaveDoctrineAdapter()->getEntityManager()->getRepository('EMRDelegator\Model\Facility')->find(1);
        $updatedName = "updatedfacilityname";
        $facilityModel->setName($updatedName);
        $updatedModel = $this->dao->update($facilityModel);
        $this->assertEquals($updatedModel->getName(), $updatedName);
    }

    /**
     * Test counting facilities in a cluster
     */
    public function testGetFacilityCountByClusterId()
    {
        $facilityCount = $this->dao->getFacilityCountByClusterId(3);
        $this->assertEquals(3, $facilityCount);
    }
}

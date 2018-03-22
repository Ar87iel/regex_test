<?php

/**
 * @category WebPT
 * @package EMRDelegatorTest
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRDelegatorTest\integration\tests\Service\Dao\Company;

use Doctrine\ORM\Query;
use EMRCore\DoctrineConnector\Adapter\Adapter;
use EMRCore\DoctrineConnector\DoctrineConnectorFactory;
use EMRCore\PrototypeFactory;
use EMRCore\SqlConnector\SqlConnectorFactory;
use EMRCoreTest\lib\PhpUnit\Framework\DatabaseTestCase;
use EMRCoreTest\lib\SqlConnector\DefaultReaderWriter;
use EMRDelegator\Model\Company as CompanyModel;
use EMRDelegator\Service\Cluster\Dao\Cluster as ClusterDao;
use EMRDelegator\Service\Company\Dao\Company as CompanyDao;

class CompanyTest extends DatabaseTestCase
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
     * @var CompanyDao
     */
    private $dao;

    /**
     * @var ClusterDao
     */
    private $clusterDao;

    /**
     * Creates test tables.
     */
    private function createTables()
    {
        $this->executeSql(include __DIR__ . '/../../../../sql/common/create_table_cluster.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/create_table_company.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/create_table_facility.sql.php');

    }

    /**
     * Drops test tables.
     */
    private function dropTables()
    {
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_facility.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_company.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_cluster.sql.php');
    }

    private function insertRecords() {
        $this->executeSql(include __DIR__ . '/../../../../sql/common/insert_clusters.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/insert_companies.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/insert_facilities.sql.php');
    }


    /**
     * Performs after class tear down.
     */
    public function tearDown()
    {
        $this->dropTables();
    }

    /**
     * Performs PhpUnit set up.
     */
    protected function setUp()
    {
        $this->dao = new CompanyDao;
        $this->dao->setDefaultReaderWriter($this->getMasterSlaveDoctrineAdapter());

        $this->clusterDao = new ClusterDao();
        $this->clusterDao->setDefaultReaderWriter($this->getMasterSlaveDoctrineAdapter());

        $this->dropTables();
        $this->createTables();
        $this->insertRecords();
    }


    /**
     * Tests company creation.
     */
    public function testCompanyCreate()
    {
        /**
         * @var CompanyModel $newCompanyCriteria
         */
        $criteria = new CompanyModel();
        $criteria->setName('mynewcompany');
        $criteria->setOnlineStatus("online");

        $clusterModel = $this->clusterDao->load(1);
        $criteria->setCluster($clusterModel);

        /**
         * @var CompanyModel $newModel
         */
        $newModel = $this->dao->create($criteria);
        $companyModel = $this->dao->load($newModel->getCompanyId());

        $this->assertEquals($companyModel->getName(), 'mynewcompany');
        $this->assertEquals($companyModel->getCluster()->getClusterId(), $criteria->getCluster()->getClusterId());

        // delete it to clean up.
        $this->dao->delete($companyModel);
    }

    /**
     * Tests company creation.
     */
    public function testCompanyDelete()
    {
        /**
         * @var CompanyModel $criteria
         */
        $criteria = new CompanyModel();
        $criteria->setName('deletethiscompany');
        $criteria->setOnlineStatus("none");

        $clusterModel = $this->clusterDao->load(1);
        $criteria->setCluster($clusterModel);

        /**
         * @var CompanyModel $newModel
         */
        $newModel = $this->dao->create($criteria);
        $companyModelToDelete = $this->dao->load($newModel->getCompanyId());
        $this->assertEquals($companyModelToDelete->getCompanyId(), $newModel->getCompanyId());

        $this->dao->delete($companyModelToDelete);
        $companyDeleted = $this->dao->load($newModel->getCompanyId());
        $this->assertNull($companyDeleted);
    }

    /**
     * Tests company load.
     */
    public function testCompanyLoad()
    {
        /**
         * @var CompanyModel $companyModel
         */
        $companyModel = $this->dao->load(1);
        $this->assertEquals($companyModel->getCompanyId(), 1);
    }

    /**
     * Tests company facilities load.
     */
    public function testCompanyFacilitiesLoad()
    {
        /**
         * @var CompanyModel $companyModel
         */
        $companyModel = $this->dao->load(3);

        $this->assertEquals(3, count($companyModel->getFacilities()));
        $facilityModels = $companyModel->getFacilities();
        $this->assertEquals('myfacility3_1', $facilityModels[0]->getName());
        $this->assertEquals('myfacility3_2', $facilityModels[1]->getName());
        $this->assertEquals('myfacility3_3', $facilityModels[2]->getName());
    }

    /**
     * Tests company not found.
     */
    public function testCompanyLoadNotFound()
    {
        /**
         * @var CompanyModel $companyModel
         */
        $companyModel = $this->dao->load(-1);
        $this->assertNull($companyModel);
    }

    /**
     * Tests company update.
     */
    public function testCompanyUpdate()
    {
        /**
         * @var CompanyModel $companyModel
         */
        $companyModel = $this->getMasterSlaveDoctrineAdapter()->getEntityManager()->getRepository('EMRDelegator\Model\Company')->find(1);
        $updatedName = "updatedcompanyname";
        $companyModel->setName($updatedName);
        $updatedModel = $this->dao->update($companyModel);
        $this->assertEquals($updatedModel->getName(), $updatedName);
    }

    /**
     * Test getList pulls back all companies in the database
     */
    public function testGetList()
    {
        $result = $this->dao->getList();

        $this->assertCount(5,$result);
        foreach($result as $companyModel){
            $this->assertInstanceOf('EMRDelegator\Model\Company',$companyModel);
        }

    }

}

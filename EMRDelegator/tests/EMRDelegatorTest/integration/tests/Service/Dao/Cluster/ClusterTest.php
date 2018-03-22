<?php

/**
 * @category WebPT
 * @package EMRDelegatorTest
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRDelegatorTest\integration\tests\Service\Dao\Cluster;

use Doctrine\ORM\Query;
use EMRCore\DoctrineConnector\Adapter\Adapter;
use EMRCore\DoctrineConnector\DoctrineConnectorFactory;
use EMRCore\PrototypeFactory;
use EMRCore\SqlConnector\SqlConnectorFactory;
use EMRCoreTest\lib\PhpUnit\Framework\DatabaseTestCase;
use EMRCoreTest\lib\SqlConnector\DefaultReaderWriter;
use EMRDelegator\Model\Cluster as ClusterModel;
use EMRDelegator\Service\Cluster\Dao\Cluster as ClusterDao;

class ClusterTest extends DatabaseTestCase
{
    /**
     * @var ClusterDao
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

    }
    
    /**
     * Drops test tables.
     */
    private function dropTables()
    {
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_cluster.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_company.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_facility.sql.php');
    }
    
    /**
     * Performs before class set up.
     */
    protected function insertRecords() {
        $this->executeSql(include __DIR__ . '/../../../../sql/common/insert_clusters.sql.php');
    }
    
    protected function tearDown() {
        $this->dropTables();
        parent::tearDown();
    }
    /**
     * Performs PhpUnit set up.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->dao = new ClusterDao;
        
        $this->dao->setDefaultReaderWriter($this->getMasterSlaveDoctrineAdapter());
        $this->dropTables();
        $this->createTables();
        $this->insertRecords();

    }

    /**
     * Tests cluster creation.
     */
    public function testClusterCreate()
    {
        /**
         * @var ClusterModel $newClusterCriteria
         */
        $criteria = new ClusterModel();
        $criteria->setName('mynewcluster');
        $criteria->setAcceptingNewCompanies(true);
        $criteria->setComment('mynewcluster, yay!');
        $criteria->setCurrentFacilityCount(100);
        $criteria->setMaxFacilityCount(500);
        $criteria->setOnlineStatus("online");

        /**
         * @var ClusterModel $newModel
         */
        $newModel = $this->dao->create($criteria);

        $this->assertEquals($newModel->getName(), 'mynewcluster');
    }

    /**
     * Tests cluster creation.
     */
    public function testClusterDelete()
    {
        /**
         * @var ClusterModel $criteria
         */
        $criteria = new ClusterModel();
        $criteria->setName('deletethiscluster');
        $criteria->setAcceptingNewCompanies(false);
        $criteria->setComment('deletethiscluster, boo!');
        $criteria->setCurrentFacilityCount(100);
        $criteria->setMaxFacilityCount(500);
        $criteria->setOnlineStatus("none");

        /**
         * @var ClusterModel $newModel
         */
        $newModel = $this->dao->create($criteria);
        $clusterModelToDelete = $this->dao->load($newModel->getClusterId());
        $this->assertEquals($clusterModelToDelete->getClusterId(), $newModel->getClusterId());

        $this->dao->delete($clusterModelToDelete);
        $clusterDeleted = $this->dao->load($newModel->getClusterId());
        $this->assertNull($clusterDeleted);
    }

    /**
     * Tests cluster load.
     */
    public function testClusterLoad()
    {
        /**
         * @var ClusterModel $clusterModel
         */
        $clusterModel = $this->getMasterSlaveDoctrineAdapter()->getEntityManager()->getRepository('EMRDelegator\Model\Cluster')->find(1);
        $this->assertEquals($clusterModel->getClusterId(), 1);
    }

    /**
     * Tests cluster load all.
     */
    public function testClusterLoadAll()
    {
        /**
         * @var ClusterModel[] $clusterModels
         */
        $clusterModels = $this->getMasterSlaveDoctrineAdapter()->getEntityManager()->getRepository('EMRDelegator\Model\Cluster')->findAll();

        $this->assertEquals(3, count($clusterModels));
    }

    /**
     * Tests cluster not found.
     */
    public function testClusterLoadNotFound()
    {
        /**
         * @var ClusterModel $clusterModel
         */
        $clusterModel = $this->getMasterSlaveDoctrineAdapter()->getEntityManager()->getRepository('EMRDelegator\Model\Cluster')->find(-1);
        $this->assertNull($clusterModel);
    }
    
    /**
     * Tests cluster update.
     */
    public function testClusterUpdate()
    {
        /**
         * @var ClusterModel $clusterModel
         */
        $clusterModel = $this->getMasterSlaveDoctrineAdapter()->getEntityManager()->getRepository('EMRDelegator\Model\Cluster')->find(1);
        $updatedName = "updatedclustername";
        $clusterModel->setName($updatedName);
        $updatedModel = $this->dao->update($clusterModel);
        $this->assertEquals($updatedModel->getName(), $updatedName);
    }

    public function testNotListClusterCompanyFacilityDueToClustersHaveNoCompanies()
    {
        $clusterModels = $this->dao->getListClusterCompanyFacility();
        $this->assertCount(0, $clusterModels);
    }

    public function testNotListClusterCompanyFacilityDueToClustersHaveNoFacilities()
    {
        $this->dropTables();
        $this->createTables();

        $this->executeSql('set foreign_key_checks = 0');
        $this->executeSql( include __DIR__ . '/../../../../sql/common/insert_clusters.sql.php');
        $this->executeSql( include __DIR__ . '/../../../../sql/common/insert_companies.sql.php');
        $this->executeSql('set foreign_key_checks = 1');

        $clusterModels = $this->dao->getListClusterCompanyFacility();
        $this->assertCount(0, $clusterModels);
    }

    public function testNotListClusterCompanyDueToClustersHaveNoCompanies()
    {
        $this->dropTables();
        $this->createTables();

        $clusterModels = $this->dao->getListClusterCompany();
        $this->assertCount(0, $clusterModels);
    }

    public function testListClusterCompanyFacilityDueToClustersHaveCompanies()
    {
        $this->dropTables();
        $this->createTables();

        $this->executeSql('set foreign_key_checks = 0');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/insert_clusters.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/insert_companies.sql.php');
        $this->executeSql('set foreign_key_checks = 1');

        $clusterModels = $this->dao->getListClusterCompany();
        $this->assertNotEmpty($clusterModels);
        $this->assertInstanceOf('EMRDelegator\Model\Cluster', $clusterModels[0]);
    }

    public function testListClusterCompanyFacilityDueToClustersHaveCompaniesHaveFacilities()
    {
        $this->dropTables();
        $this->createTables();

        $this->executeSql('set foreign_key_checks = 0');
        $this->executeSql( include __DIR__ . '/../../../../sql/common/insert_clusters.sql.php');
        $this->executeSql( include __DIR__ . '/../../../../sql/common/insert_companies.sql.php');
        $this->executeSql( include __DIR__ . '/../../../../sql/common/insert_facilities.sql.php');
        $this->executeSql('set foreign_key_checks = 1');

        $clusterModels = $this->dao->getListClusterCompanyFacility();
        $this->assertNotEmpty($clusterModels);
        $this->assertInstanceOf('EMRDelegator\Model\Cluster', $clusterModels[0]);
    }
}

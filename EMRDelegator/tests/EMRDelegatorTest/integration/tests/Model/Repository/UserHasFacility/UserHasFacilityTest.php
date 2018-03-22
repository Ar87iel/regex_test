<?php

/**
 * @category WebPT
 * @package EMRDelegatorTest
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRDelegatorTest\integration\tests\Repository\UserHasFacility;

use Doctrine\ORM\Query;
use EMRCore\DoctrineConnector\Adapter\Adapter;
use EMRCore\DoctrineConnector\DoctrineConnectorFactory;
use EMRCore\PrototypeFactory;
use EMRCore\SqlConnector\SqlConnectorFactory;
use EMRCoreTest\lib\PhpUnit\Framework\DatabaseTestCase;
use EMRCoreTest\lib\SqlConnector\DefaultReaderWriter;
use EMRDelegator\Model\Facility;
use EMRDelegator\Model\Repository\UserHasFacility;
use EMRDelegator\Service\UserHasFacility\Dao\UserHasFacility as UserHasFacilityDao;
use EMRDelegator\Service\UserHasFacility\Dto\SearchUserHasFacilityResult;
use EMRDelegator\Service\UserHasFacility\Dto\SearchUserHasFacilityResults;

class UserHasFacilityTest extends DatabaseTestCase
{
    /**
     * @var UserHasFacilityDao
     */
    private $dao;

    public  static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    /**
     * Creates test tables.
     */
    private static function createTables()
    {
        self::executeSql(include __DIR__ . '/../../../../sql/common/create_table_cluster.sql.php');
        self::executeSql(include __DIR__ . '/../../../../sql/common/create_table_company.sql.php');
        self::executeSql(include __DIR__ . '/../../../../sql/common/create_table_facility.sql.php');
        self::executeSql(include __DIR__ . '/../../../../sql/common/create_table_userhasfacility.sql.php');

    }

    /**
     * Drops test tables.
     */
    private static function dropTables()
    {
        self::executeSql(include __DIR__ . '/../../../../sql/common/drop_table_userhasfacility.sql.php');
        self::executeSql(include __DIR__ . '/../../../../sql/common/drop_table_facility.sql.php');
        self::executeSql(include __DIR__ . '/../../../../sql/common/drop_table_company.sql.php');
        self::executeSql(include __DIR__ . '/../../../../sql/common/drop_table_cluster.sql.php');

    }

    /**
     *
     */
    public static function setupForUserHasFacility()
    {
        self::dropTables();
        self::createTables();
        self::executeSql(include __DIR__ . '/sql/insert_clusters.sql.php');
        self::executeSql(include __DIR__ . '/sql/insert_companies.sql.php');
        self::executeSql(include __DIR__ . '/sql/insert_facilities.sql.php');
        self::executeSql(include __DIR__ . '/sql/insert_userhasfacilities.sql.php');
    }

    public static function setupForGetIdentityDefaultCompany()
    {
        self::dropTables();
        self::createTables();
        self::executeSql(include __DIR__ . '/../../../../sql/common/create_table_cluster.sql.php');
        self::executeSql(include __DIR__ . '/../../../../sql/common/create_table_company.sql.php');
        self::executeSql(include __DIR__ . '/../../../../sql/common/create_table_facility.sql.php');
        self::executeSql(include __DIR__ . '/../../../../sql/common/create_table_userhasfacility.sql.php');
        self::executeSql(include __DIR__ . '/sql/getIdentityDefaultCompany.sql.php');
    }

    /**
     * Performs after class tear down.
     */
    public static function tearDownAfterClass()
    {
        self::dropTables();

        parent::tearDownAfterClass();
    }

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

        /** @var DefaultReaderWriter $adapter */
        self::dropTables();
        self::createTables();

        $writerAdapter = $this->getWriterDoctrineAdapter();
        $writerAdapter->getEntityManager()->clear();

        $this->dao = new UserHasFacilityDao;
        $this->dao->setDefaultReaderWriter($writerAdapter);
    }

    /**
     * Tests that the result set only contains facilities that the userId has access to.
     */
    public function testGetsFacilityHasUserResults()
    {
        self::setupForUserHasFacility();

        $em = $this->getWriterDoctrineAdapter()->getEntityManager();

        /** @var UserHasFacility $repository */
        $repository = $em->getRepository('EMRDelegator\Model\UserHasFacility');

        /** @var SearchUserHasFacilityResults $results */
        $results = $repository->searchUserHasFacilityByIdentityId(1);

        $resultCollection = $results->getCollection();
        $this->assertCount(2, $resultCollection);

        $this->assertSame(4, $results->getDefaultFacilityId());

        /**
         * The first company and facilities in that company.
         * @var SearchUserHasFacilityResult $result
         */
        $result = $resultCollection->shift();

        $facilities = $result->getFacilities();
        $this->assertCount(3, $facilities);

        /** @var Facility $facility */
        $facility = $facilities->shift();

        $this->assertSame(1, $facility->getFacilityId());

        /**
         * The second company and facilities in that company.
         * @var SearchUserHasFacilityResult $result
         */
        $result = $resultCollection->shift();

        $facilities = $result->getFacilities();
        $this->assertCount(2, $facilities);

        /** @var Facility $facility */
        $facility = $facilities->shift();

        $this->assertSame(5, $facility->getFacilityId());
    }


    /**
     * @return UserHasFacility;
     */

    static protected function getUserHasFacilityRepository()
    {
        return self::getWriterDoctrineAdapter()
            ->getEntityManager()
            ->getRepository('EMRDelegator\Model\UserHasFacility');
    }


    public function testGetIdentityDefaultCompanyReturnsNullOnNoMatchingRecords()
    {
        self::setupForGetIdentityDefaultCompany();
        $this->assertNull(self::getUserHasFacilityRepository()->getIdentityDefaultCompany(1));
    }

    public function testGetIdentityDefaultCompanyReturnsCompanyOfDefaultFacility()
    {
        self::setupForGetIdentityDefaultCompany();
        $companyModel = self::getUserHasFacilityRepository()->getIdentityDefaultCompany(11);
        $this->assertNotEmpty($companyModel);
        $this->assertEquals(2, $companyModel->getCompanyId());

    }

    public function testGetIdentityDefaultCompanyReturnsACompanyAssociatedToIdentity()
    {
        self::setupForGetIdentityDefaultCompany();
        $companyModel = self::getUserHasFacilityRepository()->getIdentityDefaultCompany(10);
        $this->assertNotEmpty($companyModel);
        $this->assertContains($companyModel->getCompanyId(), array( 1, 2, 3 ));

    }

    public function testGetCompanyByIdentityIdAndFacilityIdReturnsNull() {
        self::setupForGetIdentityDefaultCompany();
        $this->assertNull(self::getUserHasFacilityRepository()->getCompanyByIdentityIdAndFacilityId(999,999));
    }

    public function testGetCompanyByIdentityIdAndFacilityIdReturnsCompanyModel() {
        $identityId = 10;
        $facilityId = 3;
        $companyId = 3;
        self::setupForGetIdentityDefaultCompany();
        $companyModel = self::getUserHasFacilityRepository()->getCompanyByIdentityIdAndFacilityId($identityId, $facilityId);
        $this->assertNotEmpty($companyModel);
        $this->assertEquals($companyId, $companyModel->getCompanyId());
    }

}

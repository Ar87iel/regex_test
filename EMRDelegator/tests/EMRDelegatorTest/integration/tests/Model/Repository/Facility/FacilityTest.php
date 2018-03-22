<?php
namespace EMRDelegatorTest\integration\tests\Model\Repository\Facility;

use EMRCore\DoctrineConnector\Adapter\Adapter;
use EMRCore\DoctrineConnector\DoctrineConnectorFactory;
use EMRCore\PrototypeFactory;
use EMRCoreTest\lib\SqlConnector\DefaultReaderWriter;
use EMRDelegator\Model\Company as CompanyModel;
use EMRDelegator\Model\Repository\Facility;
use PHPUnit_Framework_TestCase;

class FacilityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultReaderWriter
     */
    private static $adapter;

    /**
     * The adapter we are using.
     * @var Adapter
     */
    private static $defaultReaderWriter;

    /**
     * @var Facility
     */
    private $dao;

    /**
     * Creates test tables.
     */
    private static function createTables()
    {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/../../../../sql/common/create_table_cluster.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/create_table_company.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/create_table_facility.sql.php');
    }

    /**
     *
     */
    private static function reCreateTables()
    {
        self::dropTables();
        self::createTables();
    }

    /**
     * Drops test tables.
     */
    private static function dropTables()
    {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/../../../../sql/common/drop_table_facility.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/drop_table_company.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/drop_table_cluster.sql.php');
    }

    /**
     * Performs before class set up.
     */
    public static function setUpBeforeClass()
    {
        /** @var DefaultReaderWriter $adapter */
        $adapter = PrototypeFactory::get( 'EMRCoreTest\lib\SqlConnector\DefaultReaderWriter' );
        self::$adapter = $adapter;

        self::$defaultReaderWriter = DoctrineConnectorFactory::get('default_reader_writer');
    }

    /**
     * Performs after class tear down.
     */
    public static function tearDownAfterClass()
    {
        self::dropTables();
    }

    /**
     * Performs PhpUnit set up.
     */
    protected function setUp()
    {
        self::reCreateTables();

        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/../../../../sql/common/insert_clusters.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/insert_companies.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/insert_facilities.sql.php');

        $entityManager = self::$defaultReaderWriter->getEntityManager();
        $this->dao = $entityManager->getRepository('EMRDelegator\Model\Facility');
    }

    public function testNotGetsFacilityCountDueToInvalidCompany()
    {
        $company = new CompanyModel();
        $this->assertSame(0, $this->dao->getFacilityCountByCompany($company));
    }

    public function testGetsFacilityCount()
    {
        $company = new CompanyModel();

        $company->setCompanyId(1);
        $this->assertSame(1, $this->dao->getFacilityCountByCompany($company));

        $company->setCompanyId(2);
        $this->assertSame(1, $this->dao->getFacilityCountByCompany($company));

        $company->setCompanyId(3);
        $this->assertSame(3, $this->dao->getFacilityCountByCompany($company));
    }
}
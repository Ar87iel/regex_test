<?php
namespace EMRDelegatorTest\integration\tests\Model\Repository\Announcement;

use EMRCore\DoctrineConnector\Adapter\Adapter;
use EMRCore\DoctrineConnector\DoctrineConnectorFactory;
use EMRCore\PrototypeFactory;
use EMRCoreTest\lib\SqlConnector\DefaultReaderWriter;
use EMRDelegator\Model\Repository\Announcement;
use PHPUnit_Framework_TestCase;

class AnnouncementTest extends PHPUnit_Framework_TestCase
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
     * @var Announcement
     */
    private $repository;

    /**
     * Creates test tables.
     */
    private static function createTables()
    {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/../../../../sql/common/create_table_announcement.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/create_table_cluster.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/create_table_company.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/create_table_facility.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/create_table_userhasfacility.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/create_table_FacilityHasAnnouncement.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/create_table_IdentityHasAnnouncement.sql.php');
    }

    /**
     * Drops test tables.
     */
    private static function dropTables()
    {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/../../../../sql/common/drop_table_announcement.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/drop_table_cluster.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/drop_table_company.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/drop_table_facility.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/drop_table_userhasfacility.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/drop_table_FacilityHasAnnouncement.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/drop_table_IdentityHasAnnouncement.sql.php');
    }

    private static function insertRecords()
    {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/../../../../sql/common/insert_announcements.sql.php');
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

        self::dropTables();
        self::createTables();
        self::insertRecords();
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
        $entityManager = self::$defaultReaderWriter->getEntityManager();
        $this->repository = $entityManager->getRepository('EMRDelegator\Model\Announcement');
    }

    public function testGetsAllActiveWhenNeverDelegated()
    {
        $lastDelegation = null; //gmdate('Y-m-d H:i:s', strtotime());
        $this->assertCount(1, $this->repository->getOutstandingAnnouncements($lastDelegation,0));
    }

    public function testGetsAllActiveWhenLastLoginWasBeforeAll()
    {
        $lastDelegation = gmdate('Y-m-d H:i:s', strtotime('-6 days'));
        $this->assertCount(1, $this->repository->getOutstandingAnnouncements($lastDelegation,0));
    }

    public function testGetsOnlyActiveAnnouncmentNotSeen()
    {
        $lastDelegation = gmdate('Y-m-d H:i:s', strtotime('-3 days'));
        $this->assertCount(1, $this->repository->getOutstandingAnnouncements($lastDelegation,0));
    }

    public function testGetsNoneWhenAllSeen()
    {
        $lastDelegation = gmdate('Y-m-d H:i:s', strtotime('now'));
        $this->assertCount(0, $this->repository->getOutstandingAnnouncements($lastDelegation,0));
    }
   
}
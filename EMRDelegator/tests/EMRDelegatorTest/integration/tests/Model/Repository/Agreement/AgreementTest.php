<?php
namespace EMRDelegatorTest\integration\tests\Model\Repository\Announcement;

use EMRCore\DoctrineConnector\Adapter\Adapter;
use EMRCore\DoctrineConnector\DoctrineConnectorFactory;
use EMRCore\PrototypeFactory;
use EMRCoreTest\lib\SqlConnector\DefaultReaderWriter;
use EMRDelegator\Model\Repository\Agreement;
use PHPUnit_Framework_TestCase;

class AgreementTest extends PHPUnit_Framework_TestCase
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
     * @var Agreement
     */
    private $repository;

    /**
     * Creates test tables.
     */
    private static function createTables()
    {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/../../../../sql/common/create_table_agreementtype.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/create_table_agreement.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/create_table_userhasagreement.sql.php');
    }

    /**
     * Drops test tables.
     */
    private static function dropTables()
    {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/../../../../sql/common/drop_table_agreement.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/drop_table_agreementtype.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/drop_table_userhasagreement.sql.php');
    }

    private static function insertRecords()
    {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/sql/insert_test_data.sql.php');
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
        $this->repository = $entityManager->getRepository('EMRDelegator\Model\Agreement');
    }

    public function testNewUserGetsLatestVersionOfFirstAgreement()
    {
        $result = $this->repository->getHighestPriorityOutstanding(1);
        $this->assertEquals(10, $result->getAgreementId());
    }

    public function testUserGetsSecondAgreementIfFirstSigned()
    {
        $result = $this->repository->getHighestPriorityOutstanding(100);
        $this->assertEquals(2, $result->getAgreementId());
    }

    public function testUserGetsThirdAgreementIfSecondSigned()
    {
        $result = $this->repository->getHighestPriorityOutstanding(200);
        $this->assertEquals(3, $result->getAgreementId());
    }

    public function testUserDoesNotGetFutureVersionOfAgreemrnt()
    {
        $result = $this->repository->getHighestPriorityOutstanding(1);
        $this->assertNotEquals(20, $result->getAgreementId());
    }
}
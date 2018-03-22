<?php
/**
 * @category WebPT 
 * @package EMRDelegator
 * @author: kevinkucera
 * 10/12/13 4:15 PM
 */

use EMRCoreTest\lib\PhpUnit\Framework\DatabaseTestCase;
use EMRDelegator\Service\Agreement\Dao\Agreement as AgreementDao;
use EMRCore\DoctrineConnector\Adapter\Adapter;

class AgreementTest extends DatabaseTestCase
{
    /**
     * The adapter we are using.
     * @var Adapter
     */
    private static $defaultReaderWriter;

    /**
     * @var AgreementDao
     */
    private $dao;

    /**
     * Creates test tables.
     */
    private static function createTables()
    {
        self::executeSql(include __DIR__ . '/../../../../sql/common/create_table_agreement.sql.php');
        self::executeSql(include __DIR__ . '/../../../../sql/common/create_table_agreementtype.sql.php');
    }

    /**
     * Drops test tables.
     */
    private static function dropTables()
    {
        self::executeSql(include __DIR__ . '/../../../../sql/common/drop_table_agreement.sql.php');
        self::executeSql(include __DIR__ . '/../../../../sql/common/drop_table_agreementtype.sql.php');
    }

    /**
     * Performs before class set up.
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$defaultReaderWriter = self::getWriterDoctrineAdapter();
    }

    /**
     * Performs after class tear down.
     */
    public static function tearDownAfterClass()
    {
        self::$defaultReaderWriter->getCacheDriver()->deleteAll();
        self::dropTables();

        parent::tearDownAfterClass();
    }

    /**
     * Performs PhpUnit set up.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->dao = new AgreementDao;

        $this->dao->setDefaultReaderWriter(self::$defaultReaderWriter);

        self::dropTables();
        self::createTables();

        self::executeSql(include __DIR__ . '/../../../Model/Repository/Agreement/sql/insert_test_data.sql.php');
    }

    public function testGetLatestAgreementByType()
    {
        $agreement = $this->dao->getLatestAgreementByType('TYPE1');
        $this->assertEquals(10, $agreement->getAgreementId());
    }

    public function testGetLastestAgreementReturnsNullWhenNotFoundd()
    {
        $agreement = $this->dao->getLatestAgreementByType('DOES NOT EXIST');
        $this->assertNull($agreement);
    }
}
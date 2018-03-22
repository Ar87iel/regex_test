<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 5/9/13 4:37 PM
 */

use Console\Etl\Service\Dao\SelectAndInsertInterface;
use Console\Etl\Service\Dto\InsertStatementResult;
use EMRCore\PrototypeFactory;
use EMRCoreTest\lib\SqlConnector\DefaultReaderWriter;

abstract class SelectAndInsertTest extends PHPUnit_Framework_TestCase {

    /** @var  DefaultReaderWriter */
    protected static $adapter;
    /** @var  SelectAndInsertInterface */
    protected $dao;

    /**
     * Should create tables needed for test
     */
    abstract protected function createTables();

    /**
     * Should drop any tables that were created for test
     */
    abstract protected function dropTables();

    /**
     * Should insert records into test schema for source & dest table
     */
    abstract protected function insertRecords();
    /**
     * This method should return a valid insert statement for the destination table. It
     * should match the data that will be read from the inserted records for the test.
     * @return string
     */
    abstract protected function getInsertStatementTestValue();

    /**
     * Return an instantiated dao w/ its reader & writer adapters dependencies supplied
     * @return SelectAndInsertInterface
     */
    abstract protected function getDao();

    public static function setUpBeforeClass()
    {
        /** @var DefaultReaderWriter $adapter */
        self::$adapter = PrototypeFactory::get('EMRCoreTest\lib\SqlConnector\DefaultReaderWriter');
    }

    protected function setUp() {
        $this->dropTables();
        $this->createTables();

        self::$adapter->getDatabase()->setLogger($this->getMock('Logger', array(), array(), '', false));

        $this->dao = $this->getDao();
    }

    public function testSelectSourceRecordsAsInsertStatementsReturnsEmptyIterable() {
        $result = $this->dao->selectSourceRecordsAsInsertStatements();
        $this->assertNotNull($result);
        $this->assertInstanceOf('Zend\Db\ResultSet\HydratingResultSet', $result);
        $this->assertEquals(0, $result->count());
    }

    public function testSelectSourceRecordsAsInsertStatementsReturnsIterableSqlDtos() {
        $this->insertRecords();
        $sql = $this->getInsertStatementTestValue();

        $result = $this->dao->selectSourceRecordsAsInsertStatements();
        $this->assertNotNull($result);
        $this->assertInstanceOf('Zend\Db\ResultSet\HydratingResultSet', $result);
        $this->assertEquals(1, $result->count());
        /** @var InsertStatementResult $dto */
        $dto = $result->current();
        $this->assertNotEmpty($dto->getSql());
        $this->assertEquals($sql, $dto->getSql());
    }

    public function testGetSourceRecordCountReturnsZero() {
        $result = $this->dao->getSourceRecordCount();
        $this->assertEquals(0, $result);
    }

    public function testGetSourceRecordCountReturnsCount() {
        $this->insertRecords();
        $result = $this->dao->getSourceRecordCount();
        $this->assertEquals(1, $result);
    }

    public function testGetDestinationRecordCountReturnsZero() {
        $result = $this->dao->getDestinationRecordCount();
        $this->assertEquals(0, $result);
    }

    public function testGetDestinationRecordCountReturnsCount() {
        $this->insertRecords();
        $result = $this->dao->getDestinationRecordCount();
        $this->assertEquals(1, $result);
    }

    public function testExecuteInsertStatementInsertsRecord() {
        $sql = $this->getInsertStatementTestValue();
        $preCount = $this->dao->getSourceRecordCount();
        $this->dao->executeInsertStatement($sql);
        $postCount = $this->dao->getDestinationRecordCount();
        $this->assertGreaterThan($preCount, $postCount);
        $this->assertEquals(1, $postCount-$preCount);
    }

    public function testTruncateDestinationTable() {
        $this->insertRecords();
        $preCount = $this->dao->getDestinationRecordCount();
        $this->assertGreaterThan(0, $preCount);
        $this->dao->truncateDestinationTable();
        $postCount = $this->dao->getDestinationRecordCount();
        $this->assertEquals(0, $postCount);
    }

}

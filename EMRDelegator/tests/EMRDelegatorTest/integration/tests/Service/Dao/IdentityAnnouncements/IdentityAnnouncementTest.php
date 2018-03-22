<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 5/14/13 5:25 PM
 */
use EMRCoreTest\Lib\TestCase\Dao\Doctrine;
use EMRDelegator\Service\IdentityAnnouncements\Dao\IdentityAnnouncements as IdentityAnnouncementsDao;

/**
 * Class IdentityAnnouncementsTest
 * @property $dao IdentityAnnouncementsDao
 */
class IdentityAnnouncementsTest extends Doctrine {
    /**
     * Return an instance to the dao you are testing
     * @return IdentityAnnouncementsDao
     */
    protected function getDaoInstance() {
        $dao = new IdentityAnnouncementsDao();
        $dao->setDefaultReaderWriter(self::$doctrineAdapter);
        return $dao;
    }

    protected function setUp() {
        parent::setUp();
        $this->dropTables();
        $this->createTables();
        $this->insertRecords();
    }

    protected function tearDown() {
        $this->dropTables();
        parent::tearDown();
    }

    public function testGetIdentityLastDelegationTimeReturnsNull() {
        $result = $this->getDaoInstance()->getIdentityLastAcknowledgedTime(2);
        $this->assertNull($result);
    }

    public function testGetIdentityLastDelegationTimeReturnsDateTime() {
        $result = $this->getDaoInstance()->getIdentityLastAcknowledgedTime(1);
        $this->assertInstanceOf('DateTime', $result);
        $this->assertEquals('2013-05-13 00:00:00', $result->format('Y-m-d H:i:s'));
    }

    private function dropTables()
    {
        $this->executeSqlScript(__DIR__ . '/sql/drop_tables.sql.php');
    }

    private function createTables()
    {
        $this->executeSqlScript(__DIR__ . '/sql/create_tables.sql.php');
    }

    private function insertRecords()
    {
        $this->executeSqlScript(__DIR__ . '/sql/insert_records.sql.php');
    }
}

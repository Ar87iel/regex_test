<?php

/**
 * @category WebPT
 * @package EMRAdminTest
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRAdminTest\integration\tests\Repository\CompanyMigration;

use EMRCore\DoctrineConnector\Adapter\Adapter;
use EMRCore\DoctrineConnector\DoctrineConnectorFactory;
use EMRCore\PrototypeFactory;
use EMRCoreTest\lib\SqlConnector\DefaultReaderWriter;
use PHPUnit_Framework_TestCase;
use EMRAdmin\Model\CompanyMigration as CompanyMigrationModel;
use EMRAdmin\Service\CompanyMigration\Dao\CompanyMigration as CompanyMigrationDao;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Query;
use Zend\Filter\Int;

class CompanyMigrationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var CompanyMigrationDao
     */
    private $dao;


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
     * @param Adapter $adapter
     * @return CompanyMigrationModel
     */
    public function setDefaultReaderWriter(Adapter $adapter)
    {
        $this->defaultReaderWriter = $adapter;
        return $this;
    }

    /**
     * @return CompanyMigrationModel
     */

    static protected function getCompanyMigrationRepository()
    {
        return self::$defaultReaderWriter
            ->getEntityManager()
            ->getRepository('EMRAdmin\Model\CompanyMigration');
    }



    /**
     * Creates test tables.
     */
    private static function createTables()
    {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/../../../../sql/common/create_table_company-migration.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/create_table_company-migration-event.sql.php');


    }

    /**
     * Drops test tables.
     */
    private static function dropTables()
    {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/../../../../sql/common/drop_table_company-migration.sql.php');
        $db->execute(include __DIR__ . '/../../../../sql/common/drop_table_company-migration-event.sql.php');

    }


    public static function setUpBeforeClass()
    {
        /** @var DefaultReaderWriter $adapter */
        $adapter = PrototypeFactory::get('EMRCoreTest\lib\SqlConnector\DefaultReaderWriter');
        self::$adapter = $adapter;

        self::dropTables();
        self::createTables();
        $db = self::$adapter->getDatabase();

        $db->execute(include __DIR__ . '/sql/insert_records.sql.php');


        self::$defaultReaderWriter = DoctrineConnectorFactory::get('default_reader_writer');
    }


    public static function tearDownAfterClass()
    {
        self::dropTables();
    }


    protected function setUp()
    {
        $this->dao = new CompanyMigrationDao();
        $this->dao->setDefaultReaderWriter(self::$defaultReaderWriter);
    }


    public function testGetCompanyMigrationByCompanyId()
    {
        $companyId = 456;
        $migrationId = 5;
        $createdDateTime = '2013-06-02';

        $result = $this->dao->getCompanyMigrationByCompanyId($companyId);

        $this->assertInstanceOf('\EMRAdmin\Model\CompanyMigration', $result);
        $this->assertEquals($migrationId, $result->getMigrationId());
        $this->assertEquals($createdDateTime, $result->getCreatedDateTime()->format('Y-m-d'));
    }

}
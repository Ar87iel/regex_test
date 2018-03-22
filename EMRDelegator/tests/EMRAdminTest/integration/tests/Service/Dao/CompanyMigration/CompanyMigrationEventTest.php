<?php

/**
 * @category WebPT
 * @package EMRAdminTest
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRAdminTest\integration\tests\Service\Dao\CompanyMigration;

use DateTime;
use DateTimeZone;
use Doctrine\ORM\Query;
use EMRCore\DoctrineConnector\Adapter\Adapter;
use EMRCore\DoctrineConnector\DoctrineConnectorFactory;
use EMRCore\PrototypeFactory;
use EMRCore\SqlConnector\SqlConnectorFactory;
use EMRCoreTest\lib\SqlConnector\DefaultReaderWriter;
use EMRAdmin\Model\CompanyMigration as CompanyMigrationModel;
use EMRAdmin\Model\CompanyMigrationEvent as CompanyMigrationEventModel;
use EMRAdmin\Service\CompanyMigration\Dao\CompanyMigrationEvent as CompanyMigrationEventDao;
use EMRAdmin\Service\CompanyMigration\Dao\CompanyMigration as CompanyMigrationDao;



class CompanyMigrationEventTest extends \PHPUnit_Framework_TestCase
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
     * @var CompanyMigrationEventDao
     */
    private $eventDao;

    /**
     * @var CompanyMigrationDao
     */
    private $migrationDao;

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

    /**
     * Performs before class set up.
     */
    public static function setUpBeforeClass()
    {
        $adapter = PrototypeFactory::get( 'EMRCoreTest\lib\SqlConnector\DefaultReaderWriter' );
        self::$adapter = $adapter;

        self::$defaultReaderWriter = DoctrineConnectorFactory::get('default_reader_writer');

        self::dropTables();
        self::createTables();

        /** @var  DefaultReaderWriter $db */
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/sql/insert_records.sql.php');
    }

    /**
     * Performs after class tear down.
     */
    public static function tearDownAfterClass()
    {
        self::$defaultReaderWriter->getCacheDriver()->deleteAll();
        self::dropTables();
    }

    /**
     * Performs PhpUnit set up.
     */
    protected function setUp()
    {
        $this->eventDao = new CompanyMigrationEventDao();
        $this->eventDao->setDefaultReaderWriter(self::$defaultReaderWriter);

        $this->migrationDao = new CompanyMigrationDao();
        $this->migrationDao->setDefaultReaderWriter(self::$defaultReaderWriter);

    }


    public function testCreateMigrationEventCreatesMigration(){
        $identityId = 55;
        $companyId = 456;

        $eventModel = new CompanyMigrationEventModel();
        $eventModel->setEvent('foo');
        $message = '';
        for($i=0; $i<640010; $i++){ $message.='x'; }
        $eventModel->setMessage($message);

        $migrationModel = $this->migrationDao->load(3);
        $eventModel->setMigration($migrationModel);

        /** @var CompanyMigrationEventModel $result */
        $result = $this->eventDao->create($eventModel);

        $this->assertEquals($companyId, $result->getMigration()->getCompanyId());
        $this->assertEquals($identityId, $result->getMigration()->getIdentityId());
        $this->assertInstanceOf('EMRAdmin\Model\CompanyMigrationEvent',$result);

    }



}

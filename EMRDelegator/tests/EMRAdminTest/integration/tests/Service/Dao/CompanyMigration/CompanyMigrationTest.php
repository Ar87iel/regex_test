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
use EMRAdmin\Service\CompanyMigration\Dao\CompanyMigration as CompanyMigrationDao;

class CompanyMigrationTest extends \PHPUnit_Framework_TestCase
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
     * @var CompanyMigrationDao
     */
    private $dao;

    /**
     * Creates test tables.
     */
    private static function createTables()
    {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/../../../../sql/common/create_table_company-migration.sql.php');

    }

    /**
     * Drops test tables.
     */
    private static function dropTables()
    {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/../../../../sql/common/drop_table_company-migration.sql.php');
    }

    /**
     * Performs before class set up.
     */
    public static function setUpBeforeClass()
    {
        $adapter = PrototypeFactory::get( 'EMRCoreTest\lib\SqlConnector\DefaultReaderWriter' );
        self::$adapter = $adapter;

        self::$defaultReaderWriter = DoctrineConnectorFactory::get('default_reader_writer');

        //self::dropTables();
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
        //self::dropTables();
    }

    /**
     * Performs PhpUnit set up.
     */
    protected function setUp()
    {
        $this->dao = new CompanyMigrationDao;
        $this->dao->setDefaultReaderWriter(self::$defaultReaderWriter);
    }


    public function testCreateCompanyMigration()
    {
        $migrationId = 1;
        $identityId = 10;
        $companyId = 3;
        $destinationClusterId = 2;
        $createdDateTime = new DateTime('now',new DateTimeZone('UTC'));
        $completedDateTime = new DateTime('now',new DateTimeZone('UTC'));
        $completedState = 'success';

        $model = new CompanyMigrationModel();
        $model->setMigrationId($migrationId);
        $model->setIdentityId($identityId);
        $model->setCompanyId($companyId);
        $model->setDestinationClusterId($destinationClusterId);
        $model->setCreatedDateTime($createdDateTime);
        $model->setCompletedDateTime($completedDateTime);
        $model->setCompletedState($completedState);

        /** @var CompanyMigrationModel $result */
        $result = $this->dao->create($model);

        $this->assertNotEmpty($result);
        $this->assertInstanceOf('\EMRAdmin\Model\CompanyMigration',$model);
        $this->assertEquals($result->getCompletedState(), $completedState);

    }

    public function testLoadCompanyMigration()
    {
        $model = new CompanyMigrationModel();
        $migrationId = 3;

        /** @var CompanyMigrationModel $result */
        $result = $this->dao->load($migrationId);

        $this->assertInstanceOf('\EMRAdmin\Model\CompanyMigration',$result);
        $this->assertEquals($migrationId, $result->getMigrationId());
    }

    public function testLoadThenUpdateCompanyMigration()
    {
        $migrationId = 3;
        $priorState = 'success';
        $newState = 'failed';

        /** @var CompanyMigrationModel $model */
        $model = $this->dao->load($migrationId);

        // prior state
        $this->assertEquals($priorState,$model->getCompletedState());

        $model->setCompletedState($newState);

        $this->dao->update($model);

        // new state
        $this->assertEquals($newState,$model->getCompletedState());
    }

}
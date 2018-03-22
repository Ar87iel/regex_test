<?php
namespace EMRAdminTest\integration\tests\Service\Dao\Session;

use EMRCore\DaoFactory;
use EMRCore\DoctrineConnector\Adapter\Adapter;
use EMRCore\DoctrineConnector\DoctrineConnectorFactory;
use EMRCore\PrototypeFactory;
use EMRCoreTest\lib\SqlConnector\DefaultReaderWriter;
use EMRAdmin\Service\Session\Dao\Registry as RegistryDao;
use EMRAdmin\Model\SessionRegistry as SessionRegistryModel;
use PHPUnit_Framework_TestCase;

class RegistryTest extends PHPUnit_Framework_TestCase
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
     * @var RegistryDao
     */
    private $dao;


    /**
     * Creates test tables.
     */
    private static function createTables()
    {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/../../../../sql/common/create_table_sessionregistry.sql.php');

    }

    /**
     * Drops test tables.
     */
    private static function dropTables()
    {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/../../../../sql/common/drop_table_sessionregistry.sql.php');

    }

    private static function insertRecords()
    {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/sql/insert_test_data.sql.php');
    }

    /**
     * Return an instance to the dao you are testing
     * @return RegistryDao
     */
    protected function getDaoInstance() {
        $dao = new RegistryDao();
        $dao->setDefaultReaderWriter(self::$doctrineAdapter);
        return $dao;
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
        $this->dao = new RegistryDao();
        $this->dao->setDefaultReaderWriter(self::$defaultReaderWriter);
    }

    /**
     *
     */
    public function testGetBySsoToken(){
        $ssoToken = 'hijklmn456';

        /** @var SessionRegistryModel $model */
        $model = $this->dao->getBySsoToken($ssoToken);

        $this->assertInstanceOf('\EMRAdmin\Model\SessionRegistry',$model);
        $this->assertEquals($ssoToken,$model->getSsoToken());
    }

    public function testGetByIdentityId(){
        $identityId = '27';

        /** @var SessionRegistryModel $model */
        $model = $this->dao->getByIdentityId($identityId);
        $this->assertInstanceOf('\EMRAdmin\Model\SessionRegistry',$model);
        $this->assertEquals($identityId,$model->getIdentityId());

    }



}
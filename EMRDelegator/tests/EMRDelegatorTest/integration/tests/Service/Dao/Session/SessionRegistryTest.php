<?php
namespace EMRDelegatorTest\integration\tests\Service\Dao\Session;

use EMRCore\DaoFactory;
use EMRCore\DoctrineConnector\Adapter\Adapter;
use EMRCore\DoctrineConnector\DoctrineConnectorFactory;
use EMRCore\PrototypeFactory;
use EMRCoreTest\lib\PhpUnit\Framework\DatabaseTestCase;
use EMRCoreTest\lib\SqlConnector\DefaultReaderWriter;
use EMRDelegator\Service\Session\Dao\Registry as RegistryDao;
use EMRDelegator\Model\SessionRegistry as SessionRegistryModel;
use PHPUnit_Framework_TestCase;

class RegistryTest extends DatabaseTestCase
{
    /**
     * @var RegistryDao
     */
    private $dao;


    /**
     * Creates test tables.
     */
    private function createTables()
    {
        $this->executeSql(include __DIR__ . '/../../../../sql/common/create_table_sessionregistry.sql.php');
    }

    /**
     * Drops test tables.
     */
    private function dropTables()
    {
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_sessionregistry.sql.php');
    }

    private function insertRecords()
    {
        $this->executeSql(include __DIR__ . '/sql/insert_test_data.sql.php');
    }

    /**
     * Return an instance to the dao you are testing
     * @return RegistryDao
     */
    protected function getDaoInstance() {
        $dao = new RegistryDao();
        $dao->setDefaultReaderWriter($this->getMasterSlaveDoctrineAdapter());
        return $dao;
    }


    /**
     * Performs PhpUnit set up.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->dao = new RegistryDao();
        $this->dao->setDefaultReaderWriter($this->getMasterSlaveDoctrineAdapter());
        $this->dropTables();
        $this->createTables();
        $this->insertRecords();
    }

    /**
     *
     */
    public function testGetBySsoToken(){
        $ssoToken = 'hijklmn456';

        /** @var SessionRegistryModel $model */
        $model = $this->dao->getBySsoToken($ssoToken);

        $this->assertInstanceOf('\EMRDelegator\Model\SessionRegistry',$model);
        $this->assertEquals($ssoToken,$model->getSsoToken());
    }

    public function testGetByIdentityId(){
        $identityId = '27';

        /** @var SessionRegistryModel $model */
        $model = $this->dao->getByIdentityId($identityId);
        $this->assertInstanceOf('\EMRDelegator\Model\SessionRegistry',$model);
        $this->assertEquals($identityId,$model->getIdentityId());

    }



}
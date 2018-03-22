<?php
namespace EMRDelegatorTest\integration\tests\Service\Dao\UserHasAgreement;

use EMRCore\DoctrineConnector\DoctrineConnectorFactory;
use EMRCore\PrototypeFactory;
use EMRCoreTest\lib\PhpUnit\Framework\DatabaseTestCase;
use EMRCoreTest\lib\SqlConnector\DefaultReaderWriter;
use EMRDelegator\Service\Agreement\Dao\Agreement as AgreementDao;
use EMRDelegator\Service\UserHasAgreement\Dao\UserHasAgreement as UserHasAgreementDao;
use EMRDelegator\Model\UserHasAgreement as UserHasAgreementModel;
use PHPUnit_Framework_TestCase;

class UserHasAgreementTest extends DatabaseTestCase
{
    /**
     * @var UserHasAgreementDao
     */
    private $dao;

    /**
     * @var AgreementDao
     */
    private $agreementDao;

    /**
     * Creates test tables.
     */
    private function createTables()
    {
        $this->executeSql(include __DIR__ . '/../../../../sql/common/create_table_agreementtype.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/create_table_agreement.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/create_table_userhasagreement.sql.php');
    }

    /**
     * Drops test tables.
     */
    private function dropTables()
    {
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_agreement.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_agreementtype.sql.php');
        $this->executeSql(include __DIR__ . '/../../../../sql/common/drop_table_userhasagreement.sql.php');
    }

    private function insertRecords()
    {
        $this->executeSql(include __DIR__ . '/sql/insert_test_data.sql.php');
    }

    /**
     * Performs PhpUnit set up.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->dao = new UserHasAgreementDao();
        $this->dao->setDefaultReaderWriter($this->getMasterSlaveDoctrineAdapter());
        $this->agreementDao = new AgreementDao();
        $this->agreementDao->setDefaultReaderWriter($this->getMasterSlaveDoctrineAdapter());

        $this->dropTables();
        $this->createTables();
        $this->insertRecords();
    }

    public function testCreate()
    {
        $agreement = $this->agreementDao->load(1);

        $model = new UserHasAgreementModel();
        $model->setIdentityId(1);
        $model->setAgreement($agreement);
        $model->setRemoteAddress('blah');

        $savedModel = $this->dao->create($model);
        $this->assertGreaterThan(0,$savedModel->getRecordId());

    }

    public function testGetByIdentityIdAgreementId()
    {
        $identityId = 100;
        $agreementId = 3;

        $agreement = $this->agreementDao->load(3);

        $model = new UserHasAgreementModel();
        $model->setIdentityId($identityId);
        $model->setAgreement($agreement);
        $model->setRemoteAddress('blah');
        /** @var UserHasAgreementModel $record */
        $record = $this->dao->create($model);
        $expectedId = $record->getRecordId();

        $result = $this->dao->getByIdentityIdAgreementId($identityId, $agreementId);
        $this->assertEquals($expectedId, $result->getRecordId());

    }

    /**
     * Tests output to function 'getBulkUserHasAgreement'.
     */
    public function testGetBulkUserHasAgreement()
    {
        $identityId = [1, 2, 3];

        $result = $this->dao->getBulkUserHasAgreement($identityId);

        self::assertCount(2, $result, 'The size of result is not expected.');
    }
}
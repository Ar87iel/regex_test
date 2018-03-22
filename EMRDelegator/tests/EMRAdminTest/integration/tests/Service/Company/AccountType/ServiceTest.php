<?php
/**
 * Created by PhpStorm.
 * User: bensaunders
 * Date: 10/14/14
 * Time: 10:32 AM
 */

namespace EMRAdminTest\integration\tests\Service\Company\AccountType;

use EMRAdmin\Service\Company\AccountType\Dao\Doctrine;
use EMRModel\Company\CompanyAccountType;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Doctrine\ORM\Tools\SchemaTool;
use EMRCore\DoctrineConnector\Adapter\Adapter;
use EMRCore\DoctrineConnector\DoctrineConnectorFactory;
use EMRCore\Session\Instance\Application as ApplicationInstance;
use EMRCore\Zend\ServiceManager\Factory as ServiceManagerFactory;
use EMRCore\Zend\ServiceManager\ServiceManager;
use EMRModel\Company\Company;
use EMRModel\Facility\Facility;
use EMRModel\Facility\FacilityHasUser;
use EMRModel\Facility\Module;
use EMRModel\User\User;
use EMRModel\User\UserType;
use Zend\Config\Config;
use Zend\Console\Console;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager as ZendServiceManager;
use Zend\Stdlib\Parameters;
use Zend\Uri\Http as HttpUri;
use EMRAdmin\Service\Company\AccountType\Service as CompanyAccountTypeService;

class ServiceTest extends PHPUnit_Framework_TestCase {

    /** @var CompanyAccountTypeService */
    protected $service;

    /** @var  Adapter */
    private static $adapter;

    /** @var  SchemaTool */
    private static $schemaTool;

    /** @var  string */
    private static $databaseName;


    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        DoctrineConnectorFactory::reset();

        self::$adapter = $adapter = DoctrineConnectorFactory::get('master_master_slave');

        self::$databaseName = $database = 'test_' . md5(__CLASS__);

        $em = $adapter->getEntityManager();
        $connection = $em->getConnection();
        $connection->getSchemaManager()->dropAndCreateDatabase($database);
        $connection->exec('use ' . $database);

        DoctrineConnectorFactory::setSchemas($database, $database);
    }

    public static function tearDownAfterClass()
    {
        self::$adapter->getEntityManager()
            ->getConnection()
            ->getSchemaManager()
            ->dropDatabase(self::$databaseName);

        parent::tearDownAfterClass();
    }

    public function setUp()
    {
        $this->service = new CompanyAccountTypeService();
        $accountTypeDao = new Doctrine();
        $accountTypeDao->setDefaultMasterSlave(self::$adapter);
        $this->service->setAccountTypeDao($accountTypeDao);

        $em = self::$adapter->getEntityManager();
        $schemaTool = self::$schemaTool = new SchemaTool($em);
        $schema = array(
            $em->getClassMetadata(get_class(new CompanyAccountType())),
        );

        $em->clear();
        $schemaTool->dropSchema($schema);
        $schemaTool->createSchema($schema);
    }

    public function testCreate()
    {
        $companyAccountType = $this->getCompanyAccountTypeModel();

        /** @var CompanyAccountType $response */
        $response = $this->service->create($companyAccountType);

        $this->assertInstanceOf(get_class($companyAccountType), $response);
    }

    /** @depends testCreate */
    public function testGetById()
    {
        $companyAccountType = $this->getCompanyAccountTypeModel();

        /** @var CompanyAccountType $response */
        $response = $this->service->create($companyAccountType);

        $this->assertInstanceOf(get_class($companyAccountType), $response);

        $this->assertSame($companyAccountType->getName(), $response->getName());
        $foundCompanyAccountType = $this->service->getById($response->getId());

        $this->assertInstanceOf(get_class($companyAccountType), $foundCompanyAccountType);
    }

    public function testGetByIdReturnsNullWhenNoRecordsFound()
    {
        $this->assertNull($this->service->getById(1));
    }


    public function testUpdate()
    {
        $companyAccountType = $this->getCompanyAccountTypeModel();

        /** @var CompanyAccountType $response */
        $response = $this->service->create($companyAccountType);

        $this->assertInstanceOf(get_class($companyAccountType), $response);

        $newName = 'Company Name 123';
        $companyAccountType->setName($newName);

        $response = $this->service->update($companyAccountType);

        $this->assertSame($newName, $response->getName());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUpdateThrowsExceptionWhenIdNotFound()
    {
        $companyAccountType = $this->getCompanyAccountTypeModel();

        $companyAccountType->setId(30);

        $this->service->update($companyAccountType);
    }
    public function testGetList()
    {
        $companyAccountType1 = new CompanyAccountType();
        $companyAccountType1->setName('Account Type 1');
        $companyAccountType1->setStatus(CompanyAccountType::STATUS_ACTIVE);

        $companyAccountType2 = new CompanyAccountType();
        $companyAccountType2->setName('Account Type 2');
        $companyAccountType2->setStatus(CompanyAccountType::STATUS_INACTIVE);

        $foundAccountTypes = $this->service->getList();

        $response = array();
        /** @var CompanyAccountType $response */
        $response[] = $this->service->create($companyAccountType1);
        /** @var CompanyAccountType $response */
        $response[] = $this->service->create($companyAccountType2);

        $foundAccountTypes = $this->service->getList();

        $this->assertSame($response, $foundAccountTypes);
    }

    public function testDelete()
    {
        $companyAccountType = $this->getCompanyAccountTypeModel();

        /** @var CompanyAccountType $response */
        $response = $this->service->create($companyAccountType);

        $this->assertInstanceOf(get_class($companyAccountType), $response);

        $deleteRequestResponse = $this->service->delete($companyAccountType);

        $finalResponse = $this->service->getById($deleteRequestResponse->getId());

        $this->assertNull($finalResponse);
    }

    /**
     * @return CompanyAccountType
     */
    protected function getCompanyAccountTypeModel()
    {
        $companyAccountType = new CompanyAccountType();
        $companyAccountType->setName('The Funky Bunch');
        $companyAccountType->setStatus(CompanyAccountType::STATUS_ACTIVE);
        return $companyAccountType;
    }
}
 
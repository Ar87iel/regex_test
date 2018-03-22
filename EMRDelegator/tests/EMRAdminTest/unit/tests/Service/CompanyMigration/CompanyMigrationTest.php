<?php
/**
 * @category WebPT
 * @package EMRAdminTest
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRAdminTest\unit\tests\Service\CompanyMigration;

use EMRAdmin\Model\CompanyMigration as CompanyMigrationModel;
use EMRAdmin\Service\CompanyMigration\CompanyMigration as Service;
use EMRCore\Service\Company\Migration as MigrationState;
use PHPUnit_Framework_MockObject_MockObject;

class CompanyMigrationTest extends \PHPUnit_Framework_TestCase {

    /** @var  Service */
    private $service;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $daoMock;

    protected function setUp()
    {

        $this->daoMock = $this->createMock('EMRAdmin\Service\CompanyMigration\Dao\CompanyMigration');

        $this->service = new Service();
        $this->service->setCompanyMigrationDao($this->daoMock);

    }

    /**
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject|\EMRAdmin\Service\CompanyMigration\CompanyMigration
     */
    protected function getServiceMock($methods = array()) {
        /** @var PHPUnit_Framework_MockObject_MockObject|\EMRAdmin\Service\CompanyMigration\CompanyMigration $mock */
        $mock = $this->getMock('EMRAdmin\Service\CompanyMigration\CompanyMigration', $methods, array(), '', false);
        $mock->setCompanyMigrationDao($this->daoMock);
        return $mock;
    }

    /**
     * Tests CompanyMigration creation.
     */
    public function testCreate()
    {
        $companyId = 123;
        $clusterId = 2;

        $model = new CompanyMigrationModel();
        $model->setCompanyId($companyId);
        $model->setDestinationClusterId($clusterId);

        $this->daoMock->expects($this->once())
            ->method('create')
            ->with($model)
            ->will($this->returnValue($model));

        $result = $this->service->create($model);

        $this->assertInstanceOf('EMRAdmin\Model\CompanyMigration',$result);
        $this->assertEquals($companyId, $result->getCompanyId());

    }


    public function testGetCompanyMigrationByCompanyIdReturnsMigration()
    {
        $companyId = 123;
        $clusterId = 2;

        $model = new CompanyMigrationModel();
        $model->setCompanyId($companyId);
        $model->setDestinationClusterId($clusterId);

        $this->daoMock->expects($this->once())
            ->method('getCompanyMigrationByCompanyId')
            ->with($companyId)
            ->will($this->returnValue($model));

        $result = $this->service->getCompanyMigrationByCompanyId($companyId);

        $this->assertEquals($companyId,$result->getCompanyId());

    }

    /**
     * @expectedException \EMRAdmin\Service\CompanyMigration\Exception\MigrationNotFound
     */
    public function testGetCompanyMigrationByCompanyIdThrowsException()
    {
        $companyId = 123;
        $clusterId = 2;

        $model = new CompanyMigrationModel();
        $model->setCompanyId($companyId);
        $model->setDestinationClusterId($clusterId);

        $this->daoMock->expects($this->once())
            ->method('getCompanyMigrationByCompanyId')
            ->with($companyId);

        $result = $this->service->getCompanyMigrationByCompanyId($companyId);

        $this->assertEquals($companyId,$result->getCompanyId());

    }


    public function testGetCompanyMigrationByMigrationIdReturnsMigration()
    {
        $companyId = 123;
        $migrationId = 6;

        $model = new CompanyMigrationModel();
        $model->setCompanyId($companyId);
        $model->setMigrationId($migrationId);

        $this->daoMock->expects($this->once())
            ->method('load')
            ->with($migrationId)
            ->will($this->returnValue($model));

        $result = $this->service->getCompanyMigrationByMigrationId($migrationId);

        $this->assertEquals($migrationId,$result->getMigrationId());
        $this->assertInstanceOf('EMRAdmin\Model\CompanyMigration',$result);

    }

    /**
     * @expectedException \EMRAdmin\Service\CompanyMigration\Exception\MigrationNotFound
     */
    public function testGetCompanyMigrationByMigrationIdThrowsException()
    {
        $companyId = 123;
        $migrationId = 6;

        $model = new CompanyMigrationModel();
        $model->setCompanyId($companyId);
        $model->setMigrationId($migrationId);

        $this->daoMock->expects($this->once())
            ->method('load')
            ->with($migrationId);

        $result = $this->service->getCompanyMigrationByMigrationId($migrationId);

        $this->assertEquals($migrationId,$result->getMigrationId());
        $this->assertInstanceOf('EMRAdmin\Model\CompanyMigration',$result);

    }


    public function testQueueMigrationWrapsEsbQueueMigrationAndReturnsMigrationModel() {
        $companyId = 1;
        $clusterId = 2;
        $identityId = 3;
        $migrationId = 4;

        $model = array('foo');

        $responseMock = $this->getMock('stdClass', array('getMigrationId'));
        $responseMock->expects($this->once())
            ->method('getMigrationId')
            ->will($this->returnValue($migrationId));

        $esbMock = $this->getMock('stdClass', array('queueMigration'));
        $esbMock->expects($this->once())
            ->method('queueMigration')
            ->with($companyId, $clusterId, $identityId)
            ->will($this->returnValue($responseMock));

        $serviceMock = $this->getMock('stdClass', array('load'));
        $serviceMock->expects($this->once())
            ->method('load')
            ->with($migrationId)
            ->will($this->returnValue($model));

        $locatorMock = $this->getMock('stdClass', array('get'));
        $locatorMock->expects($this->once())
            ->method('get')
            ->with('EMRAdmin\Service\CompanyMigration\CompanyMigration')
            ->will($this->returnValue($serviceMock));

        $service = $this->getServiceMock(array('getServiceLocator', 'getCompanyMigrationEsb'));
        $service->expects($this->once())
            ->method('getCompanyMigrationEsb')
            ->will($this->returnValue($esbMock));
        $service->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($locatorMock));

        $result = $service->queueMigration($companyId, $clusterId, $identityId);
        $this->assertEquals($model, $result);
    }

}
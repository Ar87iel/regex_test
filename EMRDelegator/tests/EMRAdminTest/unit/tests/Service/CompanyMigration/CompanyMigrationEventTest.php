<?php
/**
 * @category WebPT
 * @package EMRAdminTest
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRAdminTest\unit\tests\Service\CompanyMigration;

use EMRAdmin\Model\CompanyMigration as MigrationModel;
use EMRAdmin\Model\CompanyMigrationEvent as EventModel;
use EMRAdmin\Service\CompanyMigration\CompanyMigration as MigrationService;
use EMRAdmin\Service\CompanyMigration\CompanyMigrationEvent as EventService;
use EMRAdmin\Service\CompanyMigration\Exception\MigrationNotFound;
use PHPUnit_Framework_MockObject_MockObject;

class CompanyMigrationEventTest extends \PHPUnit_Framework_TestCase {

    /** @var  MigrationService */
    private $migrationService;

    /** @var  EventService */
    private $eventService;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $eventDaoMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $migrationDaoMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $serviceLocatorMock;

    protected function setUp()
    {

    }

    public function testCompanyMigrationEventCreatesNewEvent()
    {
        $identityId = 1000;
        $companyId = 456;
        $clusterId = 2;
        $state = 'started';
        $event = 'foo';
        $message = 'message stuff';

        $migrationModel = new MigrationModel();
        $migrationModel->setCompanyId($companyId);
        $migrationModel->setIdentityId($identityId);
        $migrationModel->setDestinationClusterId($clusterId);
        $migrationModel->setCompletedState($state);

        $eventModel = new EventModel();

        $migrationServiceMock = $this->createMock('EMRAdmin\Service\CompanyMigration\CompanyMigration');
        $migrationServiceMock->expects($this->once())
            ->method('getCompanyMigrationByCompanyId')
            ->with($companyId)
            ->will($this->returnValue($migrationModel));

        $serviceLocatorMock = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->once())
            ->method('get')
            ->with('EMRAdmin\Service\CompanyMigration\CompanyMigration')
            ->will($this->returnValue($migrationServiceMock));

        $eventDaoMock = $this->createMock('EMRAdmin\Service\CompanyMigration\Dao\CompanyMigrationEvent');
        $eventDaoMock->expects($this->once())
            ->method('create')
            ->with($eventModel)
            ->will($this->returnValue($eventModel));

        $eventServiceMock = $this->getMock('EMRAdmin\Service\CompanyMigration\CompanyMigrationEvent',array('getEventModel'));
        $eventServiceMock->expects($this->once())
            ->method('getEventModel')
            ->will($this->returnValue($eventModel));

        /** @var \EMRAdmin\Service\CompanyMigration\CompanyMigrationEvent  $eventServiceMock */
        $eventServiceMock->setCompanyMigrationEventDao($eventDaoMock);
        $eventServiceMock->setServiceLocator($serviceLocatorMock);

        $result = $eventServiceMock->createCompanyMigrationEvent($companyId, $event, $message);

        $this->assertInstanceOf('EMRAdmin\Model\CompanyMigrationEvent', $result);
        $this->assertEquals($companyId,$result->getMigration()->getCompanyId());
        $this->assertEquals($event,$result->getEvent());
        $this->assertEquals($message,$result->getMessage());

    }

    /**
     *
     * @expectedException \EMRAdmin\Service\CompanyMigration\Exception\MigrationNotFound
     */
    public function testCompanyMigrationEventThrowsException()
    {

        $companyId = 456;
        $event = 'foo';

        $migrationModel = null;


        $migrationServiceMock = $this->createMock('EMRAdmin\Service\CompanyMigration\CompanyMigration');
        $migrationServiceMock->expects($this->once())
            ->method('getCompanyMigrationByCompanyId')
            ->with($companyId)
            ->will($this->returnValue(null));

        $serviceLocatorMock = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->once())
            ->method('get')
            ->with('EMRAdmin\Service\CompanyMigration\CompanyMigration')
            ->will($this->returnValue($migrationServiceMock));

        $eventService = new EventService();
        $eventService->setServiceLocator($serviceLocatorMock);
        $eventService->createCompanyMigrationEvent($companyId, $event);

    }

    public function testLookupMigrationIdByCompanyIdReturnsMigration()
    {
        $identityId = 1000;
        $companyId = 456;
        $clusterId = 2;
        $state = 'started';
        $migrationId = 50;

        $migrationModel = new MigrationModel();
        $migrationModel->setCompanyId($companyId);
        $migrationModel->setIdentityId($identityId);
        $migrationModel->setDestinationClusterId($clusterId);
        $migrationModel->setCompletedState($state);
        $migrationModel->setMigrationId($migrationId);

        $eventModel = new EventModel();

        $migrationServiceMock = $this->createMock('EMRAdmin\Service\CompanyMigration\CompanyMigration');
        $migrationServiceMock->expects($this->once())
            ->method('getCompanyMigrationByCompanyId')
            ->with($companyId)
            ->will($this->returnValue($migrationModel));

        $serviceLocatorMock = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->once())
            ->method('get')
            ->with('EMRAdmin\Service\CompanyMigration\CompanyMigration')
            ->will($this->returnValue($migrationServiceMock));

        $eventDaoMock = $this->createMock('EMRAdmin\Service\CompanyMigration\Dao\CompanyMigrationEvent');

        $eventServiceMock = $this->getMock('EMRAdmin\Service\CompanyMigration\CompanyMigrationEvent',
            array('getEventModel'));
        $eventServiceMock->expects($this->once())
            ->method('getEventModel')
            ->will($this->returnValue($eventModel));

        /** @var \EMRAdmin\Service\CompanyMigration\CompanyMigrationEvent  $eventServiceMock */
        $eventServiceMock->setCompanyMigrationEventDao($eventDaoMock);
        $eventServiceMock->setServiceLocator($serviceLocatorMock);

        $result = $eventServiceMock->lookupMigrationIdByCompanyId($companyId);

        $this->assertNotEmpty($result);
        $this->assertEquals($migrationId, $result);

    }

    /**
     *
     * @expectedException \EMRAdmin\Service\CompanyMigration\Exception\MigrationNotFound
     */
    public function testLookupMigrationIdByCompanyIdThrowsException()
    {
        $companyId = 456;

        $migrationModel = null;

        $migrationServiceMock = $this->createMock('EMRAdmin\Service\CompanyMigration\CompanyMigration');
        $migrationServiceMock->expects($this->once())
            ->method('getCompanyMigrationByCompanyId')
            ->with($companyId)
            ->will($this->returnValue($migrationModel));

        $serviceLocatorMock = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->once())
            ->method('get')
            ->with('EMRAdmin\Service\CompanyMigration\CompanyMigration')
            ->will($this->returnValue($migrationServiceMock));

        $eventService = new EventService();
        $eventService->setServiceLocator($serviceLocatorMock);
        $eventService->lookupMigrationIdByCompanyId($companyId);

    }


}
<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 5/9/13 5:45 PM
 */

use ConsoleTest\Unit\Service\SelectAndInsert\SelectAndInsertConcrete as Service;
use EMRCoreTest\Helper\Reflection as Helper;


class SelectAndInsertTest2 extends PHPUnit_Framework_TestCase {
    /** @var  Service */
    protected $service;


    protected function getServiceFQCN() {
        return 'Console\Etl\Service\SelectAndInsert';
    }
    protected function getResultDtoFQCN() {
        return 'Console\Etl\Service\Dto\ImportFromLegacyResult';
    }

    protected function getResultDtoMock() {
        return $this->getMock($this->getResultDtoFQCN());
    }

    protected function getServiceMock($mockedMethods = array()) {
        return $this->getMock($this->getServiceFQCN(), $mockedMethods);
    }

    protected function getServiceLocatorMock() {
        return $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
    }

    protected function setUp() {
        parent::setUp();
        $this->service = new Service();
    }

    public function testGetResultDtoGetsDtoFromServiceLocatorOnFirstCall() {
        $resultMock = 'mock';

        $locatorMock = $this->getServiceLocatorMock();
        $locatorMock->expects($this->once())
            ->method('get')
            ->with($this->getResultDtoFQCN())
            ->will($this->returnValue($resultMock));

        $this->service->setServiceLocator($locatorMock);

        $resultOne = Helper::invoke($this->service, 'getResultDto');
        $this->assertNotNull($resultOne);
        $this->assertEquals($resultMock, $resultOne);

        $resultTwo = Helper::invoke($this->service, 'getResultDto');
        $this->assertNotNull($resultTwo);
        $this->assertEquals($resultMock, $resultTwo);
    }

    public function testSetExpectedResultCountWrapsDaoGetSourceRecordCount() {
        $count = 7;

        $resultMock = $this->getResultDtoMock();
        $resultMock->expects($this->once())
            ->method('setExpectedRecordCount')
            ->with($count);

        $daoMock = $this->getMock('stdClass', array('getSourceRecordCount'));
        $daoMock->expects($this->once())
            ->method('getSourceRecordCount')
            ->will($this->returnValue($count));

        $service = $this->getServiceMock(array('getResultDto', 'getDao'));
        $service->expects($this->once())
            ->method('getResultDto')
            ->will($this->returnValue($resultMock));
        $service->expects($this->once())
            ->method('getDao')
            ->will($this->returnValue($daoMock));
        Helper::invoke($service, 'setExpectedResultCount');
    }

    public function testSetActualResultCountWrapsDaoGetDestinationRecordCount() {
        $count = 17;

        $resultMock = $this->getResultDtoMock();
        $resultMock->expects($this->once())
            ->method('setActualRecordCount')
            ->with($count);

        $daoMock = $this->getMock('stdClass', array('getDestinationRecordCount'));
        $daoMock->expects($this->once())
            ->method('getDestinationRecordCount')
            ->will($this->returnValue($count));

        $service = $this->getServiceMock(array('getResultDto', 'getDao'));
        $service->expects($this->once())
            ->method('getResultDto')
            ->will($this->returnValue($resultMock));
        $service->expects($this->once())
            ->method('getDao')
            ->will($this->returnValue($daoMock));
        Helper::invoke($service, 'setActualResultCount');
    }

    public function testSetForeignKeyChecksWrapsDaoMethod() {
        $bool = true;
        $daoMock = $this->getMock('stdClass', array('foreignKeyChecks'));
        $daoMock->expects($this->once())
            ->method('foreignKeyChecks')
            ->with($bool);
        $service = $this->getServiceMock(array('getDao'));
        $service->expects($this->once())
            ->method('getDao')
            ->will($this->returnValue($daoMock));
        Helper::invoke($service, 'setForeignKeyChecks', array($bool));

        $bool = false;
        $daoMock = $this->getMock('stdClass', array('foreignKeyChecks'));
        $daoMock->expects($this->once())
            ->method('foreignKeyChecks')
            ->with($bool);
        $service = $this->getServiceMock(array('getDao'));
        $service->expects($this->once())
            ->method('getDao')
            ->will($this->returnValue($daoMock));
        Helper::invoke($service, 'setForeignKeyChecks', array($bool));
    }

    public function testMigrateRecordsExecutesStatement() {
        $sql = 'insert foo';
        $insertMock = $this->getMock('stdClass', array('getSql'));
        $insertMock->expects($this->once())
            ->method('getSql')
            ->will($this->returnValue($sql));
        $records = array($insertMock);

        $daoMock = $this->getMock('stdClass', array('selectSourceRecordsAsInsertStatements', 'executeInsertStatement'));
        $daoMock->expects($this->once())
            ->method('selectSourceRecordsAsInsertStatements')
            ->will($this->returnValue($records));
        $daoMock->expects($this->once())
            ->method('executeInsertStatement')
            ->with($sql);

        $service = $this->getServiceMock(array('getDao'));
        $service->expects($this->exactly(2))
            ->method('getDao')
            ->will($this->returnValue($daoMock));
        Helper::invoke($service, 'migrateRecords');
    }

    public function testTruncateDestinationTableWrapsDaoMethod() {
        $daoMock = $this->getMock('stdClass', array('truncateDestinationTable'));
        $daoMock->expects($this->once())
            ->method('truncateDestinationTable');
        $service = $this->getServiceMock(array('getDao'));
        $service->expects($this->once())
            ->method('getDao')
            ->will($this->returnValue($daoMock));
        Helper::invoke($service, 'truncateDestinationTable');
    }
}

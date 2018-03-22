<?php
use EMRAdmin\Service\Facility\Module\Dto\Module;
use EMRAdmin\Service\Facility\Module\Modules as ModulesService;

/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 8/7/13 3:26 PM
 */

class ModulesTest2 extends PHPUnit_Framework_TestCase {
    /**
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject|\EMRAdmin\Service\Facility\Module\Modules
     */
    protected function getServiceMock($methods = array()) {
        return $this->getMock('EMRAdmin\Service\Facility\Module\Modules', $methods);
    }

    public function testGetByFacilityIdCallsEsbAndMarshalsResponse() {
        $modulesDto = 'foo';
        $facilityId = 9;
        $modules = array('foo');

        $marshallerMock = $this->getMock('stdClass', array('marshall'));
        $marshallerMock->expects($this->once())
            ->method('marshall')
            ->with($modulesDto)
            ->will($this->returnValue($modules));

        $serviceMock = $this->getMock('stdClass', array('getModules'));
        $serviceMock->expects($this->once())
            ->method('getModules')
            ->with($facilityId)
            ->will($this->returnValue($modulesDto));

        $service = $this->getServiceMock(array('getEsbFacilityService', 'getModulesDtoMarshaller'));
        $service->expects($this->once())
            ->method('getEsbFacilityService')
            ->will($this->returnValue($serviceMock));
        $service->expects($this->once())
            ->method('getModulesDtoMarshaller')
            ->will($this->returnValue($marshallerMock));

        $result = $service->getByFacilityId($facilityId);
        $this->assertEquals($modules, $result);
    }

    public function testHasBillingFeedModuleReturnsTrue() {
        $facilityId = 11;
        $moduleMock = $this->getMock('stdClass', array('getId'));
        $moduleMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(Module::BILLING_FEEDS));

        $modules = array($moduleMock);

        $service = $this->getServiceMock(array('getByFacilityId'));
        $service->expects($this->once())
            ->method('getByFacilityId')
            ->with($facilityId)
            ->will($this->returnValue($modules));
        $result = $service->hasBillingFeedModule($facilityId);
        $this->assertTrue($result);
    }

    public function testHasBillingFeedModuleReturnsFalse() {
        $facilityId = 11;
        $moduleMock = $this->getMock('stdClass', array('getId'));
        $moduleMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(Module::BILLING_SERVICE));

        $modules = array($moduleMock);

        $service = $this->getServiceMock(array('getByFacilityId'));
        $service->expects($this->once())
            ->method('getByFacilityId')
            ->with($facilityId)
            ->will($this->returnValue($modules));
        $result = $service->hasBillingFeedModule($facilityId);
        $this->assertFalse($result);
    }

    public function testGetList()
    {
        $expected = array();

        $dao = $this->createMock('EMRAdmin\Service\Facility\Module\Dao\Modules');
        $dao->expects($this->once())
            ->method('getList')
            ->will($this->returnValue($expected));

        $service = new ModulesService();
        $service->setModulesDao($dao);

        $result = $service->getList();
        $this->assertEquals($expected, $result);
    }
}

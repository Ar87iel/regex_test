<?php
use EMRAdmin\Service\Facility\FacilityHasModules\FacilityHasModules;
use EMRAdmin\Service\Facility\FacilityHasModules\Dao\Dto\CreateParameters;

/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jkozel
 * 10/5/13 12:55 PM 
 */

class FacilityHasModulesTest extends PHPUnit_Framework_TestCase {
    /**
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject|FacilityHasModules
     */
    protected function getServiceMock($methods = array()) {
        return $this->getMock('EMRAdmin\Service\Facility\FacilityHasModules\FacilityHasModules', $methods);
    }

    public function testCreateFacilityHasModule() {
        $createParam = new CreateParameters();
        $test = 'bar';

        $daoMock = $this->getMock('stdClass', array('createFacilityHasModules'));
        $daoMock->expects($this->once())
            ->method('createFacilityHasModules')
            ->with($createParam)
            ->will($this->returnValue($test));

        $service = $this->getServiceMock(array('getDao'));
        $service->expects($this->once())
            ->method('getDao')
            ->will($this->returnValue($daoMock));

        $result = $service->createFacilityHasModule($createParam);
        $this->assertEquals($test, $result);
    }

    public function testGetFaciltyHasModule()
    {
        $facilityId = 7;
        $test = 'bar';

        $daoMock = $this->getMock('stdClass', array('getFacilityHasModules'));
        $daoMock->expects($this->once())
            ->method('getFacilityHasModules')
            ->with($facilityId)
            ->will($this->returnValue($test));

        $service = $this->getServiceMock(array('getDao'));
        $service->expects($this->once())
            ->method('getDao')
            ->will($this->returnValue($daoMock));

        $result = $service->getFacilityHasModulesByFacilityId($facilityId);
        $this->assertEquals($test, $result);
    }
}

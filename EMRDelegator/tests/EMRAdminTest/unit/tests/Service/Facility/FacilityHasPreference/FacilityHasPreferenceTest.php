<?php
use EMRAdmin\Service\Facility\FacilityHasPreference\Dto\CreateParameter;
use EMRAdmin\Service\Facility\FacilityHasPreference\FacilityHasPreference;

/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 10/4/13 9:04 AM
 */

class FacilityHasPreferenceTest extends PHPUnit_Framework_TestCase {

    /**
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject|FacilityHasPreference
     */
    protected function getServiceMock($methods = array()) {
        return $this->getMock('EMRAdmin\Service\Facility\FacilityHasPreference\FacilityHasPreference', $methods);
    }

    public function testCreateWrapsDaoCreate() {
        $createParam = new CreateParameter();
        $test = 'bar';

        $daoMock = $this->getMock('stdClass', array('create'));
        $daoMock->expects($this->once())
            ->method('create')
            ->with($createParam)
            ->will($this->returnValue($test));

        $service = $this->getServiceMock(array('getDao'));
        $service->expects($this->once())
            ->method('getDao')
            ->will($this->returnValue($daoMock));

        $result = $service->create($createParam);
        $this->assertEquals($test, $result);
    }

    public function testGetWrapsDaoGet() {
        $facilityId = 3;
        $test = 'bar';

        $daoMock = $this->getMock('stdClass', array('get'));
        $daoMock->expects($this->once())
            ->method('get')
            ->with($facilityId)
            ->will($this->returnValue($test));

        $service = $this->getServiceMock(array('getDao'));
        $service->expects($this->once())
            ->method('getDao')
            ->will($this->returnValue($daoMock));

        $result = $service->get($facilityId);
        $this->assertEquals($test, $result);
    }
}

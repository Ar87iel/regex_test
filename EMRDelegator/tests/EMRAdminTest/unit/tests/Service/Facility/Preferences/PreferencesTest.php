<?php
use EMRAdmin\Service\Facility\Preferences\Preferences;

/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 9/30/13 1:42 PM
 */

class PreferencesTest2 extends PHPUnit_Framework_TestCase {
    /**
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject|Preferences
     */
    protected function getServiceMock($methods = array()) {
        return $this->getMock('EMRAdmin\Service\Facility\Preferences\Preferences', $methods);
    }

    public function testGetListWrapsDaoMethod() {
        $test = 'foo';
        $daoMock = $this->getMock('stdClass', array('getList'));
        $daoMock->expects($this->once())
            ->method('getList')
            ->will($this->returnValue($test));
        $service = $this->getServiceMock(array('getDao'));
        $service->expects($this->once())
            ->method('getDao')
            ->will($this->returnValue($daoMock));
        $r = $service->getList();
        $this->assertEquals($test, $r);
    }
}

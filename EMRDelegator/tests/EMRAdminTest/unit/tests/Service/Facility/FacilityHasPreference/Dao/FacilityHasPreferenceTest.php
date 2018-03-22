<?php
namespace EMRAdminTest\Unit\Service\Facility\FacilityHasPreference;

use EMRAdmin\Service\Facility\FacilityHasPreference\Dao\FacilityHasPreference;
use EMRAdmin\Service\Facility\FacilityHasPreference\Dto\CreateParameter;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use EMRAdmin\Service\Facility\FacilityHasPreference\Dto\FacilityHasPreference as FacilityHasPreferenceDto;
use EMRCoreTest\Helper\Reflection;

class FacilityHasPreferenceTest extends \PHPUnit_Framework_TestCase {
    /**
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject|FacilityHasPreference
     */
    protected function getDaoMock($methods = array()) {
        return $this->getMock('EMRAdmin\Service\Facility\FacilityHasPreference\Dao\FacilityHasPreference', $methods);
    }

    public function testMarshalResponseToModels() {
        $id = 3;
        $facilityId = 7;
        $preferenceId = 11;
        $value = 13;

        $payload = (object)array(
            'id' => $id,
            'facilityId' => $facilityId,
            'preferenceId' => $preferenceId,
            'value' => $value
        );
        $success = new Success();
        $success->setPayload((object)array('facilityHasPreferences' => array($payload, $payload, $payload)));

        $test = new FacilityHasPreferenceDto();
        $test->setFacilityId($facilityId);
        $test->setId($id);
        $test->setPreferenceId($preferenceId);
        $test->setValue($value);
        $tests = array($test,$test,$test);

        $dao = $this->getDaoMock(array());
        $result = Reflection::invoke($dao, 'marshalResponseToModels', array($success));
        $this->assertEquals($tests, $result);
    }

    public function testGet() {
        $uri = 'http://foo';
        $method = 'get';
        $facilityId = 7;
        $fullUri = "$uri/$facilityId";
        $response = new Success();
        $marshalled = '[]';

        $routeMock = $this->getMock('stdClass', array('getUri', 'getMethod'));
        $routeMock->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue($uri));
        $routeMock->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue($method));
        $dao = $this->getDaoMock(array('getRoute', 'call', 'marshalResponseToModels'));
        $dao->expects($this->once())
            ->method('getRoute')
            ->will($this->returnValue($routeMock));
        $dao->expects($this->once())
            ->method('call')
            ->with($fullUri, $method, array())
            ->will($this->returnValue($response));
        $dao->expects($this->once())
            ->method('marshalResponseToModels')
            ->with($response)
            ->will($this->returnValue($marshalled));
        $result = $dao->get($facilityId);
        $this->assertEquals($marshalled, $result);
    }

    public function testCreate() {
        $uri = 'http://foo';
        $method = 'get';
        $facilityId = 7;
        $response = new Success();
        $marshalled = '[]';
        $preferences = '1,2';
        $createParam = new CreateParameter();
        $createParam->setFacilityId($facilityId);
        $createParam->setPreferences($preferences);
        $params = array(
            'facilityId' => $facilityId,
            'preferences' => $preferences
        );

        $routeMock = $this->getMock('stdClass', array('getUri', 'getMethod'));
        $routeMock->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue($uri));
        $routeMock->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue($method));
        $dao = $this->getDaoMock(array('getRoute', 'call', 'marshalResponseToModels'));
        $dao->expects($this->once())
            ->method('getRoute')
            ->will($this->returnValue($routeMock));
        $dao->expects($this->once())
            ->method('call')
            ->with($uri, $method, $params)
            ->will($this->returnValue($response));
        $dao->expects($this->once())
            ->method('marshalResponseToModels')
            ->with($response)
            ->will($this->returnValue($marshalled));
        $result = $dao->create($createParam);
        $this->assertEquals($marshalled, $result);
    }
}

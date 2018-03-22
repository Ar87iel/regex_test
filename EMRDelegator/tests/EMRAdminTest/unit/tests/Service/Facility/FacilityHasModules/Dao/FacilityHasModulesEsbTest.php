<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jkozel
 * 10/4/13 7:03 PM 
 */

use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use EMRCoreTest\Helper\Reflection;
use EMRAdmin\Service\Facility\FacilityHasModules\Dao\FacilityHasModulesEsb;
use EMRAdmin\Service\Facility\FacilityHasModules\Dao\Dto\CreateParameters;
use EMRAdmin\Service\Facility\FacilityHasModules\Dto\FacilityHasModules as FacilityHasModulesDto;

class FacilityHasModulesEsbTest extends PHPUnit_Framework_TestCase {

    /**
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject|FacilityHasModulesEsb
     */
    protected function getDaoMock($methods = array())
    {
        return $this->getMock('EMRAdmin\Service\Facility\FacilityHasModules\Dao\FacilityHasModulesEsb', $methods);
    }

    public function testCreateFacilityHasModules()
    {
        $uri = 'http://foo';
        $method = 'get';
        $facilityId = 7;
        $response = new Success();
        $modules = array(1,2);
        $marshalled = '[]';
        $createParameters = new CreateParameters;
        $createParameters->setModuleIds($modules);
        $createParameters->setFacilityId($facilityId);
        $params = array(
            'facilityId' => $facilityId,
            'modules' => json_encode($modules),
        );

        $routeMock = $this->getMock('stdClass', array('getUri', 'getMethod'));
        $routeMock->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue($uri));
        $routeMock->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue($method));

        $dao = $this->getDaoMock(array('getRoute', 'call', 'marshalCreateResponseToModels'));

        $dao->expects($this->once())
            ->method('getRoute')
            ->with(FacilityHasModulesEsb::ROUTE_CREATE_FACILITYHASMODULES, FacilityHasModulesEsb::ROUTE_NAMESPACE)
            ->will($this->returnValue($routeMock));
        $dao->expects($this->once())
            ->method('call')
            ->with($uri, $method, $params)
            ->will($this->returnValue($response));
        $dao->expects($this->once())
            ->method('marshalCreateResponseToModels')
            ->with($response)
            ->will($this->returnValue($marshalled));

        $result = $dao->createFacilityHasModules($createParameters);

        $this->assertEquals($marshalled, $result);
    }

    public function testGetFacilityHasModules()
    {
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
        $dao = $this->getDaoMock(array('getRoute', 'call', 'marshalGetResponseToModels'));
        $dao->expects($this->once())
            ->method('getRoute')
            ->with(FacilityHasModulesEsb::ROUTE_GET_FACILITYHASMODULES, FacilityHasModulesEsb::ROUTE_NAMESPACE)
            ->will($this->returnValue($routeMock));
        $dao->expects($this->once())
            ->method('call')
            ->with($fullUri, $method, array())
            ->will($this->returnValue($response));
        $dao->expects($this->once())
            ->method('marshalGetResponseToModels')
            ->with($response)
            ->will($this->returnValue($marshalled));
        $result = $dao->getFacilityHasModules($facilityId);
        $this->assertEquals($marshalled, $result);
    }

    public function testMarshalCreateResponseToModels()
    {
        $id = 3;
        $facilityId = 7;
        $moduleId = 11;

        $payload = (object)array(
            'id' => $id,
            'facilityId' => $facilityId,
            'moduleId' => $moduleId
        );
        $facilityHasModules = (object)array('facilityHasModule' => $payload);
        $success = new Success();
        $success->setPayload((object)array('result' => array($facilityHasModules, $facilityHasModules, $facilityHasModules)));

        $test = new FacilityHasModulesDto();
        $test->setFacilityId($facilityId);
        $test->setId($id);
        $test->setModuleId($moduleId);
        $tests = array($test,$test,$test);

        $dao = $this->getDaoMock(array());
        $result = Reflection::invoke($dao, 'marshalCreateResponseToModels', array($success));
        $this->assertEquals($tests, $result);
    }


    public function testMarshalGetResponseToModels()
    {
        $id = 3;
        $facilityId = 7;
        $moduleId = 11;

        $payload = (object)array(
            'facilityHasModule' => (object)array(
                'id' => $id,
                'facilityId' => $facilityId,
                'moduleId' => $moduleId
            )
        );
        $success = new Success();
        $success->setPayload((object)array($payload, $payload, $payload));

        $test = new FacilityHasModulesDto();
        $test->setFacilityId($facilityId);
        $test->setId($id);
        $test->setModuleId($moduleId);
        $tests = array($test,$test,$test);

        $dao = $this->getDaoMock(array());
        $result = Reflection::invoke($dao, 'marshalGetResponseToModels', array($success));
        $this->assertEquals($tests, $result);
    }

}

<?php
/**
 * @category WebPT
 * @package ServiceTest
 * @copyright Copyright (c) 2012 WebPT, INC
 */
namespace ServiceTest\Unit;

use EMRCore\Service\Dto\RequestContext;
use EMRCore\Zend\Form\ServiceHelper;
use EMRCore\Zend\Module\Service\Response\Content;
use EMRCore\Zend\Module\Service\Response\Error;
use EMRDelegator\Facility\Marshaller\FacilityModelToArray;
use EMRDelegator\Model\Facility as FacilityModel;
use EMRDelegator\Model\Company as CompanyModel;
use Service\Controller\FacilityController;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Stdlib\Parameters;

class FacilityControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Service\Controller\FacilityController
     */
    private $controller;

    /**
     * @var \Zend\Http\PhpEnvironment\Request
     */
    private $request;

    /**
     * @var \Zend\Mvc\Router\RouteMatch
     */
    private $routeMatch;

    /**
     * @var \Zend\Mvc\MvcEvent
     */
    private $mvcEvent;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $serviceLocatorMock;

    public function setUp()
    {
        $this->controller = new FacilityController();
        $this->request = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'facility'));
        $this->mvcEvent = new MvcEvent();
        $this->mvcEvent->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->mvcEvent);
        $this->controller->setRequestContext(new RequestContext());

        $this->serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $this->controller->setServiceLocator($this->serviceLocatorMock);
    }

    public function testGet()
    {
        $companyModel = new CompanyModel();
        $companyModel->setCompanyId(10);

        $facilityModel = new FacilityModel();
        $facilityModel->setFacilityId(1);
        $facilityModel->setName('Facility 1');
        $facilityModel->setCompany($companyModel);

        $facilityService = $this->getMock('EMRDelegator\Service\Facility\Facility');
        $facilityService->expects($this->once())->method('load')->will($this->returnValue($facilityModel));

        $this->serviceLocatorMock->expects($this->any())->method('get')->will($this->returnValueMap(
                array(
                    array('EMRDelegator\Service\Facility\Facility', $facilityService),
                    array('EMRDelegator\Facility\Marshaller\FacilityModelToArray', new FacilityModelToArray()),
                    array('EMRCore\Zend\Form\ServiceHelper', new ServiceHelper() ),
                    array('ServiceResponseContent', new Content())
                )));

        $this->request->setMethod('GET');
        $this->routeMatch->setParam('id', '1234');
        $response = $this->controller->dispatch($this->request);
        $this->assertInstanceOf('EMRCore\Zend\Module\Service\Response\Content',$response);

        $this->assertObjectHasAttribute('content',$response);
        $content = $response->getContent();
        $this->assertArrayHasKey('facility',$content);
        $responseFacility = $content['facility'];

        $this->assertEquals($responseFacility['facilityId'], $facilityModel->getFacilityId());
        $this->assertEquals($responseFacility['name'],$facilityModel->getName());
        $this->assertEquals($responseFacility['companyId'], $facilityModel->getCompany()->getCompanyId());
    }

    public function testPrepareCreate()
    {
        $companyModel = new CompanyModel();
        $companyModel->setCompanyId(1);
        $companyModel->setName('My Company');

        $companyService = $this->getMock('EMRDelegator\Service\Facility\Facility');
        $companyService->expects($this->once())->method('load')->will($this->returnValue($companyModel));

        $this->serviceLocatorMock->expects($this->any())->method('get')->will($this->returnValue($companyService));
        $facilityName = 'Some New Facility';
        $facilityModel = $this->controller->prepareCreateFacility(array('name'=>$facilityName,'companyId'=>1));
        $this->assertEquals($facilityName, $facilityModel->getName());
        $this->assertEquals($companyModel, $facilityModel->getCompany());
    }

    public function testCreate()
    {
        $companyModel = new CompanyModel();
        $companyModel->setCompanyId(10);

        $facilityModel = new FacilityModel();
        $facilityModel->setFacilityId(473);
        $facilityModel->setName('My Cluster');
        $facilityModel->setCompany($companyModel);

        $facilityService = $this->getMock('EMRDelegator\Service\Facility\Facility');
        $facilityService->expects($this->once())->method('create')->will($this->returnValue($facilityModel));
        $facilityService->expects($this->once())->method('load')->will($this->returnValue($facilityModel));

        $serviceHelper = new ServiceHelper();
        $serviceHelper->setLogger($this->getMock('Logger', array(), array(), '', false));

        $companyModel = new CompanyModel();
        $companyModel->setCompanyId(1);
        $companyModel->setName('My Company');

        $companyService = $this->getMock('EMRDelegator\Service\Company\Company');
        $companyService->expects($this->once())->method('load')->will($this->returnValue($companyModel));

        $this->serviceLocatorMock->expects($this->any())->method('get')->will($this->returnValueMap(
            array(
                array('EMRDelegator\Service\Facility\Facility', $facilityService),
                array('EMRDelegator\Service\Company\Company', $companyService),
                array('EMRDelegator\Facility\Marshaller\FacilityModelToArray', new FacilityModelToArray()),
                array('EMRCore\Zend\Form\ServiceHelper', $serviceHelper ),
                array('ServiceResponseContent', new Content())
            )));

        $this->request->setMethod('POST');
        $params = new Parameters(array(
            'name' => $facilityModel->getName(),
            'companyId' => $facilityModel->getCompany()->getCompanyId(),
        ));
        $this->request->setPost($params);
        $response = $this->controller->dispatch($this->request);
        $this->assertInstanceOf('EMRCore\Zend\Module\Service\Response\Content',$response);

        $this->assertObjectHasAttribute('content',$response);
        $content = $response->getContent();
        $this->assertArrayHasKey('facility',$content);
        $this->assertArrayHasKey('facilityId',$content['facility']);
        $this->assertEquals($facilityModel->getFacilityId(),$content['facility']['facilityId']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateMissingRequiredParams()
    {
        $createData = array();
        $serviceHelper = new ServiceHelper();
        $serviceHelper->setLogger($this->getMock('Logger', array(), array(), '', false));

        $this->serviceLocatorMock->expects($this->any())->method('get')->will($this->returnValueMap(
            array(
                array( 'EMRCore\Zend\Form\ServiceHelper', $serviceHelper )
            )));

        $this->controller->create($createData);

    }

    public function testUpdate()
    {
        $companyModel = new CompanyModel();

        $originalModel = new FacilityModel();
        $originalModel->setFacilityId(1);
        $originalModel->setName('Facility 1');

        $updatedModel = new FacilityModel();
        $updatedModel->setFacilityId(1);
        $updatedModel->setName('Updated Facility 1 Name');
        $updatedModel->setCompany($companyModel);

        $updateData = array('name', $updatedModel->getName());

        $controllerMock = $this->getMock('Service\Controller\FacilityController', array('load','prepareUpdateFacilityModel'));
        $controllerMock->expects($this->once())
            ->method('prepareUpdateFacilityModel')
            ->with($this->equalTo($originalModel),$this->equalTo($updateData))
            ->will($this->returnValue($updatedModel));

        $facilityService = $this->getMock('EMRDelegator\Service\Facility\Facility');
        $facilityService->expects($this->once())
            ->method('update')
            ->with($this->equalTo($updatedModel))
            ->will($this->returnValue($updatedModel));
        $facilityService->expects($this->once())
            ->method('load')
            ->with($originalModel->getFacilityId())
            ->will($this->returnValue($originalModel));

        $this->serviceLocatorMock->expects($this->any())->method('get')->will($this->returnValueMap(
            array(
                array('EMRDelegator\Service\Facility\Facility', $facilityService),
                array('EMRDelegator\Facility\Marshaller\FacilityModelToArray', new FacilityModelToArray()),
                array('ServiceResponseContent', new Content()),
                array('ServiceResponseError', new Error()),
                array('EMRCore\Zend\Form\ServiceHelper', new ServiceHelper() ),
            )));

        /** @var $controllerMock FacilityController*/
        $controllerMock->setServiceLocator($this->serviceLocatorMock);

        /** @var $response Content */
        $response = $controllerMock->update($originalModel->getFacilityId(),$updateData);
        $this->assertInstanceOf('EMRCore\Zend\Module\Service\Response\Content',$response);

        $this->assertObjectHasAttribute('content',$response);
        $content = $response->getContent();
        $this->assertEquals(true, $content['success']);
    }

    public function testPrepareUpdate()
    {
        $originalModel = new FacilityModel();
        $originalModel->setFacilityId(1);
        $originalModel->setName('Facility 1');

        $data['name'] = 'New Facility Name';

        $this->serviceLocatorMock->expects($this->any())->method('get')->will($this->returnValueMap(
            array(
                array('EMRCore\Zend\Form\ServiceHelper', new ServiceHelper() ),
            )));

        $updatedModel = $this->controller->prepareUpdateFacilityModel($originalModel,$data);
        $this->assertEquals($data['name'],$updatedModel->getName());
    }

    public function testDelete()
    {
        $facilityId = 1;
        $facilityService = $this->getMock('EMRDelegator\Service\Facility\Facility');
        $facilityService->expects($this->once())->method('delete')->with($facilityId);

        $this->serviceLocatorMock->expects($this->any())->method('get')->will($this->returnValueMap(
            array(
                array('EMRDelegator\Service\Facility\Facility', $facilityService),
                array('ServiceResponseContent', new Content())
            )));

        $response = $this->controller->delete($facilityId);
        $this->assertInstanceOf('EMRCore\Zend\Module\Service\Response\Content',$response);

        $this->assertObjectHasAttribute('content',$response);
        $content = $response->getContent();
        $this->assertEquals(true,$content['success']);
    }
}

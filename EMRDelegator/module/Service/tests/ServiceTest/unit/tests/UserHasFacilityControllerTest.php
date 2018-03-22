<?php

/**
 * @category WebPT
 * @package EMRDelegatorTest
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace ServiceTest\Unit;

use EMRCore\Zend\Form\ServiceHelper;
use Service\Controller\UserHasFacilityController;
use EMRCore\Zend\Module\Service\Response\Content;
use EMRDelegator\Model\UserHasFacility;
use EMRDelegator\Service\UserHasFacility\UserHasFacility as UserHasFacilityService;
use EMRCoreTest\Helper\Reflection as Helper;
use EMRDelegator\Service\UserHasFacility\Dto\SearchUsersHasFacilityResults;
use Zend\Http\Request;
use EMRDelegator\Model\Company;
use EMRDelegator\Service\UserHasFacility\Dto\SearchUserHasFacilityResult;
use EMRDelegator\Model\Cluster;

class UserHasFacilityControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var UserHasFacilityController
     */
    private $controller;
    /** @var  ServiceHelper */
    private $serviceHelper;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $serviceLocatorMock;

    public function setUp()
    {
        $this->controller = new UserHasFacilityController();

        $this->serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $this->controller->setServiceLocator($this->serviceLocatorMock);

        $this->serviceHelper = new ServiceHelper();
        $this->serviceHelper->setLogger($this->getMock('Logger', array(), array(), '', false));
    }

    public function testCreate()
    {
        $identityId = 1;

        $rawData = array(
            'identityId' => $identityId,
            'facilityIds' => json_encode(array(1, 2)),
            'defaultFacilityId' => 2
        );

        $userHasFacilityService = $this->getMock('EMRDelegator\Service\UserHasFacility\UserHasFacility');
        $userHasFacilityService->expects($this->at(0))
                ->method('replaceUserFacilities')
                ->with($identityId, json_decode($rawData['facilityIds']), $rawData['defaultFacilityId']);

        $this->serviceLocatorMock->expects($this->any())
                ->method('get')->will($this->returnValueMap(
                        array(
                            array('EMRDelegator\Service\UserHasFacility\UserHasFacility', $userHasFacilityService),
                            array('ServiceResponseContent', new Content())
        )));

        $response = $this->controller->create($rawData);
        $this->assertInstanceOf('EMRCore\Zend\Module\Service\Response\Content', $response);
    }

    public function testCreatePrepareFormData()
    {
        $identityId = 1;

        $rawData = array(
            'identityId' => $identityId,
            'facilityIds' => json_encode(array(1, 2)),
            'defaultFacilityId' => 2
        );

        $returnedData = $this->controller->createPrepareFormData($rawData);
        $this->assertEquals($rawData, $returnedData);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreatePrepareFormDataInvalidIdentifyId()
    {
        $this->serviceLocatorMock->expects($this->once())
                ->method('get')
                ->with('EMRCore\Zend\Form\ServiceHelper')
                ->will($this->returnValue($this->serviceHelper));

        $rawData = array(
            'identityId' => 'asdf',
            'facilityIds' => json_encode(array(1, 2)),
            'defaultFacilityId' => 2
        );

        $returnedData = $this->controller->createPrepareFormData($rawData);
        $this->assertEquals($rawData, $returnedData);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreatePrepareFormDataMissingIdentifyId()
    {
        $this->serviceLocatorMock->expects($this->once())
                ->method('get')
                ->with('EMRCore\Zend\Form\ServiceHelper')
                ->will($this->returnValue($this->serviceHelper));

        $rawData = array(
            'facilityIds' => json_encode(array(1, 2)),
            'defaultFacilityId' => 2
        );

        $returnedData = $this->controller->createPrepareFormData($rawData);
        $this->assertEquals($rawData, $returnedData);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreatePrepareFormDataInvalidFacilityId()
    {
        $this->serviceLocatorMock->expects($this->once())
                ->method('get')
                ->with('EMRCore\Zend\Form\ServiceHelper')
                ->will($this->returnValue($this->serviceHelper));

        $rawData = array(
            'identityId' => 1,
            'facilityIds' => 2,
            'defaultFacilityId' => 2
        );

        $returnedData = $this->controller->createPrepareFormData($rawData);
        $this->assertEquals($rawData, $returnedData);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreatePrepareFormDataMissingFacilityId()
    {
        $this->serviceLocatorMock->expects($this->once())
                ->method('get')
                ->with('EMRCore\Zend\Form\ServiceHelper')
                ->will($this->returnValue($this->serviceHelper));

        $rawData = array(
            'identityId' => 1,
            'defaultFacilityId' => 2
        );

        $returnedData = $this->controller->createPrepareFormData($rawData);
        $this->assertEquals($rawData, $returnedData);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreatePrepareFormDataInvalidDefaultFacilityId()
    {
        $this->serviceLocatorMock->expects($this->once())
                ->method('get')
                ->with('EMRCore\Zend\Form\ServiceHelper')
                ->will($this->returnValue($this->serviceHelper));

        $rawData = array(
            'identityId' => 1,
            'facilityIds' => json_encode(array(1, 2)),
            'defaultFacilityId' => 'qwer'
        );

        $returnedData = $this->controller->createPrepareFormData($rawData);
        $this->assertEquals($rawData, $returnedData);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreatePrepareFormDataMissingDefaultFacilityId()
    {
        $this->serviceLocatorMock->expects($this->once())
                ->method('get')
                ->with('EMRCore\Zend\Form\ServiceHelper')
                ->will($this->returnValue($this->serviceHelper));

        $rawData = array(
            'identityId' => 1,
            'facilityIds' => json_encode(array(1, 2)),
        );

        $returnedData = $this->controller->createPrepareFormData($rawData);
        $this->assertEquals($rawData, $returnedData);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotPrepareGetListDueToInvalidIdentityId()
    {
        $this->serviceLocatorMock->expects($this->once())->method('get')
                ->will($this->returnValue($this->serviceHelper));

        $this->controller->prepareGetList(array());
    }

    public function testPrepareGetListGetsFilteredIdentityId()
    {
        $identityId = 1;

        $actualIdentityId = $this->controller->prepareGetList(array(
            'identityId' => (string) $identityId,
        ));

        $this->assertSame($identityId, $actualIdentityId);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPrepareGetListByTokenThrowsExceptionOnInvalidToken()
    {
        $data = array('wpt_sso_token' => 'stuff');
        $errorMessage = 'bad stuff happened';

        $form = $this->getMock('Service\Controller\Form\UserHasFacility\GetByToken');
        $form->expects($this->once())->method('setData')->with($data);
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formHelper = $this->getMock('EMRCore\Zend\Form\ServiceHelper');
        $formHelper->expects($this->once())
                ->method('getValidationMessagesAsString')
                ->with($form)
                ->will($this->returnValue($errorMessage));

        $controller = $this->getMock('Service\Controller\UserHasFacilityController', array('getByTokenZendForm', 'getFormHelper'));
        $controller->expects($this->once())->method('getByTokenZendForm')->will($this->returnValue($form));
        $controller->expects($this->once())->method('getFormHelper')->will($this->returnValue($formHelper));

        /** @var \Service\Controller\UserHasFacilityController $controller */
        $result = Helper::invoke($controller, 'prepareGetListByToken', array($data));
    }

    /**
     *
     */
    public function testPrepareGetListByTokenValidatesTokenReturnsWptToken()
    {
        $data = array('wpt_sso_token' => 'stuff');
        $form = $this->getMock('Service\Controller\Form\UserHasFacility\GetByToken');
        $form->expects($this->once())->method('setData')->with($data);
        $form->expects($this->once())->method('getData')->will($this->returnValue($data));
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));

        $controller = $this->getMock('Service\Controller\UserHasFacilityController', array('getByTokenZendForm'));
        $controller->expects($this->once())->method('getByTokenZendForm')->will($this->returnValue($form));

        /** @var \Service\Controller\UserHasFacilityController $controller */
        $result = Helper::invoke($controller, 'prepareGetListByToken', array($data));
        $this->assertEquals($data['wpt_sso_token'], $result);
    }

    /**
     *
     */
    public function testGetByTokenAction()
    {
        $paramName = 'wpt_sso_token';
        $token = '1234stuffandthings';
        $data = array($paramName => $token);
        $identityId = 832;
        $expectedResult = 'yeah!';

        $request = $this->getMock('Zend\Http\Request');
        $request->expects($this->at(0))->method('getQuery')
                ->with('userId', 0)
                ->will($this->returnValue(0));
        $request->expects($this->at(1))->method('getQuery')
                ->with($paramName, $this->anything())
                ->will($this->returnValue($token));

        $sessionRegistry = $this->getMock('EMRDelegator\Model\SessionRegistry');
        $sessionRegistry->expects($this->once())
                ->method('getIdentityId')
                ->will($this->returnValue($identityId));

        $registryService = $this->getMock('EMRDelegator\Service\Session\Registry');
        $registryService->expects($this->once())
                ->method('getBySsoToken')
                ->with($token)
                ->will($this->returnValue($sessionRegistry));

        $controller = $this->getMock('Service\Controller\UserHasFacilityController', array('getRequest', 'prepareGetListByToken', 'getSessionRegistryService', 'getFacilityListResponse'));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $controller->expects($this->once())->method('prepareGetListByToken')
                ->with($data)
                ->will($this->returnValue($token));
        $controller->expects($this->once())->method('getSessionRegistryService')
                ->will($this->returnValue($registryService));
        $controller->expects($this->once())
                ->method('getFacilityListResponse')
                ->with($identityId)->will($this->returnValue($expectedResult));

        /** @var \Service\Controller\UserHasFacilityController $controller */
        $result = $controller->getByTokenAction();
        $this->assertEquals($expectedResult, $result);
    }

    /**
     *
     */
    public function testGetByTokenWithUserIdAction()
    {
        $paramName = 'wpt_sso_token';
        $token = '1234stuffandthings';
        $data = array($paramName => $token);
        $identityId = 832;
        $expectedResult = 'yeah!';

        $request = $this->getMock('Zend\Http\Request');
        $request->expects($this->at(0))->method('getQuery')
                ->with('userId', 0)
                ->will($this->returnValue($identityId));
        $request->expects($this->at(1))->method('getQuery')
                ->with($paramName, $this->anything())
                ->will($this->returnValue($token));

        $controller = $this->getMock('Service\Controller\UserHasFacilityController', array(
            'getRequest',
            'prepareGetListByToken',
            'validateTokenIsAdmin',
            'getFacilityListResponse',
                )
        );
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $controller->expects($this->once())->method('prepareGetListByToken')
                ->with($data)
                ->will($this->returnValue($token));
        $controller->expects($this->once())
                ->method('getFacilityListResponse')
                ->with($identityId)->will($this->returnValue($expectedResult));
        $controller->expects($this->once())
                ->method('validateTokenIsAdmin')
                ->with($token);

        /** @var \Service\Controller\UserHasFacilityController $controller */
        $result = $controller->getByTokenAction();
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test the get list ghostbrowse
     */
    public function testGetListGhostBrowseAction()
    {
        $identities = array(1, 2);

        $response = new SearchUsersHasFacilityResults;

        $cluster = new Cluster;
        $cluster->setClusterId(1);

        $company = new Company;
        $company->setCompanyId(1);
        $company->setCluster($cluster);

        $result = new SearchUserHasFacilityResult();
        $result->setCompany($company);

        $response->getCollection()->push($result);

        $userHasFacilityService = $this->getMock("EMRDelegator\Service\UserHasFacility\UserHasFacility");
        $userHasFacilityService->expects($this->once())
                ->method("searchUserHasFacilityByIdentities")
                ->with($this->equalTo($identities))
                ->will($this->returnValue($response));


        $this->serviceLocatorMock->expects($this->any())
                ->method('get')
                ->will($this->returnValueMap(
                                array(
                                    array('EMRDelegator\Service\UserHasFacility\UserHasFacility', $userHasFacilityService)
                                )
        ));

        /** @var Request $request */
        $request = new Request;

        /** @var Parameters $query */
        $query = $request->getQuery();
        $query->set("identities", json_encode($identities));

        $request->setPost($query);

        $controller = $this->getMock('Service\Controller\UserHasFacilityController', array('getRequest', 'getContentResponse'));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));

        $controller->setServiceLocator($this->serviceLocatorMock);
        $controller->getListGhostBrowseAction();
    }

}

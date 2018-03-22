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
use EMRDelegator\Cluster\Marshaller\ClusterModelToArray;
use EMRDelegator\Model\Cluster as ClusterModel;
use InvalidArgumentException;
use Service\Controller\ClusterController;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

class ClusterControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClusterController
     */
    private $controller;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var RouteMatch
     */
    private $routeMatch;

    /**
     * @var MvcEvent
     */
    private $mvcEvent;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $serviceLocatorMock;

    public function setUp()
    {
        $this->controller = new ClusterController();
        $this->request = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'cluster'));
        $this->mvcEvent = new MvcEvent();
        $this->mvcEvent->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->mvcEvent);
        $this->controller->setRequestContext(new RequestContext());

        $this->serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $this->controller->setServiceLocator($this->serviceLocatorMock);
    }

    public function testGetList()
    {
        $cluster1 = new ClusterModel();
        $cluster1->setClusterId(1);
        $cluster1->setName('Cluster 1');

        $cluster2 = new ClusterModel();
        $cluster2->setClusterId(2);
        $cluster2->setName('Cluster 2');
        /** @var $clusterArray ClusterModel[] */
        $clusterArray = array($cluster1, $cluster2);


        $clusterService = $this->getMock('EMRDelegator\Service\Cluster\Cluster');
        $clusterService->expects($this->once())->method('getList')->will($this->returnValue($clusterArray));

        $this->serviceLocatorMock->expects($this->any())->method('get')->will($this->returnValueMap(
            array(
                array('EMRDelegator\Service\Cluster\Cluster', $clusterService),
                array('EMRDelegator\Cluster\Marshaller\ClusterModelToArray', new ClusterModelToArray()),
                array('ServiceResponseContent', new Content()),
                array('ServiceResponseError', new Error()),
            )));

        $this->request->setMethod('GET');
        $response = $this->controller->dispatch($this->request);
        $this->assertInstanceOf('EMRCore\Zend\Module\Service\Response\Content',$response);
        $this->assertObjectHasAttribute('content',$response);
        $content = $response->getContent();
        $this->assertArrayHasKey('clusters',$content);
        $responseClusters = $content['clusters'];
        $this->assertCount(2,$responseClusters);
        for($i=0; $i<count($clusterArray); $i++){
            $responseClusterInfo = $responseClusters[$i];
            $expectedCluster = $clusterArray[$i];
            $this->assertEquals($responseClusterInfo['clusterId'],$expectedCluster->getClusterId());
            $this->assertEquals($responseClusterInfo['clusterName'],$expectedCluster->getName());
        }

    }

    public function testGet()
    {
        $clusterModel = new ClusterModel();
        $clusterModel->setClusterId(1);
        $clusterModel->setName('Cluster 1');
        $clusterModel->setMaxFacilityCount(10);
        $clusterModel->setCurrentFacilityCount(5);
        $clusterModel->setOnlineStatus('All');
        $clusterModel->setAcceptingNewCompanies(true);

        $clusterService = $this->getMock('EMRDelegator\Service\Cluster\Cluster');
        $clusterService->expects($this->once())->method('load')->will($this->returnValue($clusterModel));

        $this->serviceLocatorMock->expects($this->any())->method('get')->will($this->returnValueMap(
                array(
                    array('EMRDelegator\Service\Cluster\Cluster', $clusterService),
                    array('EMRDelegator\Cluster\Marshaller\ClusterModelToArray', new ClusterModelToArray()),
                    array('ServiceResponseContent', new Content()),
                    array('ServiceResponseError', new Error()),
                )));

        $this->request->setMethod('GET');
        $this->routeMatch->setParam('id', '1234');
        $response = $this->controller->dispatch($this->request);
        $this->assertInstanceOf('EMRCore\Zend\Module\Service\Response\Content',$response);

        $this->assertObjectHasAttribute('content',$response);
        $content = $response->getContent();
        $this->assertArrayHasKey('cluster',$content);
        $responseCluster = $content['cluster'];

        $this->assertEquals($responseCluster['clusterId'], $clusterModel->getClusterId());
        $this->assertEquals($responseCluster['clusterName'],$clusterModel->getName());
        $this->assertEquals($responseCluster['facilityMax'], $clusterModel->getMaxFacilityCount());
        $this->assertEquals($responseCluster['facilityCurrent'],$clusterModel->getCurrentFacilityCount());
        $this->assertEquals($responseCluster['acceptingNewCompanies'], $clusterModel->getAcceptingNewCompanies());
        $this->assertEquals($responseCluster['onlineStatus'],$clusterModel->getOnlineStatus());
        $this->assertEquals($responseCluster['comment'],$clusterModel->getComment());
    }

    public function testCreate()
    {
        $clusterModel = new ClusterModel();
        $clusterModel->setClusterId(1234);
        $clusterModel->setName('My Cluster');
        $clusterModel->setMaxFacilityCount(10);
        $clusterModel->setCurrentFacilityCount(5);
        $clusterModel->setOnlineStatus('All');
        $clusterModel->setAcceptingNewCompanies(true);

        $clusterService = $this->getMock('EMRDelegator\Service\Cluster\Cluster');
        $clusterService->expects($this->once())->method('create')->will($this->returnValue($clusterModel));

        $this->serviceLocatorMock->expects($this->any())->method('get')->will($this->returnValueMap(
            array(
                array('EMRDelegator\Service\Cluster\Cluster', $clusterService),
                array('EMRDelegator\Cluster\Marshaller\ClusterModelToArray', new ClusterModelToArray()),
                array('EMRCore\Zend\Form\ServiceHelper', new ServiceHelper()),
                array('ServiceResponseContent', new Content()),
            )));

        $data = array(
            'clusterName' => $clusterModel->getName(),
            'facilityMax' => $clusterModel->getMaxFacilityCount()
        );
        $response = $this->controller->create($data);
        $this->assertInstanceOf('EMRCore\Zend\Module\Service\Response\Content',$response);

        $this->assertObjectHasAttribute('content',$response);
        $content = $response->getContent();
        $this->assertArrayHasKey('cluster',$content);
        $this->assertArrayHasKey('clusterId',$content['cluster']);
        $this->assertEquals($clusterModel->getClusterId(),$content['cluster']['clusterId']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidateCreateMissingName()
    {
        $serviceHelper = new ServiceHelper();
        $serviceHelper->setLogger($this->getMock('Logger', array(), array(), '', false));
        $this->serviceLocatorMock->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(
                array(
                    array('EMRCore\Zend\Form\ServiceHelper', $serviceHelper),
                )));
        $clusterData = array();
        $this->controller->create($clusterData);
    }

    public function testUpdate()
    {
        $originalModel = new ClusterModel();
        $originalModel->setClusterId(1);
        $originalModel->setName('Cluster 1');
        $originalModel->setMaxFacilityCount(10);
        $originalModel->setCurrentFacilityCount(5);
        $originalModel->setOnlineStatus('All');
        $originalModel->setAcceptingNewCompanies(true);

        $updatedModel = new ClusterModel();
        $updatedModel->setClusterId(1);
        $updatedModel->setName('Cluster 2');
        $updatedModel->setMaxFacilityCount(20);
        $updatedModel->setCurrentFacilityCount(10);
        $updatedModel->setOnlineStatus('None');
        $updatedModel->setAcceptingNewCompanies(false);

        $clusterId = 1;
        $updateData = array(
            'clusterName' => $updatedModel->getName(),
            'facilityMax' => $updatedModel->getMaxFacilityCount(),
            'facilityCurrent' => $updatedModel->getCurrentFacilityCount(),
            'acceptingNewCompanies' => $updatedModel->getAcceptingNewCompanies(),
            'onlineStatus' => $updatedModel->getOnlineStatus(),
            'comment' => $updatedModel->getComment()
        );

        $clusterService = $this->getMock('EMRDelegator\Service\Cluster\Cluster');
        $clusterService->expects($this->once())->method('load')
            ->with($originalModel->getClusterId())
            ->will($this->returnValue($originalModel));
        $clusterService->expects($this->once())->method('update')
            ->with($updatedModel)
            ->will($this->returnValue($updatedModel));

        $this->serviceLocatorMock->expects($this->any())->method('get')->will($this->returnValueMap(
            array(
                array('EMRDelegator\Service\Cluster\Cluster', $clusterService),
                array('EMRDelegator\Cluster\Marshaller\ClusterModelToArray', new ClusterModelToArray()),
                array('ServiceResponseContent', new Content())
            )));

        $mockController = $this->getMock('Service\Controller\ClusterController', array('validateUpdateData', 'updatePrepareModel'));
        $mockController->expects($this->once())->method('validateUpdateData')
            ->with($updateData)
            ->will($this->returnValue($updateData));
        $mockController->expects($this->once())
            ->method('updatePrepareModel')
            ->with($originalModel, $updateData)
            ->will($this->returnValue($updatedModel));

        /** @var $mockController \Service\Controller\ClusterController */
        $mockController->setServiceLocator($this->serviceLocatorMock);
        $response = $mockController->update($clusterId, $updateData);
        $this->assertInstanceOf('EMRCore\Zend\Module\Service\Response\Content',$response);

        $this->assertObjectHasAttribute('content',$response);
        $content = $response->getContent();
        $this->assertArrayHasKey('success',$content);
        $this->assertTrue($content['success']);
    }

    public function testUpdatePrepareModel()
    {
        $originalModel = new ClusterModel();
        $originalModel->setClusterId(1);
        $originalModel->setName('Cluster 1');
        $originalModel->setMaxFacilityCount(10);
        $originalModel->setOnlineStatus(ClusterModel::STATUS_ALL);
        $originalModel->setAcceptingNewCompanies(true);

        $data = array(
            'clusterName' => 'Cluster 2',
            'facilityMax' => 20,
            'onlineStatus' => ClusterModel::STATUS_NONE,
            'acceptingNewCompanies' => false,
        );

        $updatedModel = $this->controller->updatePrepareModel($originalModel, $data);
        $this->assertEquals($data['clusterName'],$updatedModel->getName());
        $this->assertEquals($data['facilityMax'],$updatedModel->getMaxFacilityCount());
        $this->assertEquals($data['onlineStatus'],$updatedModel->getOnlineStatus());
        $this->assertEquals($data['acceptingNewCompanies'],$updatedModel->getAcceptingNewCompanies());
    }


    public function testDelete()
    {
        $clusterId = 123;

        $clusterService = $this->getMock('EMRDelegator\Service\Cluster\Cluster');
        $clusterService->expects($this->once())->method('delete')
            ->with($clusterId);

        $this->serviceLocatorMock->expects($this->any())->method('get')->will($this->returnValueMap(
            array(
                array('EMRDelegator\Service\Cluster\Cluster', $clusterService),
                array('ServiceResponseContent', new Content())
            )));

        $response = $this->controller->delete($clusterId);
        $this->assertInstanceOf('EMRCore\Zend\Module\Service\Response\Content',$response);

        $this->assertObjectHasAttribute('content',$response);
        $content = $response->getContent();
        $this->assertArrayHasKey('success',$content);
        $this->assertTrue($content['success']);
    }

    public function testGetsClusterIdsWithCompanyIdsAndFacilityIds()
    {
        $data = 1;

        $service = $this->getMock('EMRDelegator\Service\Cluster\Cluster');
        $service->expects($this->once())->method('getListClusterCompanyFacility')
            ->will($this->returnValue($data));

        $marshaller = $this->getMock('Service\Controller\Marshaller\ClusterToClusterIdCompanyIdFacilityIdArray');
        $marshaller->expects($this->once())->method('marshall')
            ->with($data)->will($this->returnValue($data));

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->any())->method('get')
            ->will($this->returnValueMap(array(
                array('EMRDelegator\Service\Cluster\Cluster', $service),
                array('Service\Controller\Marshaller\ClusterToClusterIdCompanyIdFacilityIdArray', $marshaller),
                array('ServiceResponseContent', new Content),
            )));

        $controller = new ClusterController;
        $controller->setServiceLocator($serviceLocator);

        $response = $controller->getClusterIdsWithCompanyIdsAndFacilityIdsAction();

        $actual = $response->getContent();

        $expected = array(
            'clusters' => $data,
        );

        $this->assertSame($expected, $actual);
    }

    public function testGetsClusterIdsWithCompanyIds()
    {
        $data = 1;

        $service = $this->getMock('EMRDelegator\Service\Cluster\Cluster');
        $service->expects($this->once())->method('getListClusterCompany')
            ->will($this->returnValue($data));

        $marshaller = $this->getMock('Service\Controller\Marshaller\ClusterToClusterIdCompanyIdArray');
        $marshaller->expects($this->once())->method('marshall')
            ->with($data)->will($this->returnValue($data));

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->any())->method('get')
            ->will($this->returnValueMap(array(
                array('EMRDelegator\Service\Cluster\Cluster', $service),
                array('Service\Controller\Marshaller\ClusterToClusterIdCompanyIdArray', $marshaller),
                array('ServiceResponseContent', new Content),
            )));

        $controller = new ClusterController;
        $controller->setServiceLocator($serviceLocator);

        $response = $controller->getClusterIdsWithCompanyIdsAction();

        $actual = $response->getContent();

        $expected = array(
            'clusters' => $data,
        );

        $this->assertSame($expected, $actual);
    }
}

<?php
namespace ServiceTest\Unit;

use EMRCore\Zend\Form\ServiceHelper;
use EMRCore\Zend\Module\Service\Response\Content;
use EMRCoreTest\Helper\Reflection;
use EMRCoreTest\PrototypeFactory\Mock as PrototypeFactoryMock;
use EMRDelegator\Company\Marshaller\CompanyModelToArray;
use EMRDelegator\Model\Cluster as ClusterModel;
use EMRDelegator\Model\Company as CompanyModel;
use EMRDelegator\Model\Company;
use EMRDelegator\Service\Company\Dto\SearchCompanyRequest;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Service\Controller\CompanyController;
use stdClass;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;

class CompanyControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $facilityService;
    /**
     * @var CompanyController
     */
    private $controller;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $clusterService;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $companyService;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $serviceLocator;

    public function setUp()
    {
        $this->clusterService = $this->getMock('EMRDelegator\Service\Cluster\Cluster', array(), array(), '', false);
        $this->companyService = $this->getMock('EMRDelegator\Service\Company\Company', array(), array(), '', false);
        $this->facilityService = $this->getMock('EMRDelegator\Service\Facility\Facility', array(), array(), '', false);

        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface', array(), array(), '', false);

        $serviceHelper = new ServiceHelper();
        $serviceHelper->setLogger($this->getMock('Logger', array(), array(), '', false));

        $this->serviceLocator->expects($this->any())->method('get')
            ->will($this->returnValueMap(array(
                array( 'EMRDelegator\Service\Cluster\Cluster', $this->clusterService ),
                array( 'EMRDelegator\Service\Facility\Facility', $this->facilityService ),
                array( 'EMRDelegator\Model\Company', new CompanyModel() ),
                array( 'EMRCore\Zend\Form\ServiceHelper', $serviceHelper ),
                array( 'ServiceResponseContent', new Content() ),
                array( 'EMRDelegator\Company\Marshaller\CompanyModelToArray', new CompanyModelToArray() )
            )));

        $this->controller = new CompanyController();
        $this->controller->setServiceLocator($this->serviceLocator);
        $this->controller->setCompanyService($this->companyService);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotPreparesCreateCompanyDueToCompanyIdIsSet()
    {
        $this->controller->prepareCreateCompany(array(
            'companyId' => 1,
        ));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotPreparesCreateCompanyDueToMissingCompanyName()
    {
        $this->controller->prepareCreateCompany(array());
    }

    public function testPrepareCreateCompanyLoadsClusterById()
    {
        $clusterModel = new ClusterModel();
        $clusterModel->setClusterId(1);

        $companyModel = new CompanyModel();
        $companyModel->setCompanyId(2);
        $companyModel->setName('asdf');
        $companyModel->setCluster($clusterModel);

        $this->clusterService->expects($this->once())->method('load')
            ->with($this->equalTo($clusterModel->getClusterId()))->will($this->returnValue($clusterModel));

        $preparedCompanyModel = $this->controller->prepareCreateCompany(array(
            'name' => $companyModel->getName(),
            'clusterId' => $clusterModel->getClusterId(),
        ));

        $this->assertSame($companyModel->getCluster()->getClusterId(), $preparedCompanyModel->getCluster()->getClusterId());
    }

    public function testPrepareCreateCompanySetsName()
    {
        $clusterModel = new ClusterModel();
        $clusterModel->setClusterId(1);

        $companyModel = new CompanyModel();
        $companyModel->setCompanyId(2);
        $companyModel->setName('asdf');
        $companyModel->setCluster($clusterModel);

        $preparedCompanyModel = $this->controller->prepareCreateCompany(array(
            'name' => $companyModel->getName(),
        ));

        $this->assertSame($companyModel->getName(), $preparedCompanyModel->getName());
    }

    public function testPrepareCreateCompanySetsOnlineStatus()
    {
        $clusterModel = new ClusterModel();
        $clusterModel->setClusterId(1);

        $companyModel = new CompanyModel();
        $companyModel->setCompanyId(2);
        $companyModel->setName('asdf');
        $companyModel->setOnlineStatus('All');
        $companyModel->setCluster($clusterModel);

        $preparedCompanyModel = $this->controller->prepareCreateCompany(array(
            'name' => $companyModel->getName(),
            'onlineStatus' => $companyModel->getOnlineStatus(),
        ));

        $this->assertSame($companyModel->getOnlineStatus(), $preparedCompanyModel->getOnlineStatus());
    }

    public function testPrepareUpdateCompanySetsName()
    {
        $companyModel = new CompanyModel();
        $companyModel->setCompanyId(2);
        $companyModel->setName('asdf');

        $this->companyService->expects($this->once())->method('load')
            ->with($this->equalTo($companyModel->getCompanyId()))->will($this->returnValue(new CompanyModel()));

        $preparedCompanyModel = $this->controller->prepareUpdateCompany($companyModel->getCompanyId(), array(
            'name' => $companyModel->getName(),
        ));

        $this->assertSame($companyModel->getName(), $preparedCompanyModel->getName());
    }

    public function testPrepareUpdateCompanySetsOnlineStatus()
    {
        $companyModel = new CompanyModel();
        $companyModel->setCompanyId(2);
        $companyModel->setName('asdf');
        $companyModel->setOnlineStatus('All');

        $this->companyService->expects($this->once())->method('load')
            ->with($this->equalTo($companyModel->getCompanyId()))->will($this->returnValue(new CompanyModel()));

        $preparedCompanyModel = $this->controller->prepareUpdateCompany($companyModel->getCompanyId(), array(
            'name' => $companyModel->getName(),
            'onlineStatus' => $companyModel->getOnlineStatus(),
        ));

        $this->assertSame($companyModel->getOnlineStatus(), $preparedCompanyModel->getOnlineStatus());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotPreparesDeleteCompanyDueToInvalidCompanyId()
    {
        $this->controller->prepareDeleteCompany(0);
    }

    public function testPrepareDeleteCompanyLoadsCompanyModel()
    {
        $companyModel = new CompanyModel();
        $companyModel->setCompanyId(1);

        $this->companyService->expects($this->once())->method('load')
            ->with($this->equalTo($companyModel->getCompanyId()))->will($this->returnValue($companyModel));

        $preparedCompanyModel = $this->controller->prepareDeleteCompany($companyModel->getCompanyId());

        $this->assertSame($companyModel->getCompanyId(), $preparedCompanyModel->getCompanyId());
    }

    public function testSearch()
    {
        $controller = new CompanyController;

        $query = new Parameters;

        $request = new Request;
        $request->setQuery($query);

        Reflection::set($controller, 'request', $request);

        $requestMarshaller = $this->getMock('\Service\Controller\Marshaller\ParametersToSearchCompanyRequest');
        $requestMarshaller->expects($this->once())->method('marshall')
            ->with($query)->will($this->returnValue($searchCompanyRequest = new SearchCompanyRequest));

        $companyService = $this->getMock('\EMRDelegator\Service\Company\Company');
        $companyService->expects($this->once())->method('searchCompany')
            ->with($searchCompanyRequest)->will($this->returnValue($searchCompanyResponse = new stdClass));

        $responseMarshaller = $this->getMock('stdClass', array('marshall'));
        $responseMarshaller->expects($this->once())->method('marshall')
            ->with($searchCompanyResponse)->will($this->returnValue($marshalledResponse = new stdClass));

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->any())->method('get')
            ->will($this->returnValueMap(array(
                array('EMRDelegator\Service\Company\Marshaller\SearchCompanyResultsToArray', $responseMarshaller),
                array('ServiceResponseContent', $content = new Content),
            )));

        $controller->setParametersToSearchCompanyRequestMarshaller($requestMarshaller);
        $controller->setCompanyService($companyService);
        $controller->setServiceLocator($serviceLocator);

        $this->assertSame($content, $controller->searchAction());
        $this->assertInternalType('array', $content = $content->getContent());
        $this->assertArrayHasKey('companies', $content);
        $this->assertSame($marshalledResponse, $content['companies']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotGetsCompanyDueToInvalidCompanyId()
    {
        $this->controller->getCompany(0);
    }

    /**
     * test getList returns expected results
     */
    public function testGetList()
    {
        $cluster = new ClusterModel();
        $cluster->setClusterId(10);

        $companyModel1 = new CompanyModel();
        $companyModel1->setCompanyId(1);
        $companyModel1->setCluster($cluster);

        $companyModel2 = new CompanyModel();
        $companyModel2->setCompanyId(2);
        $models = array($companyModel1,$companyModel2);
        $companyModel2->setCluster($cluster);

        $this->companyService->expects($this->once())->method('getList')->will($this->returnValue($models));
        $result = $this->controller->getList();
        $this->assertInstanceOf('EMRCore\Zend\Module\Service\Response\Content', $result);
        $response = $result->getResponse();
        $companies = $response['response']['companies'];
        $this->assertCount(2, $companies);
        $this->assertEquals(1, $companyModel1->getCompanyId());
        $this->assertEquals(2, $companyModel2->getCompanyId());
    }

    /**
     * Test that set migration calls update with the company Id and migration status
     */
    public function testSetMigrationStatusCallsUpdate()
    {
        $companyId = 832;
        $migrationStatus = 'Doing Stuff';
        $data = array('companyId'=>$companyId, 'migrationStatus' => $migrationStatus);
        $expectedResult = array('company'=>'ok');

        $params = new Parameters();
        $params->fromArray(array('companyId'=>$companyId, 'migrationStatus'=>$migrationStatus));

        $request = new Request();
        $request->setQuery($params);

        $model = new Company();

        $controllerMock = $this->getMock('Service\Controller\CompanyController',
            array('getRequest','validateSetStatusData', 'updateCompany', 'getMarshalledCompany'));
        $controllerMock->expects($this->once())->method('getRequest')
            ->will($this->returnValue($request));
        $controllerMock->expects($this->once())->method('validateSetStatusData')
            ->with($data)
            ->will($this->returnValue($data));
        $controllerMock->expects($this->once())->method('updateCompany')
            ->with($companyId, $data)
            ->will($this->returnValue($model));
        $controllerMock->expects($this->once())->method('getMarshalledCompany')
            ->with($model)
            ->will($this->returnValue($expectedResult));

        /** @var \Service\Controller\CompanyController $controllerMock */
        $controllerMock->setServiceLocator($this->serviceLocator);
        $result = $controllerMock->setMigrationStatusAction();

        $this->assertInstanceOf('EMRCore\Zend\Module\Service\Response\Content', $result);
        $response = $result->getResponse();
        $this->assertEquals($response['response']['company'], 'ok');
    }

}
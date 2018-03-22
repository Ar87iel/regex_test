<?php
namespace EMRAdminTest\unit\tests\Service\Company\Dao;

use EMRAdmin\Service\Company\Dao\Esb;
use EMRAdmin\Service\Company\Dto\GetCompaniesWithFacilitiesRequest;
use EMRAdmin\Service\Company\Dto\SaveCompanyRequest;
use EMRAdmin\Service\Company\Dto\Search\SearchCompanyCollection;
use EMRAdmin\Service\Company\Marshaller\Search\StdClassToSearchCompany;
use EMRAdmin\Service\Company\Marshaller\Search\SuccessToSearchCompaniesResponse;
use EMRAdmin\Service\Company\Marshaller\SuccessToGetCompaniesResponse;
use EMRAdmin\Service\Company\Dto\GetCompaniesResponse;
use EMRAdmin\Service\Company\Dto\CompanyFromDelegator;
use EMRCore\Config\Service\PrivateService\Esb\Dto\Route;
use EMRCore\EsbFactory;
use EMRCore\Marshaller\DtoToArray;
use EMRCore\Zend\Http\ClientWrapper;
use EMRCore\Zend\Http\Request\Pagination\Dto\Pagination;
use EMRCore\Zend\Http\Request\Pagination\Marshaller\DtoToArray as PaginationDtoToArray;
use EMRCore\Zend\module\Service\src\Response\Parser\Json;
use EMRCoreTest\lib\PhpUnit\SingletonTestCaseHelper;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Zend\Http\Response;
use EMRAdmin\Service\Company\Dto\SearchLite\SearchCompanyLiteCollection;

/**
 * This TestCase should generally include one test per assertion.
 * Mock the Application config and provide mocked endpoints and request methods.
 * Mock an HTTPClient and return it from the mocked EsbFactory.
 * Test that the HTTPClient produced by the mocked EsbFactory is called using the mocked config.
 *
 * @category WebPT
 * @package EMRAdmin
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class EsbTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    public $mockEsbFactory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    public $mockServiceLocator;

    /**
     * @var SingletonTestCaseHelper
     */
    private $singletonTestCaseHelper;

    /**
     * @var Esb
     */
    private $dao;

    public function setUp()
    {
        $this->singletonTestCaseHelper = new SingletonTestCaseHelper($this);

        $esbFactoryClass = 'EMRCore\EsbFactory';
        $this->mockEsbFactory = $this->getMock($esbFactoryClass, array('getClient'), array(), '', false);

        $this->singletonTestCaseHelper->mockSingleton($this->mockEsbFactory, $esbFactoryClass);

        $this->mockServiceLocator = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');

        $this->dao = new Esb();
        $this->dao->setServiceLocator($this->mockServiceLocator);
    }

    public function tearDown()
    {
        $this->singletonTestCaseHelper->unmockSingletons();
    }

    public function testSaveCompanyBuildsClientWithRoute()
    {
        // A fake parameter list.
        $esbParameters = array();

        // A fake ESB route.
        $route = new Route();
        $route->setName('asdf');
        $route->setUri('qwer');
        $route->setMethod('zxcv');

        // A fake response.
        $esbResponse = json_encode(array(
            'content' => array(
                'response' => array(
                    'stuff' => 'things',
                ),
            ),
        ));

        // A fake request.
        $saveCompanyRequest = new SaveCompanyRequest();

        // Mock the array marshaller to return ESB parameters when the request is received.
        $mockArrayMarshaller = $this->createMock('EMRAdmin\Service\Company\Marshaller\SaveCompanyRequestToArray');
        $mockArrayMarshaller->expects($this->once())->method('marshall')
                ->with($this->equalTo($saveCompanyRequest))->will($this->returnValue($esbParameters));

        // Mock the routes config to return our fake route, always.
        $mockRoutes = $this->createMock('EMRCore\Config\Service\PrivateService\Esb\Routes');
        $mockRoutes->expects($this->once())->method('getRouteByName')
                ->with($this->anything())->will($this->returnValue($route));

        // Mock the dto marshaller so that this test does not blow up when marshall is called.
        $mockDtoMarshaller = $this->createMock('EMRAdmin\Service\Company\Marshaller\SuccessToSaveCompanyResponse');
        $mockDtoMarshaller->expects($this->once())->method('marshall')
                ->with($this->anything())->will($this->returnValue(null));

        // Stub service locator calls to return the mocks.
        $this->mockServiceLocator->expects($this->any())->method('get')
                ->withAnyParameters()
                ->will($this->returnCallback(function($name) use ($mockArrayMarshaller, $mockRoutes, $mockDtoMarshaller)
                                {

                                    if ($name === 'EMRAdmin\Service\Company\Marshaller\SaveCompanyRequestToArray')
                                    {
                                        return $mockArrayMarshaller;
                                    }

                                    if ($name === 'EMRCore\Config\Service\PrivateService\Esb\Routes')
                                    {
                                        return $mockRoutes;
                                    }

                                    if ($name === 'EMRAdmin\Service\Company\Marshaller\SuccessToSaveCompanyResponse')
                                    {
                                        return $mockDtoMarshaller;
                                    }

                                    if ($name === 'EMRCore\Zend\module\Service\src\Response\Parser\Json')
                                    {
                                        return null;
                                    }

                                    throw new InvalidArgumentException("Mock ServiceLocatorInterface could not create [$name]");
                                }));

        // Mock the client wrapper and ensure that execute is called. This is how the ESB request is sent.
        $mockClientWrapper = $this->createMock('EMRCore\Zend\Http\ClientWrapper');
        $mockClientWrapper->expects($this->once())->method('execute')->will($this->returnValue($esbResponse));

        // Ensure that the ESB factory returns the mock client wrapper when supplied with the route parameters.
        $this->mockEsbFactory->expects($this->once())->method('getClient')
                ->with($this->equalTo($route->getUri()), $this->equalTo($route->getMethod()), $this->equalTo($esbParameters))
                ->will($this->returnValue($mockClientWrapper));

        $this->dao->saveCompany($saveCompanyRequest);
    }

    public function testGetCompanyByIdBuildsClientWithRoute()
    {
        $companyId = 1;

        // A fake parameter list.
        $esbParameters = array();

        // A fake ESB route.
        $route = new Route();
        $route->setName('asdf');
        $route->setUri('qwer');
        $route->setMethod('zxcv');

        $esbRouteUri = $route->getUri() . '/' . $companyId;

        // A fake response.
        $esbResponse = json_encode(array(
            'content' => array(
                'response' => array(
                    'stuff' => 'things',
                ),
            ),
        ));

        // Mock the routes config to return our fake route, always.
        $mockRoutes = $this->createMock('EMRCore\Config\Service\PrivateService\Esb\Routes');
        $mockRoutes->expects($this->once())->method('getRouteByName')
                ->with($this->anything())->will($this->returnValue($route));

        // Mock the dto marshaller so that this test does not blow up when marshall is called.
        $mockDtoMarshaller = $this->createMock('EMRAdmin\Service\Company\Marshaller\SuccessToGetCompanyByIdResponse');
        $mockDtoMarshaller->expects($this->once())->method('marshall')
                ->with($this->anything())->will($this->returnValue(null));

        // Stub service locator calls to return the mocks.
        $this->mockServiceLocator->expects($this->any())->method('get')
                ->withAnyParameters()
                ->will($this->returnCallback(function($name) use ($mockRoutes, $mockDtoMarshaller)
                                {

                                    if ($name === 'EMRCore\Config\Service\PrivateService\Esb\Routes')
                                    {
                                        return $mockRoutes;
                                    }

                                    if ($name === 'EMRAdmin\Service\Company\Marshaller\SuccessToGetCompanyByIdResponse')
                                    {
                                        return $mockDtoMarshaller;
                                    }

                                    if ($name === 'EMRCore\Zend\module\Service\src\Response\Parser\Json')
                                    {
                                        return null;
                                    }

                                    throw new InvalidArgumentException("Mock ServiceLocatorInterface could not create [$name]");
                                }));

        // Mock the client wrapper and ensure that execute is called. This is how the ESB request is sent.
        $mockClientWrapper = $this->createMock('EMRCore\Zend\Http\ClientWrapper');
        $mockClientWrapper->expects($this->once())->method('execute')->will($this->returnValue($esbResponse));

        // Ensure that the ESB factory returns the mock client wrapper when supplied with the route parameters.
        $this->mockEsbFactory->expects($this->once())->method('getClient')
                ->with($this->equalTo($esbRouteUri), $this->equalTo($route->getMethod()), $this->equalTo($esbParameters))
                ->will($this->returnValue($mockClientWrapper));

        $this->dao->getCompanyById($companyId);
    }

    /**
     * This test proves that when a valid JSON string is returned from the ESB that the SearchCompanies
     * marshaller will be executed correctly.
     *
     * The ESB request that we are testing has a complicated return value that requires the use of many
     * marshaller objects. Each marshaller has tests to prove its functionality.
     *
     * This test proves that the process is bootstrapped correctly when given a real input.
     */
    public function testGetsCompaniesWithFacilities()
    {
        // Create a DAO and set some mocked dependencies.
        $dao = new Esb;

        $pagination = new Pagination;

        $paginationService = $this->getMock('\EMRCore\Zend\Http\Request\Pagination\Pagination', array(
            'getPagination',
        ));

        $paginationService->expects($this->once())->method('getPagination')
            ->will($this->returnValue($pagination));

        $dao->setPaginationService($paginationService);
        $dao->setPaginationDtoToArrayMarshaller($paginationMarshaller = new PaginationDtoToArray);

        $paginationMarshaller->setPaginationService($paginationService);

        // Create a mock EsbFactory (for mock requests) and set the dependency.
        $esbFactory = $this->getMock('EMRCore\EsbFactory', array(), array(), '', false);
        $dao->setEsbFactory($esbFactory);

        // Create a mock PrototypeFactory and set the dependency.
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        // Create a mock ServiceLocator and set the dependency.
        $serviceLocator = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');
        $dao->setServiceLocator($serviceLocator);

        // Create a fake route.
        $route = new Route;
        $route->setUri('asdf');
        $route->setMethod('qwer');

        // Always use the fake route for our request.
        $routes = $this->createMock('EMRCore\Config\Service\PrivateService\Esb\Routes');
        $routes->expects($this->once())->method('getRouteByName')
                ->with($this->anything())->will($this->returnValue($route));

        $successToSearchCompaniesMarshaller = new SuccessToSearchCompaniesResponse;
        $successToSearchCompaniesMarshaller->setServiceLocator($serviceLocator);

        // Return some services from our mock ServiceLocator
        $serviceLocator->expects($this->any())->method('get')
            ->will($this->returnValueMap(array(
                array('EMRCore\Config\Service\PrivateService\Esb\Routes', $routes),
                array('EMRCore\Zend\module\Service\src\Response\Parser\Json', new Json),
                array('EMRAdmin\Service\Company\Marshaller\Search\SuccessToSearchCompaniesResponse', $successToSearchCompaniesMarshaller),
                array('EMRAdmin\Service\Company\Marshaller\Search\StdClassToSearchCompany', new StdClassToSearchCompany),
                array('EMRCore\Marshaller\DtoToArray', new DtoToArray),
            )));

        // Create a mock response JSON string for our test.
        $content = json_encode(array(
            'content' => array(
                'response' => array(
                    'companies' => array(
                    ),
                ),
            ),
        ));

        // Return the mock response string.
        $response = new Response;
        $response->setStatusCode(200);
        $response->setContent($content);

        // Return the mock response.
        $client = $this->createMock('Zend\Http\Client');
        $client->expects($this->once())->method('send')
                ->will($this->returnValue($response));

        // Return the mock client.
        $clientWrapper = new ClientWrapper;
        $clientWrapper->setLogger($this->getMock('Logger', array(), array(), '', false));
        $clientWrapper->setClient($client);

        // Always produce the fake ClientWrapper in the mock EsbFactory.
        $esbFactory->expects($this->any())
            ->method('getClient')
            ->with($this->equalTo($route->getUri()), $this->equalTo($route->getMethod()), $this->anything())
            ->will($this->returnValue($clientWrapper));

        // Create a fake request.
        $request = new GetCompaniesWithFacilitiesRequest;

        // Run the service!
        $collection = $dao->getCompaniesWithFacilitiesByCriteria($request);

        // This proves that we get an empty collection.
        $this->assertCount(0, $collection);
    }

    /**
     * This tests proves that when an appropriate response is returned by the ESB and the Delegator, it will be 
     * correctly marshalled and returned as a DTO containing a collection of companies
     */
    public function testGetCompanies()
    {
        // Create a DAO and set some mocked dependencies.
        $dao = new Esb;

        // Create a mock EsbFactory (for mock requests) and set the dependency.
        $esbFactory = $this->getMock('EMRCore\EsbFactory', array(), array(), '', false);
        $dao->setEsbFactory($esbFactory);

        // Create a mock PrototypeFactory and set the dependency.
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        // Create a mock ServiceLocator and set the dependency.
        $serviceLocator = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');
        $dao->setServiceLocator($serviceLocator);

        // Create a fake route.
        $route = new Route;
        $route->setUri('asdf');
        $route->setMethod('qwer');

        // Always use the fake route for our request.
        $routes = $this->createMock('EMRCore\Config\Service\PrivateService\Esb\Routes');
        $routes->expects($this->once())
            ->method('getRouteByName')
            ->with($this->anything())
            ->will($this->returnValue($route));

        // Return some services from our mock ServiceLocator
        $serviceLocator->expects($this->any())
            ->method('get')
             ->will($this->returnCallback(
                function($name) use ($routes, $prototypeFactory, $serviceLocator)
                {
                    switch ($name)
                    {
                        case 'EMRCore\Config\Service\PrivateService\Esb\Routes':
                            return $routes;
                            break;
                        case 'EMRCore\Zend\module\Service\src\Response\Parser\Json':
                            return new Json();
                            break;
                        case 'EMRAdmin\Service\Company\Marshaller\SuccessToGetCompaniesResponse':

                            $companiesMarshaller = new SuccessToGetCompaniesResponse();

                            $companiesMarshaller->setPrototypeFactory($prototypeFactory);

                            return $companiesMarshaller;
                            break;
                        default :
                            throw new InvalidArgumentException("Mock ServiceLocatorInterface cannot provide [$name].");
                            break;
                    }
                }));

        // Return some instances from our mock PrototypeFactory.
        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name) use ($routes)
                {
                    switch ($name)
                    {
                        case 'EMRAdmin\Service\Company\Dto\Search\SearchCompanyCollection':
                            return new SearchCompanyCollection;
                            break;
                        case 'EMRAdmin\Service\Company\Dto\GetCompaniesResponse':

                            $companiesResponse = new GetCompaniesResponse();
                            return $companiesResponse;
                            break;
                        case 'EMRAdmin\Service\Company\Dto\CompanyFromDelegator':
                            return new CompanyFromDelegator();
                            break;
                        default :
                            throw new InvalidArgumentException("Mock PrototypeFactory cannot provide [$name].");
                            break;
                    }
                }));

        // Create a mock response JSON string for our test.
        $arrContentToTest = array(
            'content' => array(
                'response' => array(
                    'companies' => array(
                        array(
                            'companyId' => 1,
                            'companyName' => 'My company name',
                            'onlineStatus' => 'A',
                            'clusterId' => 1
                        ),
                        array(
                            'companyId' => 2,
                            'companyName' => 'My other company',
                            'onlineStatus' => 'I',
                            'clusterId' => 2
                        )
                    ),
                ),
            ),
        );

        $content = json_encode($arrContentToTest);

        // Return the mock response string.
        $response = new Response;
        $response->setStatusCode(200);
        $response->setContent($content);

        // Return the mock response.
        $client = $this->createMock('Zend\Http\Client');
        $client->expects($this->once())->method('send')
                ->will($this->returnValue($response));

        // Return the mock client.
        $clientWrapper = new ClientWrapper;
        $clientWrapper->setLogger($this->getMock('Logger', array(), array(), '', false));
        $clientWrapper->setClient($client);

        // Always produce the fake ClientWrapper in the mock EsbFactory.
        $esbFactory->expects($this->any())
            ->method('getClient')
            ->with($this->equalTo($route->getUri()), $this->equalTo($route->getMethod()), $this->anything())
            ->will($this->returnValue($clientWrapper));

        $dao->setEsbFactory($esbFactory);

        // Run the service!
        $collection = $dao->getCompanies();

        $elements = $collection->getElements();

        // This proves that we get a collection with the same number of companies in the array.
        $this->assertCount(count($arrContentToTest['content']['response']['companies']), $elements);

        /*
         * The following lines prove that every element in the array remains the same after being converted to a JSON
         * string and after being marshalled to a companies collection
         */
        $this->assertSame($arrContentToTest['content']['response']['companies'][0]['companyId'], $elements[0]->getCompanyId());
        $this->assertSame($arrContentToTest['content']['response']['companies'][0]['companyName'], $elements[0]->getName());
        $this->assertSame($arrContentToTest['content']['response']['companies'][0]['onlineStatus'], $elements[0]->getOnlineStatus());
        $this->assertSame($arrContentToTest['content']['response']['companies'][0]['clusterId'], $elements[0]->getClusterId());

        $this->assertSame($arrContentToTest['content']['response']['companies'][1]['companyId'], $elements[1]->getCompanyId());
        $this->assertSame($arrContentToTest['content']['response']['companies'][1]['companyName'], $elements[1]->getName());
        $this->assertSame($arrContentToTest['content']['response']['companies'][1]['onlineStatus'], $elements[1]->getOnlineStatus());
        $this->assertSame($arrContentToTest['content']['response']['companies'][1]['clusterId'], $elements[1]->getClusterId());
    }

    /**
     * This test proves that when a valid JSON string is returned from the ESB that the SearchCompanies
     * marshaller will be executed correctly.
     *
     * The ESB request that we are testing has a complicated return value that requires the use of many
     * marshaller objects. Each marshaller has tests to prove its functionality.
     *
     * This test proves that the process is bootstrapped correctly when given a real input.
     */
    public function testGetsCompaniesWithFacilitiesLite()
    {
        // Create a DAO and set some mocked dependencies.
        $dao = new Esb;

        //A fake query request
        $searchQuery = 'asd';

        // A fake parameter list.
        $esbParameters = array(
            'searchQuery' => $searchQuery
        );

        // Create a mock EsbFactory (for mock requests) and set the dependency.
        $esbFactory = $this->getMock('EMRCore\EsbFactory', array(), array(), '', false);
        $dao->setEsbFactory($esbFactory);

        // Create a mock ServiceLocator and set the dependency.
        $serviceLocator = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');
        $dao->setServiceLocator($serviceLocator);

        // Create a fake route.
        $route = new Route;
        $route->setUri('asdf');
        $route->setMethod('qwer');

        // Always use the fake route for our request.
        $routes = $this->createMock('EMRCore\Config\Service\PrivateService\Esb\Routes');
        $routes->expects($this->once())
            ->method('getRouteByName')
            ->with($this->anything())
            ->will($this->returnValue($route));

        $marshallerMock = $this->getMock('EMRAdmin\Service\Company\Marshaller\Search\SuccessToSearchCompaniesLiteResponse', array('marshall'));
        $marshallerMock->expects($this->once())
            ->method('marshall')
            ->will($this->returnValue(new SearchCompanyLiteCollection));

        // Return some services from our mock ServiceLocator
        $serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(
                function($name) use ($routes, $marshallerMock)
                {
                    if ($name === 'EMRCore\Config\Service\PrivateService\Esb\Routes')
                    {
                        return $routes;
                    }

                    if ($name == 'EMRAdmin\Service\Company\Marshaller\Search\SuccessToSearchCompaniesLiteResponse')
                    {
                        return $marshallerMock;
                    }
                    if ($name === 'EMRCore\Zend\module\Service\src\Response\Parser\Json')
                    {
                        return new Json;
                    }

                    throw new InvalidArgumentException("Mock ServiceLocatorInterface cannot provide [$name].");
                }));

        // Create a mock response JSON string for our test.
        $content = json_encode(array(
            'content' => array(
                'response' => array(
                    'companies' => array(
                    ),
                ),
            ),
        ));

        // Return the mock response string.
        $response = new Response;
        $response->setStatusCode(200);
        $response->setContent($content);

        // Return the mock response.
        $client = $this->createMock('Zend\Http\Client');
        $client->expects($this->once())->method('send')
                ->will($this->returnValue($response));

        // Return the mock client.
        $clientWrapper = new ClientWrapper;
        $clientWrapper->setLogger($this->getMock('Logger', array(), array(), '', false));
        $clientWrapper->setClient($client);

        // Always produce the fake ClientWrapper in the mock EsbFactory.
        $esbFactory->expects($this->any())
            ->method('getClient')
            ->with($this->equalTo($route->getUri()), $this->equalTo($route->getMethod()), $this->EqualTo($esbParameters))
            ->will($this->returnValue($clientWrapper));

        // Run the service!
        $collection = $dao->getCompaniesWithFacilitiesLiteByCriteria($searchQuery);

        // This proves that we get an empty collection.
        $this->assertCount(0, $collection);
    }

}
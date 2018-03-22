<?php

namespace EMRAdminTest\unit\tests\Service\GhostBrowse\Dao;

use EMRAdmin\Service\GhostBrowse\Dao\Esb;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseRequest;
use EMRAdmin\Service\GhostBrowse\Marshaller\Search\SuccessToGhostBrowseResponse;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseCollection;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponse;
use EMRAdmin\Service\GhostBrowse\Marshaller\StdClassToSearchGhostBrowse;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseUserCollection;
use EMRAdmin\Service\GhostBrowse\Marshaller\Search\UserPayloadToUser;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseUser;
use EMRAdmin\Service\GhostBrowse\Dto\Search\UsersByCompanyId as UsersByCompanyIdDto;
use EMRCore\Config\Service\PrivateService\Esb\Dto\Route;
use EMRCore\Zend\Http\ClientWrapper;
use EMRCore\Zend\module\Service\src\Response\Parser\Json;
use EMRCoreTest\lib\PhpUnit\SingletonTestCaseHelper;
use EMRCore\EsbFactory;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Zend\Http\Response;
use EMRAdmin\Service\Company\Dto\SearchLite\SearchCompanyLiteCollection;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class GhostEsbTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var EsbFactory|PHPUnit_Framework_MockObject_MockObject
     */
    public $mockEsbFactory;

    /**
     * @var ServiceLocatorInterface|PHPUnit_Framework_MockObject_MockObject
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
    
    /*
     * 
     */
    private $mockPrototypeFactory;

    /**
     * set up the esb
     */
    public function setUp()
    {
        $this->singletonTestCaseHelper = new SingletonTestCaseHelper($this);

        $esbFactoryClass = 'EMRCore\EsbFactory';
        $this->mockEsbFactory = $this->getMock($esbFactoryClass, array(), array(), '', false);

        $this->singletonTestCaseHelper->mockSingleton($this->mockEsbFactory, $esbFactoryClass);

        $this->mockServiceLocator = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');
        
        $this->mockPrototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false );

        $this->dao = new Esb();
        $this->dao->setServiceLocator($this->mockServiceLocator);
    }

    public function tearDown()
    {
        $this->singletonTestCaseHelper->unmockSingletons();
    }

    /**
     * test the GetGhostGhostByCritera method inside the DAO
     */
    public function testGetGhostByCriteria()
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
                        case 'EMRAdmin\Service\GhostBrowse\Marshaller\Search\SuccessToGhostBrowseResponse':
                            $marshaller = new SuccessToGhostBrowseResponse();
                            $marshaller->setPrototypeFactory($prototypeFactory);
                            $marshaller->setServiceLocator($serviceLocator);
                            return $marshaller;
                            break;
                        case 'EMRAdmin\Service\GhostBrowse\Marshaller\StdClassToSearchGhostBrowse':
                            $marshaller = new StdClassToSearchGhostBrowse();

                            $marshaller->setPrototypeFactory($prototypeFactory);
                            $marshaller->setServiceLocator($serviceLocator);

                            return $marshaller;
                            break;
                        case 'EMRAdmin\Service\GhostBrowse\Marshaller\Search\UserPayloadToUser':
                            $marshaller = new UserPayloadToUser();
                            $marshaller->setPrototypeFactory($prototypeFactory);
                            $marshaller->setServiceLocator($serviceLocator);
                            return $marshaller;
                            break;
                        default :
                            throw new InvalidArgumentException("Mock ServiceLocatorInterface cannot provide [$name].");
                            break;
                    }
                }));

        // Create a mock response JSON string for our test.
        $arrConstructArray = array(
            'content' => array(
                'response' => array(
                    'facilities' => array(
                        array(
                            'id' => 1,
                            'name' => 'facility name',
                            'users' => array(
                                array(
                                    'userId' => 1,
                                    'userName' => 'user',
                                    'firstName' => 'John',
                                    'lastName' => 'Doe',
                                    'userType' => 'PT',
                                    'companyAdmin' => true,
                                    'facilityAdmin' => false,
                                    'status' => 'A'
                                ),
                                array(
                                    'userId' => 2,
                                    'userName' => 'user2',
                                    'firstName' => 'Jane',
                                    'lastName' => 'Doe',
                                    'userType' => 'PTA',
                                    'companyAdmin' => false,
                                    'facilityAdmin' => false,
                                    'status' => 'A'
                                )
                            )
                        ),
                        array(
                            'id' => 2,
                            'name' => 'facility 2 name',
                            'users' => array(
                                array(
                                    'userId' => 3,
                                    'userName' => 'user3',
                                    'firstName' => 'John',
                                    'lastName' => 'Smith',
                                    'userType' => 'PT',
                                    'companyAdmin' => false,
                                    'facilityAdmin' => true,
                                    'status' => 'A'
                                )
                            )
                        )
                    ),
                ),
            ),
        );

        $content = json_encode($arrConstructArray);

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

        // Return some instances from our mock PrototypeFactory.
        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name)
                {
                    switch ($name)
                    {
                        case 'EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseCollection':
                            return new SearchGhostBrowseResponseCollection();
                            break;
                        case 'EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponse':
                            return new SearchGhostBrowseResponse();
                            break;
                        case 'EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseUserCollection':
                            return new SearchGhostBrowseResponseUserCollection();
                            break;
                        case 'EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseUser':
                            return new SearchGhostBrowseResponseUser();
                            break;
                        default :
                            throw new InvalidArgumentException("Mock PrototypeFactory cannot provide [$name].");
                            break;
                    }
                }));

        // Create a fake request.
        $request = new SearchGhostBrowseRequest();

        // Run the service!
        /**
         * @var SearchGhostBrowseResponseCollection $collection
         */
        $collection = $dao->getGhostByCriteria($request);

        // Proves that the collection returned is of SearchGhostBrowseResponseCollection type
        $this->assertTrue($collection instanceof SearchGhostBrowseResponseCollection);

        $facilities = $collection->getElements();

        //Proves that the same number of facilites inside the contruct array is present after marshalling
        $this->assertSame(count($arrConstructArray['content']['response']['facilities']), count($facilities));

        //proves that the first facility's data inside the construct array stays the same after marshalling
        $this->assertEquals($arrConstructArray['content']['response']['facilities'][0]['id'], $facilities[0]->getId());
        $this->assertEquals($arrConstructArray['content']['response']['facilities'][0]['name'], $facilities[0]->getName());

        $users = $facilities[0]->getUsers()->getElements();

        //Proves that the user data inside the facility stays the same after marshalling
        $this->assertEquals($arrConstructArray['content']['response']['facilities'][0]['users'][1]['userId'], $users[1]->getUserId());
        $this->assertEquals($arrConstructArray['content']['response']['facilities'][0]['users'][1]['userName'], $users[1]->getUserName());
    }

    /*
     * Test the ghostEsb to search by username
     */
    public function testGetGhostByUsername()
    {
        $username = "asd";

        // Create a fake route.
        $route = new Route;
        $route->setUri('asdf');
        $route->setMethod('qwer');

        //A fake User
        $user = array(
            'id' => 1,
            'fullname' => "asd",
            'username' => 'asd'
        );

        //A fake facility
        $facility = array(
            'name' => "asd",
            'id' => 1,
            'users' => $user,
        );

        //A fake content
        $content = json_encode(
                array(
                    'content' => array(
                        'response' => array(
                            'companies' => array(
                                'name' => 'asd',
                                'id' => 1,
                                'facilities' => $facility,
                            )
                        ),
                    ),
        ));

        $companies = new SearchCompanyLiteCollection();

        // Always use the fake route for our request.
        $routes = $this->createMock('EMRCore\Config\Service\PrivateService\Esb\Routes');

        $routes->expects($this->once())
                ->method('getRouteByName')
                ->with($this->anything())
                ->will($this->returnValue($route));

        $marshaller = $this->createMock('EMRAdmin\Service\GhostBrowse\Marshaller\Search\SuccessToGhostBrowseSearchResponse');
        $marshaller->expects($this->once())
                ->method('marshall')
                ->with($this->anything())
                ->will($this->returnValue($companies));

        // Return some services from our mock ServiceLocator
        $this->mockServiceLocator->expects($this->any())->method('get')
                ->will($this->returnCallback(function($name) use ($routes, $marshaller)
                                {
                                    switch ($name)
                                    {
                                        case 'EMRCore\Config\Service\PrivateService\Esb\Routes':
                                            return $routes;
                                            break;
                                        case 'EMRCore\Zend\module\Service\src\Response\Parser\Json':
                                            return new Json();
                                            break;
                                        case 'EMRAdmin\Service\GhostBrowse\Marshaller\Search\SuccessToGhostBrowseSearchResponse':
                                            return $marshaller;
                                            break;
                                        default :
                                            throw new InvalidArgumentException("Mock ServiceLocatorInterface cannot provide [$name].");
                                            break;
                                    }
                                }));

        // Return the mock response string.
        $response = new Response;
        $response->setStatusCode(200);
        $response->setContent($content);

        // Return the mock response.
        $client = $this->createMock('Zend\Http\Client');
        $client->expects($this->once())->method('send')
                ->will($this->returnValue($response));

        // Return the mock client.
        $clientWrapper = new ClientWrapper();
        $clientWrapper->setLogger($this->getMock('Logger', array(), array(), '', false));
        $clientWrapper->setClient($client);

        // Always produce the fake ClientWrapper in the mock EsbFactory.
        $this->mockEsbFactory->expects($this->any())
            ->method('getClient')
            ->with($this->equalTo($route->getUri()), $this->equalTo($route->getMethod()), $this->anything())
            ->will($this->returnValue($clientWrapper));

        $this->dao->setEsbFactory($this->mockEsbFactory);
        $this->dao->getGhostByUsername($username);
    }
    
    /*
     * Test the ghostEsb to search by username
     */
    public function testGetGhostByCompanyId()
    {
        $companyId = array(
            'companyId' => 32
        );

        // Create a fake route.
        $route = new Route;
        $route->setUri('asdf');
        $route->setMethod('qwer');

        //A fake User
        $user = array(
            'id' => 1,
            'fullName' => "asd",
            'userName' => 'asd',
            'userType' => 2,
            'userStatus' => 'I',
            'isAdmin' => 0
        );

        //A fake facility
        $facility = array(
            'name' => "asd",
            'facilityId' => 1,
            'users' => $user,
        );

        //A fake content
        $content = json_encode(
                array(
                    'content' => array(
                        'response' => array(
                            $facility
                        ),
                    ),
        ));

        $companies = new UsersByCompanyIdDto();

        // Always use the fake route for our request.
        $routes = $this->createMock('EMRCore\Config\Service\PrivateService\Esb\Routes');

        $routes->expects($this->once())
                ->method('getRouteByName')
                ->with($this->anything())
                ->will($this->returnValue($route));

        $marshaller = $this->createMock('EMRAdmin\Service\GhostBrowse\Marshaller\Search\UsersByCompanyId');
        $marshaller->expects($this->once())
                ->method('marshall')
                ->with($this->anything())
                ->will($this->returnValue($companies));

        // Return some services from our mock ServiceLocator
        $this->mockServiceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(
                function($name) use ($routes, $marshaller)
                {
                    switch ($name)
                    {
                        case 'EMRCore\Config\Service\PrivateService\Esb\Routes':
                            return $routes;
                            break;
                        case 'EMRCore\Zend\module\Service\src\Response\Parser\Json':
                            return new Json();
                            break;
                        case 'EMRAdmin\Service\GhostBrowse\Marshaller\Search\UsersByCompanyId':
                            return $marshaller;
                            break;
                        default :
                            throw new InvalidArgumentException("Mock ServiceLocatorInterface cannot provide [$name].");
                            break;
                    }
                }));

        // Return the mock response string.
        $response = new Response;
        $response->setStatusCode(200);
        $response->setContent($content);

        // Return the mock response.
        $client = $this->createMock('Zend\Http\Client');
        $client->expects($this->once())->method('send')
                ->will($this->returnValue($response));

        // Return the mock client.
        $clientWrapper = new ClientWrapper();
        $clientWrapper->setLogger($this->getMock('Logger', array(), array(), '', false));
        $clientWrapper->setClient($client);

        // Always produce the fake ClientWrapper in the mock EsbFactory.
        $this->mockEsbFactory->expects($this->any())
            ->method('getClient')
            ->with($this->equalTo($route->getUri()), $this->equalTo($route->getMethod()), $this->anything())
            ->will($this->returnValue($clientWrapper));

        $this->dao->setEsbFactory($this->mockEsbFactory);
        $this->dao->getSearchByCompanyId($companyId);
    }

}
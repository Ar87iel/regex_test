<?php

namespace EMRAdminTest\unit\tests\Service\UniqueUsername\Dao;

use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseCollection;
use EMRAdmin\Service\UniqueUsername\Dao\Esb;
use EMRAdmin\Service\UniqueUsername\Dto\GetUniqueUsernameRequest;
use EMRAdmin\Service\UniqueUsername\Dto\GetUniqueUsernameResponse;
use EMRAdmin\Service\UniqueUsername\Marshaller\SuccessToGetUniqueUsernameResponse;
use EMRCore\Config\Service\PrivateService\Esb\Dto\Route;
use EMRCore\Config\Service\PrivateService\Esb\Routes;
use EMRCore\EsbFactory;
use EMRCore\EsbFactoryAwareInterface;
use EMRCore\Zend\Http\ClientWrapper;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use EMRCore\Zend\module\Service\src\Response\Parser\Json;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use EMRCoreTest\lib\PhpUnit\SingletonTestCaseHelper;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Zend\Http\Response;

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
    
      /**
     * set up the esb
     */
    public function setUp()
    {
        $this->singletonTestCaseHelper = new SingletonTestCaseHelper($this);

        $esbFactoryClass = 'EMRCore\EsbFactory';
        $this->mockEsbFactory = $this->getMock($esbFactoryClass, array(), array(), '', false);

        $this->singletonTestCaseHelper->mockSingleton($this->mockEsbFactory, $esbFactoryClass);

        $this->mockServiceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

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
    public function testEsbCriteria()
    {
        $username = "asd";
        
        // Create a DAO and set some mocked dependencies.
        $dao = new Esb;
        
        // Create a mock EsbFactory (for mock requests) and set the dependency.
        $esbFactory = $this->getMock('EMRCore\EsbFactory', array(), array(), '', false);
        $dao->setEsbFactory($esbFactory);
        
        // Create a mock PrototypeFactory and set the dependency.
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);
        
        // Create a mock ServiceLocator and set the dependency.
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $dao->setServiceLocator($serviceLocator);
        
        // Create a fake route.
        $route = new Route;
        $route->setUri('asdf');
        $route->setMethod('qwer');
        
        // Always use the fake route for our request.
        $routes = $this->getMock('EMRCore\Config\Service\PrivateService\Esb\Routes');
        
        $routes->expects($this->once())
                ->method('getRouteByName')
                ->with($this->anything())
                ->will($this->returnValue($route));
        
         // Return some services from our mock ServiceLocator
        $serviceLocator->expects($this->any())->method('get')
            ->will($this->returnCallback(
                function($name) use ($routes, $prototypeFactory)
                {
                    switch ($name)
                    {
                        case 'EMRCore\Config\Service\PrivateService\Esb\Routes':
                            return $routes;
                            break;
                        case 'EMRCore\Zend\module\Service\src\Response\Parser\Json':
                            return new Json();
                            break;
                        case 'EMRAdmin\Service\UniqueUsername\Marshaller\SuccessToGetUniqueUsernameResponse';
                            $object = new SuccessToGetUniqueUsernameResponse();

                            $object->setPrototypeFactory($prototypeFactory);
                            return $object;
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
                        'username' => 'gogo',
                        'isUnique' => true,
                )));
                    
        $content = json_encode($arrConstructArray);
        
        // Return the mock response string.
        $response = new Response;
        $response->setStatusCode(200);
        $response->setContent($content);
             
        // Return the mock response.
        $client = $this->getMock('Zend\Http\Client');
        $client->expects($this->once())->method('send')
                ->will($this->returnValue($response));
        
        // Return the mock client.
        $clientWrapper = new ClientWrapper;
        $clientWrapper->setLogger($this->getMock('Logger', array(), array(), '', false));
        $clientWrapper->setClient($client);
        
        // Always produce the fake ClientWrapper in the mock EsbFactory.
        $esbFactory->expects($this->any())
            ->method('getClient')
            ->with($this->equalTo($route->getUri().'/'.$username), $this->equalTo($route->getMethod()), $this->anything())
            ->will($this->returnValue($clientWrapper));
        
        // Return some instances from our mock PrototypeFactory.
        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name)
                {
                    switch ($name)
                    {
                        case 'EMRAdmin\Service\UniqueUsername\Dto\GetUniqueUsernameRequest':
                            return new GetUniqueUsernameRequest();
                            break;
                        case 'EMRAdmin\Service\UniqueUsername\Dto\GetUniqueUsernameResponse':
                            return new GetUniqueUsernameResponse();
                            break;
                        default :
                            throw new InvalidArgumentException("Mock PrototypeFactory cannot provide [$name].");
                            break;
                    }
                }));
                
        // Create a fake request.
        $request = new GetUniqueUsernameRequest();
        $request->setUsername($username);
        
        // Run the service!
        /**
         * @var SearchGhostBrowseResponseCollection $collection
         */
        $collection = $dao->getUniqueUsername($request);
        
         // Proves that the collection returned is of GetUniqueUsernameResponse type
        $this->assertTrue($collection instanceof GetUniqueUsernameResponse);
        
        //proves that the first facility's data inside the construct array stays the same after marshalling
        $this->assertEquals($arrConstructArray['content']['response']['isUnique'], $collection->getIsUnique());
        $this->assertEquals($arrConstructArray['content']['response']['username'], $collection->getUsername());
    }
}
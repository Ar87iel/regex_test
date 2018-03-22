<?php

namespace EMRAdminTest\unit\tests\Service\UniqueEmail\Dao;

use EMRCore\EsbFactory;
use EMRCore\PrototypeFactory;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use EMRCoreTest\lib\PhpUnit\SingletonTestCaseHelper;
use EMRAdmin\Service\UniqueEmail\Dao\Esb;
use EMRCore\Config\Service\PrivateService\Esb\Dto\Route;
use EMRCore\Zend\module\Service\src\Response\Parser\Json;
use EMRAdmin\Service\UniqueEmail\Marshaller\SuccessToGetUniqueEmailResponse;
use EMRAdmin\Service\UniqueEmail\Dto\GetUniqueEmailRequest;
use EMRAdmin\Service\UniqueEmail\Dto\GetUniqueEmailResponse;
use Zend\Http\Response;
use EMRCore\Zend\Http\ClientWrapper;
use Zend\ServiceManager\ServiceLocatorInterface;

class EsbUniqueEmailTest extends PHPUnit_Framework_TestCase
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

        $this->dao = new Esb();
        $this->dao->setServiceLocator($this->mockServiceLocator);
    }

    public function tearDown()
    {
        $this->singletonTestCaseHelper->unmockSingletons();
    }

    public function testEsbCriteria()
    {
        $email = 'asdasdasd';

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

        // Create a mock PrototypeFactory and set the dependency.
        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        // Return some instances from our mock PrototypeFactory.
        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name) {
                    switch ($name)
                    {
                        case 'EMRAdmin\Service\UniqueEmail\Dto\GetUniqueEmailRequest':
                            return new GetUniqueEmailRequest();
                            break;
                        case 'EMRAdmin\Service\UniqueEmail\Dto\GetUniqueEmailResponse':
                            return new GetUniqueEmailResponse();
                            break;
                        default :
                            throw new InvalidArgumentException("Mock PrototypeFactory cannot provide [$name].");
                            break;
                    }
                }));

        // Return some services from our mock ServiceLocator
        $this->mockServiceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(
                function($name) use ($routes, $prototypeFactory) {
                    switch ($name)
                    {
                        case 'EMRCore\Config\Service\PrivateService\Esb\Routes':
                            return $routes;
                            break;
                        case 'EMRCore\Zend\module\Service\src\Response\Parser\Json':
                            return new Json();
                            break;
                        case 'EMRAdmin\Service\UniqueEmail\Marshaller\SuccessToGetUniqueEmailResponse':
                            $object = new SuccessToGetUniqueEmailResponse();

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
                    'email' => $email,
                    'isUnique' => true,
        )));

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
        $clientWrapper = new ClientWrapper();
        $clientWrapper->setLogger($this->getMock('Logger', array(), array(), '', false));
        $clientWrapper->setClient($client);
        
        // Always produce the fake ClientWrapper in the mock EsbFactory.
        $this->mockEsbFactory->expects($this->any())
            ->method('getClient')
            ->with($this->equalTo($route->getUri().'/'.$email), $this->equalTo($route->getMethod()), $this->anything())
            ->will($this->returnValue($clientWrapper));
        
        $this->dao->setEsbFactory($this->mockEsbFactory);
        
        $request = new GetUniqueEmailRequest();
        
        $request->setEmail($email);
        
        $collection = $this->dao->getUniqueEmail($request);
        
        $this->assertSame($email, $collection->getEmail(), 'Asserting that the returned email is the same as the one sent');
    }

}
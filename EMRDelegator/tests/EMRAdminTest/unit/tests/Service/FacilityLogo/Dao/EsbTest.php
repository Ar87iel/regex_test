<?php

namespace EMRAdminTest\unit\tests\Service\FacilityLogo\Dao;

use EMRAdmin\Service\FacilityLogo\Dao\Esb;
use EMRAdmin\Service\FacilityLogo\Dto\DeleteFacilityLogoRequest;
use EMRCore\Config\Service\PrivateService\Esb\Dto\Route;
use EMRCore\Config\Service\PrivateService\Esb\Routes;
use EMRCore\EsbFactory;
use EMRCore\Zend\Http\ClientWrapper;
use EMRCore\Zend\module\Service\src\Response\Parser\Json;
use InvalidArgumentException;
use Logger;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Zend\Http\Client;
use Zend\Http\Response;
use EMRAdmin\Service\FacilityLogo\Dto\SaveFacilityLogoRequest;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class EsbTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ServiceLocatorInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $serviceLocator;

    /**
     * @var EsbFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $esbFactory;

    /**
     *
     * @var Routes
     */
    private $routes;

    /**
     *
     * @var Route
     */
    private $route;

    /**
     * @var Esb
     */
    private $dao;

    public function setUp()
    {
        //Create a DAO
        $this->dao = new Esb();

        //Create a mock EsbFactory (for mock requests)
        $this->esbFactory = $this->getMock('EMRCore\EsbFactory', array(), array(), '', false);

        // Create a mock ServiceLocator
        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
    }

    public function testDeleteFacilityLogo()
    {

        //Set the mock EsbFactory
        $this->dao->setEsbFactory($this->esbFactory);

        //Set the mock ServiceLocator
        $this->dao->setServiceLocator($this->serviceLocator);

        //Create a mock PrototypeFactory and set the dependency.
        $SuccessMarshaller = $this->getMock('EMRAdmin\Service\FacilityLogo\Marshaller\SuccessToDeleteFacilityLogoResponse');
        $SuccessMarshaller->expects($this->once())->method('marshall')
                ->will($this->returnValue(array()));

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
        $this->serviceLocator->expects($this->any())->method('get')
                ->will($this->returnCallback(function($name) use ($routes, $SuccessMarshaller)
                                {
                                    switch ($name)
                                    {
                                        case 'EMRCore\Config\Service\PrivateService\Esb\Routes':
                                            return $routes;
                                            break;
                                        case 'EMRCore\Zend\module\Service\src\Response\Parser\Json':
                                            return new Json();
                                            break;
                                        case 'EMRAdmin\Service\FacilityLogo\Marshaller\SuccessToDeleteFacilityLogoResponse':
                                            return $SuccessMarshaller;
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
        $this->esbFactory->expects($this->any())
            ->method('getClient')
            ->with($this->equalTo($route->getUri() . '/2'), $this->equalTo($route->getMethod()), $this->anything())
            ->will($this->returnValue($clientWrapper));

        // Create a fake request.
        $request = new DeleteFacilityLogoRequest();
        $request->setId(2);

        $deleteResponse = $this->dao->deleteFacilityLogo($request);
        $this->assertTrue(is_array($deleteResponse));
    }

    public function testSaveFacilityLogo()
    {

        /** @var SaveFacilityLogoRequest $request */
        $request = new SaveFacilityLogoRequest();

        /**
         * Create a fake route.
         * @var Route $route
         */
        $route = new Route;
        $route->setUri('asdf');
        $route->setMethod('qwer');

        // Create a fake routes for the request.
        $routes = $this->getMock('EMRCore\Config\Service\PrivateService\Esb\Routes');

        $routes->expects($this->once())
                ->method('getRouteByName')
                ->with($this->anything())
                ->will($this->returnValue($route));

        // Create a mock for the service locator services.
        $this->serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(
                function($name) use ($routes)
                {
                    switch ($name)
                    {
                        case 'EMRCore\Config\Service\PrivateService\Esb\Routes':
                            return $routes;
                            break;
                        case 'EMRCore\Zend\module\Service\src\Response\Parser\Json':
                            return new Json();
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
                    'success' => true,
        )));

        $content = json_encode($arrConstructArray);

        // Return the mock response string.
        $response = new Response;
        $response->setStatusCode(200);
        $response->setContent($content);

        // Return the mock response.
        /** @var Client|PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMock('Zend\Http\Client');
        $client->expects($this->once())->method('send')
                ->will($this->returnValue($response));

        /** @var Logger|PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->getMock('Logger', array(), array(), '', false);

        // Return the mock client.
        $clientWrapper = new ClientWrapper;
        $clientWrapper->setLogger($logger);
        $clientWrapper->setClient($client);

        // Always produce the fake ClientWrapper in the mock EsbFactory.
        $this->esbFactory->expects($this->any())
            ->method('getClient')
            ->with($this->equalTo($route->getUri()), $this->equalTo($route->getMethod()), $this->anything())
            ->will($this->returnValue($clientWrapper));

        //Set the mock Service locator on our DAO
        $this->dao->setServiceLocator($this->serviceLocator);

        //Set the mock EsbFactory on our DAO
        $this->dao->setEsbFactory($this->esbFactory);

        //Call the ESB and get the response. The response will be a true/false.
        $saveResponse = $this->dao->saveFacilityLogo($request);

        //Assert if is boolean.
        $this->assertTrue(is_bool($saveResponse));
    }

}
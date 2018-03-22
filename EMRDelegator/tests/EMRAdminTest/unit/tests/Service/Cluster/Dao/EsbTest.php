<?php

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

namespace EMRAdminTest\unit\tests\Service\Cluster\Dao;

use EMRAdmin\Service\Cluster\Dao\Esb;
use EMRCore\Config\Service\PrivateService\Esb\Dto\Route;
use EMRCoreTest\lib\PhpUnit\SingletonTestCaseHelper;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\Cluster\Dto\Cluster;

class EsbTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    public $esbFactory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    public $serviceLocator;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    public $prototypeFactory;

    /**
     * @var Esb
     */
    private $dao;

    /**
     * @var SingletonTestCaseHelper
     */
    private $singletonTestCaseHelper;

    public function setUp()
    {
        $this->singletonTestCaseHelper = new SingletonTestCaseHelper($this);

        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $this->esbFactory = $this->getMock('EMRCore\EsbFactory', array('getClient'), array(), '', false);

        $this->singletonTestCaseHelper->mockSingleton($this->esbFactory, 'EMRCore\EsbFactory');

        $this->prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $this->dao = new Esb();
        $this->dao->setServiceLocator($this->serviceLocator);
        $this->dao->setPrototypeFactory($this->prototypeFactory);
        $this->dao->setEsbFactory($this->esbFactory);
    }

    public function testGetClusterById()
    {

        $id = 3;

        // A fake ESB route.
        $route = new Route();
        $route->setName('asdf');
        $route->setUri('qwer');
        $route->setMethod('zxcv');
        
        $esbRouteUri = $route->getUri() . '/' . $id;

        // A fake response.
        $esbResponse = json_encode(array(
            'content' => array(
                'response' => array(
                    'stuff' => 'things',
                ),
            ),
        ));
        
        // A fake parser.
        $parser = null;

        // Mock the routes config to return our fake route.
        $routesService = $this->getMock('EMRCore\Config\Service\PrivateService\Esb\Routes');
        $routesService->expects($this->once())
            ->method('getRouteByName')
            ->with($this->anything())
            ->will($this->returnValue($route));

        // Mock the dto marshaller so that this test does not blow up when marshall is called.
        $dtoMarshaller = $this->getMock('EMRAdmin\Service\Cluster\Marshaller\SuccessToGetClusterResponse');
        $dtoMarshaller->expects($this->once())
            ->method('marshall')
            ->with($this->anything())
            ->will($this->returnValue(null));
        
        // Stub service locator calls to return the mocks.
        $this->serviceLocator->expects($this->any())
            ->method('get')
            ->withAnyParameters()
            ->will($this->returnCallback(
                function($name) use ($routesService, $parser,$dtoMarshaller)
                {

                    if ($name == 'EMRCore\Config\Service\PrivateService\Esb\Routes')
                    {
                        return $routesService;
                    }
                    if ($name == 'EMRCore\Zend\module\Service\src\Response\Parser\Json')
                    {
                        return $parser;
                    }
                    if($name =='EMRAdmin\Service\Cluster\Marshaller\SuccessToGetClusterResponse'){
                        return $dtoMarshaller;
                    }

                    throw new InvalidArgumentException("Mock ServiceLocatorInterface could not create [$name]");
                }));

        // Mock the client wrapper and ensure that execute is called. This is how the ESB request is sent.
        $clientWrapper = $this->getMock('EMRCore\Zend\Http\ClientWrapper');
        $clientWrapper->expects($this->once())
            ->method('setResponseParser')
            ->with($this->equalTo($parser));
        $clientWrapper->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($esbResponse));

        // Ensure that the ESB factory returns the mock client wrapper when supplied with the route parameters.
        $this->esbFactory->expects($this->once())
            ->method('getClient')
            ->with($this->equalTo($esbRouteUri), $this->equalTo($route->getMethod()))
            ->will($this->returnValue($clientWrapper));

        $this->dao->getById($id);
    }

    public function testGetListBuildsClientWithRoute()
    {
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

        // Mock the routes config to return our fake route.
        $routesService = $this->getMock('EMRCore\Config\Service\PrivateService\Esb\Routes');
        $routesService->expects($this->once())
            ->method('getRouteByName')
            ->with($this->anything())
            ->will($this->returnValue($route));

        // Mock the dto marshaller so that this test does not blow up when marshall is called.
        $dtoMarshaller = $this->getMock('EMRAdmin\Service\Cluster\Marshaller\SuccessToGetListResponse');
        $dtoMarshaller->expects($this->once())
            ->method('marshall')
            ->with($this->anything())
            ->will($this->returnValue(null));

        // A fake parser.
        $parser = null;

        // Stub service locator calls to return the mocks.
        $this->serviceLocator->expects($this->any())->method('get')
            ->withAnyParameters()
            ->will($this->returnCallback(
                function($name) use ($routesService, $dtoMarshaller, $parser)
                {

                    if ($name === 'EMRCore\Config\Service\PrivateService\Esb\Routes')
                    {
                        return $routesService;
                    }

                    if ($name === 'EMRAdmin\Service\Cluster\Marshaller\SuccessToGetListResponse')
                    {
                        return $dtoMarshaller;
                    }

                    if ($name === 'EMRCore\Zend\module\Service\src\Response\Parser\Json')
                    {
                        return $parser;
                    }

                    throw new InvalidArgumentException("Mock ServiceLocatorInterface could not create [$name]");
                }));

        // Mock the client wrapper and ensure that execute is called. This is how the ESB request is sent.
        $clientWrapper = $this->getMock('EMRCore\Zend\Http\ClientWrapper');
        $clientWrapper->expects($this->once())
            ->method('setResponseParser')
            ->with($this->equalTo($parser));
        $clientWrapper->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($esbResponse));

        // Ensure that the ESB factory returns the mock client wrapper when supplied with the route parameters.
        $this->esbFactory->expects($this->once())
            ->method('getClient')
            ->with($this->equalTo($route->getUri()), $this->equalTo($route->getMethod()))
            ->will($this->returnValue($clientWrapper));

        $this->dao->getList();
    }

    public function testSaveClusterBuildsClientWithRoute()
    {

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

        $saveClusterRequest = new Cluster;

        // Mock the routes config to return our fake route.
        $routesService = $this->getMock('EMRCore\Config\Service\PrivateService\Esb\Routes');
        $routesService->expects($this->once())
            ->method('getRouteByName')
            ->with($this->anything())
            ->will($this->returnValue($route));

        // Mock the dto marshaller so that this test does not blow up when marshall is called.
        $dtoMarshaller = $this->getMock('EMRAdmin\Service\Cluster\Marshaller\SaveClusterRequestToArray');
        $dtoMarshaller->expects($this->once())
            ->method('marshall')
            ->with($this->anything())
            ->will($this->returnValue(array()));

        // Mock the dto marshaller so that this test does not blow up when marshall is called.
        $successResponse = $this->getMock('EMRAdmin\Service\Cluster\Marshaller\SuccessToSaveClusterResponse');
        $successResponse->expects($this->once())
            ->method('marshall')
            ->with($this->anything())
            ->will($this->returnValue(new Cluster));

        // A fake parser.
        $parser = null;

        // Stub service locator calls to return the mocks.
        $this->serviceLocator->expects($this->any())
            ->method('get')
            ->withAnyParameters()
            ->will($this->returnCallback(
                function($name) use ($routesService, $dtoMarshaller, $parser, $successResponse)
                {

                    if ($name === 'EMRCore\Config\Service\PrivateService\Esb\Routes')
                    {
                        return $routesService;
                    }

                    if ($name === 'EMRAdmin\Service\Cluster\Marshaller\SaveClusterRequestToArray')
                    {
                        return $dtoMarshaller;
                    }

                    if ($name === 'EMRCore\Zend\module\Service\src\Response\Parser\Json')
                    {
                        return $parser;
                    }
                    if ($name == 'EMRAdmin\Service\Cluster\Marshaller\SuccessToSaveClusterResponse')
                    {
                        return $successResponse;
                    }

                    throw new InvalidArgumentException("Mock ServiceLocatorInterface could not create [$name]");
                }));

        // Mock the client wrapper and ensure that execute is called. This is how the ESB request is sent.
        $clientWrapper = $this->getMock('EMRCore\Zend\Http\ClientWrapper');
        $clientWrapper->expects($this->once())
            ->method('setResponseParser')
            ->with($this->equalTo($parser));
        $clientWrapper->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($esbResponse));

        // Ensure that the ESB factory returns the mock client wrapper when supplied with the route parameters.
        $this->esbFactory->expects($this->once())
            ->method('getClient')
            ->with($this->equalTo($route->getUri()), $this->equalTo($route->getMethod()))
            ->will($this->returnValue($clientWrapper));

        $this->dao->saveCluster($saveClusterRequest);
    }

}
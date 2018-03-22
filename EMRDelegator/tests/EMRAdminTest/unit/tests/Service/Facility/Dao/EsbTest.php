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

namespace EMRAdminTest\unit\tests\Service\Facility\Dao;

use EMRAdmin\Service\Facility\Dao\Esb;
use EMRAdmin\Service\Facility\Dto\SaveFacilityRequest;
use EMRCore\Config\Service\PrivateService\Esb\Dto\Route;
use EMRCore\EsbFactory;
use EMRCoreTest\lib\PhpUnit\SingletonTestCaseHelper;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

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

    public function testSaveFacilityBuildsClientWithRoute()
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
        $saveFacilityRequest = new SaveFacilityRequest();

        // Mock the array marshaller to return ESB parameters when the request is received.
        $mockArrayMarshaller = $this->createMock('EMRAdmin\Service\Facility\Marshaller\SaveFacilityRequestToArray');
        $mockArrayMarshaller->expects($this->once())->method('marshall')
                ->with($this->equalTo($saveFacilityRequest))->will($this->returnValue($esbParameters));

        // Mock the routes config to return our fake route, always.
        $mockRoutes = $this->createMock('EMRCore\Config\Service\PrivateService\Esb\Routes');
        $mockRoutes->expects($this->once())->method('getRouteByName')
                ->with($this->anything())->will($this->returnValue($route));

        // Mock the dto marshaller so that this test does not blow up when marshall is called.
        $mockDtoMarshaller = $this->getMock('EMRAdmin\Service\Facility\Marshaller\SuccessToSaveFacilityResponse', array('marshall'));
        $mockDtoMarshaller->expects($this->once())->method('marshall')
                ->with($this->anything())->will($this->returnValue(null));

        // Stub service locator calls to return the mocks.
        $this->mockServiceLocator->expects($this->any())->method('get')
                ->withAnyParameters()
                ->will($this->returnCallback(function($name) use ($mockArrayMarshaller, $mockRoutes, $mockDtoMarshaller)
                                {

                                    if ($name === 'EMRAdmin\Service\Facility\Marshaller\SaveFacilityRequestToArray')
                                    {
                                        return $mockArrayMarshaller;
                                    }

                                    if ($name === 'EMRCore\Config\Service\PrivateService\Esb\Routes')
                                    {
                                        return $mockRoutes;
                                    }

                                    if ($name === 'EMRAdmin\Service\Facility\Marshaller\SuccessToSaveFacilityResponse')
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

        $this->dao->saveFacility($saveFacilityRequest);
    }

    public function testGetFacilityByIdBuildsClientWithRoute()
    {
        /** @var int $facilityId */
        $facilityId = 1;

        // A fake parameter list.
        $esbParameters = array();

        // A fake ESB route.
        $route = new Route();
        $route->setName('asdf');
        $route->setUri('qwer');
        $route->setMethod('zxcv');

        $esbRouteUri = $route->getUri() . '/' . $facilityId;

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
        $mockDtoMarshaller = $this->createMock('EMRAdmin\Service\Facility\Marshaller\SuccessToGetFacilityByIdResponse');
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

                                    if ($name === 'EMRAdmin\Service\Facility\Marshaller\SuccessToGetFacilityByIdResponse')
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

        $this->dao->getFacilityById($facilityId);
    }

}
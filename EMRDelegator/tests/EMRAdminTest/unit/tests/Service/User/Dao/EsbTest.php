<?php
namespace EMRAdminTest\unit\tests\Service\User\Dao;

use EMRAdmin\Service\User\Dao\Esb;
use EMRAdmin\Service\User\Dto\UnlockLogin;
use EMRAdmin\Service\User\Marshaller\SuccessGetUserFacilitiesToArray;
use EMRAdmin\Service\User\Marshaller\SuccessToGetUserByIdFromAuthResponse;
use EMRAdmin\Service\User\Marshaller\UnlockLoginToArray;
use EMRCore\Config\Service\PrivateService\Esb\Dto\Route;
use EMRCore\Service\Auth\Application\Dto\Application;
use EMRCore\Zend\Http\ClientWrapper;
use EMRCore\Zend\module\Service\src\Response\Parser\Json;
use EMRCoreTest\Helper\Reflection;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Zend\Http\Response;
use EMRAdmin\Service\User\Dto\User;
use EMRCore\Contact\Email\Dto\Email;
use EMRCoreTest\lib\PhpUnit\SingletonTestCaseHelper;
use EMRAdmin\Service\User\Marshaller\SuccessGetUserToArray;
use EMRAdmin\Service\User\Marshaller\ArrayToSaveUserResponse;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use EMRUser\Service\User\Dto\UsersCountRequest;

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
     * @var SingletonTestCaseHelper
     */
    private $singletonTestCaseHelper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $mockEsbFactory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $serviceLocator;

    /**
     * A fake route
     * @var Route $route
     */
    private $route;

    /**
     *
     * @var Esb $userDao 
     */
    private $userDao;

    public function setUp()
    {
        $this->singletonTestCaseHelper = new SingletonTestCaseHelper($this);

        $esbFactoryClass = 'EMRCore\EsbFactory';
        $this->mockEsbFactory = $this->getMock($esbFactoryClass, array('getClient'), array(), '', false);

        $this->singletonTestCaseHelper->mockSingleton($this->mockEsbFactory, $esbFactoryClass);

        // Create a service locator mock to produce instances of our parser and marshaller.
        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        // Create a route to use instead of bootstrapping the application config.
        $this->route = new Route();
        $this->route->setName('asdf');
        $this->route->setUri('qwer');
        $this->route->setMethod('zxcv');

        //Create a UserDao
        $this->userDao = new Esb();
    }

    /**
     * Parses the response from a mocked ESB Request. This actually executes the parser
     * and marshaller so there should be no surprises regarding the Success payload data type.
     */
    public function testUnlocksLogin()
    {
        $unlockLogin = new UnlockLogin;

        // Create a value to be returned by a mock Client.
        $routeReturnValue = json_encode(array(
            'content' => array(
                'response' => array(
                    'success' => true,
                ),
            ),
        ));

        // Create a response container for the content value.
        $response = new Response;
        $response->setStatusCode(200);
        $response->setContent($routeReturnValue);

        // Create the DAO. It is ok that it is a mock for this test.
        $dao = $this->getMock('EMRAdmin\Service\User\Dao\Esb', array(
            'getRoute',
        ));

        // Always return the custom route.
        $dao->expects($this->any())->method('getRoute')->withAnyParameters()->will($this->returnValue($this->route));

        // Create a mock Client. This will be executed by our client wrapper.
        $client = $this->getMock('Zend\Http\Client');
        $client->expects($this->once())->method('send')->will($this->returnValue($response));

        // Create a ClientWrapper to execute our mock Client.
        $clientWrapper = new ClientWrapper;
        $clientWrapper->setClient($client);
        $clientWrapper->setLogger($this->getMock('Logger', array(), array(), '', false));

        // Create a mock EsbFactory to return our ClientWrapper.
        $this->mockEsbFactory->expects($this->once())->method('getClient')
            ->with($this->equalTo($this->route->getUri()), $this->equalTo($this->route->getMethod()), $this->anything())
            ->will($this->returnValue($clientWrapper));

        $this->serviceLocator->expects($this->any())->method('get')
            ->will($this->returnValueMap(array(
                array('EMRCore\Zend\module\Service\src\Response\Parser\Json', new Json),
                array('EMRAdmin\Service\User\Marshaller\UnlockLoginToArray', new UnlockLoginToArray),
            )));

        /**
         * Set dependencies into the DAO.
         * @var Esb $dao
         */
        $dao->setServiceLocator($this->serviceLocator);
        $dao->setEsbFactory($this->mockEsbFactory);

        // Get the response.
        $response = $dao->unlockLogin($unlockLogin);

        $payload = $response->getPayload();

        $this->assertInstanceOf('stdClass', $payload);
        $this->assertTrue(property_exists($payload, 'success'));
        $this->assertTrue($response->getPayload()->success);
    }

    public function testGetUserByIdFromAuth()
    {
        // User data
        $userId = 1;
        $user = new User;
        $user->setId($userId);

        // Build ESB response.
        $response = json_encode(
            array(
                'content' => array(
                    'response' => array(
                        'stuff' => 'things'
                    ),
                )
            )
        );

        // Set up some routes. This function can use up to three! : (
        $route = new Route;
        $route->setMethod('asdf');
        $route->setUri('qwer');

        $dao = $this->getMock('EMRAdmin\Service\User\Dao\Esb', array('getRoute'));
        $dao->expects($this->once())->method('getRoute')->with($this->equalTo(ESB::ROUTE_AUTH_GET_USER))->will($this->returnValue($route));

        $mockMarshaller = $this->getMock('EMRAdmin\Service\User\Marshaller\SuccessToGetUserByIdFromAuthResponse');
        $mockMarshaller->expects($this->once())->method('marshall')->with($this->equalTo($response))->will($this->returnValue($user));

        // Return some services.
        $this->serviceLocator->expects($this->any())->method('get')->withAnyParameters()->will(
                $this->returnCallback(
                    function ($name) use ($mockMarshaller) {

                        if ($name === 'EMRCore\Zend\module\Service\src\Response\Parser\Json') {
                            return new Json;
                        }

                        if ($name === 'EMRAdmin\Service\User\Marshaller\SuccessToGetUserByIdFromAuthResponse') {
                            return $mockMarshaller;
                        }

                        throw new InvalidArgumentException("Mock ServiceLocatorInterface could not create [$name]");
                    }
                )
            );

        // Mock the client.
        $client = $this->getMock('EMRCore\Zend\Http\ClientWrapper');
        $client->expects($this->once())->method('execute')->will($this->returnValue($response));

        $this->mockEsbFactory->expects($this->once())->method('getClient')
            ->with($route->getUri() . '/' . $userId, $route->getMethod(), array())
            ->will($this->returnValue($client));

        $dao->setServiceLocator($this->serviceLocator);
        $dao->setEsbFactory($this->mockEsbFactory);

        // Get the response.
        /** @var User $result */
        $result = Reflection::invoke($dao, 'getUserByIdFromAuth', array($userId));

        $this->assertInstanceOf('\EMRAdmin\Service\User\Dto\User', $result);
    }

    public function testGetUserByIdFromDelegator()
    {
        $userId = 1;
        $routeName = 'getUser';
        $response = 'sldksld';
        $expectedResponse = array(
            'key' => 'value',
        );
        $jsonObject = new Json;

        /** @var Esb|PHPUnit_Framework_TestCase $esbMock */
        $esbMock = $this->getMock('EMRAdmin\Service\User\Dao\Esb', array(
            'getRoute'
        ), array(), '', false);

        $clientWrapperMock = $this->getMock('EMRCore\Zend\Http\ClientWrapper');

        $marshallMock = $this->getMock('EMRAdmin\Service\User\Marshaller\SuccessGetUserToArray', array(
            'marshall',
        ), array(), '', false);

        $route = new Route();
        $route->setName($routeName);
        $route->setUri('asdf');
        $route->setMethod('qwer');

        $esbMock->expects($this->once())->method('getRoute')
            ->with($this->equalTo(Esb::ROUTE_GET_USER))
            ->will($this->returnValue($route));

        $this->serviceLocator->expects($this->any())->method('get')
            ->will($this->returnValueMap(array(
                array('EMRCore\Zend\module\Service\src\Response\Parser\Json', $jsonObject),
                array('EMRAdmin\Service\User\Marshaller\SuccessGetUserToArray', $marshallMock),
            )));

        $clientWrapperMock->expects($this->once())->method('execute')
            ->will($this->returnValue($response));

        $marshallMock->expects($this->once())->method('marshall')
            ->with($this->equalTo($response))
            ->will($this->returnValue($expectedResponse));

        $this->mockEsbFactory->expects($this->once())->method('getClient')
            ->with($route->getUri() . '/' . $userId, $route->getMethod(), array())
            ->will($this->returnValue($clientWrapperMock));

        $esbMock->setServiceLocator($this->serviceLocator);
        $esbMock->setEsbFactory($this->mockEsbFactory);

        // Test method
        $result = Reflection::invoke($esbMock, 'getUserByIdFromDelegator', array($userId));

        $this->assertInternalType('array', $result);
    }

    public function testGetUserFacilities()
    {
        $result = array(
            'id' => 11,
            'name' => 'name',
            'companyName' => 'companyName',
        );

        // Build ESB response.
        $response = json_encode(
            array(
                'content' => array(
                    'response' => array(
                        'facilities' => array(
                            'id' => '11'
                        )
                    )
                )
            )
        );

        // Set up some routes. This function can use up to three! : (
        $route = new Route;
        $route->setMethod('asdf');
        $route->setUri('qwer');

        $dao = $this->getMock('EMRAdmin\Service\User\Dao\Esb', array('getRoute'));
        $dao->expects($this->once())->method('getRoute')->with($this->equalTo(ESB::ROUTE_GET_USER_FACILITIES))->will($this->returnValue($route));

        $mockMarshaller = $this->getMock('EMRAdmin\Service\User\Marshaller\SuccessGetUserFacilitiesToArray');
        $mockMarshaller->expects($this->once())->method('marshall')->with($this->equalTo($response))->will($this->returnValue($result));

        // Return some services.
        $this->serviceLocator->expects($this->any())->method('get')->withAnyParameters()->will(
            $this->returnCallback(
                function ($name) use ($mockMarshaller) {

                    if ($name === 'EMRCore\Zend\module\Service\src\Response\Parser\Json') {
                        return new Json;
                    }

                    if ($name === 'EMRAdmin\Service\User\Marshaller\SuccessGetUserFacilitiesToArray') {
                        return $mockMarshaller;
                    }

                    throw new InvalidArgumentException("Mock ServiceLocatorInterface could not create [$name]");
                }
            )
        );

        // Mock the client.
        $client = $this->getMock('EMRCore\Zend\Http\ClientWrapper');
        $client->expects($this->once())->method('execute')->will($this->returnValue($response));

        $this->mockEsbFactory->expects($this->once())->method('getClient')
            ->with($route->getUri() . '?identityId=' . $userId = 2, $route->getMethod(), array())
            ->will($this->returnValue($client));

        $dao->setServiceLocator($this->serviceLocator);
        $dao->setEsbFactory($this->mockEsbFactory);

        // Get the response.
        $result = $dao->getUserFacilities($userId);

        $this->assertInternalType('array', $result);
    }

    public function testGetSuperUserById()
    {
        $dao = $this->getMock('\EMRAdmin\Service\User\Dao\Esb', array(
            'getUserByIdFromAuth',
        ));

        $application = new Application;
        $application->setApplicationId(Application::ADMIN);

        $user = new User;
        $user->setId(13);
        $user->addApplication($application);

        $dao->expects($this->once())->method('getUserByIdFromAuth')
            ->with($user->getId())->will($this->returnValue($user));

        $dao->expects($this->never())->method('getUserByIdFromDelegator');

        /** @var Esb $dao */
        $result = $dao->getUserById($user->getId());

        $this->assertSame($user, $result);
        $this->assertSame(array($application), $result->getApplications());
    }

    public function testGetDocPortalUserById()
    {
        $dao = $this->getMock('\EMRAdmin\Service\User\Dao\Esb', array(
            'getUserByIdFromAuth',
        ));

        $application = new Application;
        $application->setApplicationId(Application::DOCUMENT_PORTAL);

        $user = new User;
        $user->setId(12);
        $user->addApplication($application);

        $dao->expects($this->once())->method('getUserByIdFromAuth')
            ->with($user->getId())->will($this->returnValue($user));

        $dao->expects($this->never())->method('getUserByIdFromDelegator');

        /** @var Esb $dao */
        $result = $dao->getUserById($user->getId());

        $this->assertSame($user, $result);
        $this->assertSame(array($application), $result->getApplications());
    }

    public function testGetClusterUserById()
    {
        $dao = $this->getMock('\EMRAdmin\Service\User\Dao\Esb', array(
            'getUserByIdFromAuth',
            'getUserByIdFromDelegator',
            'getUserFacilities',
        ));

        $emrApp = new Application;
        $emrApp->setApplicationId(Application::PT_EMR);

        $docPortalApp = new Application;
        $docPortalApp->setApplicationId(Application::DOCUMENT_PORTAL);

        $user = new User;
        $user->setId(23);
        $user->addApplication($emrApp);
        $user->addApplication($docPortalApp);

        $dao->expects($this->once())->method('getUserByIdFromAuth')
            ->with($user->getId())->will($this->returnValue($user));

        $dao->expects($this->once())->method('getUserByIdFromDelegator')
            ->with($user->getId())->will($this->returnValue(array(
                'id' => $user->getId(),
                'userName' => $username = 'asdf',
                'userType' => $userType = 'qwer',
                'license' => $license = 'zxcv',
                'nationalProviderId' => $npi = 'vcxz',
                'fullName' => $fullName = 'fdsa',
                'firstName' => $firstName = 'rewq',
                'lastName' => $lastName = 'wert',
                'middleName' => $middleName = 'sdfg',
                'credentials' => $credentials = 'xcvb',
                'alternateId' => $alternateId = 'trew',
                'status' => $status = 'gfds',
                'permissions' => $permissions = 'bvcx',
                'createCalendar' => $createCalendar = 'erty',
                'email' => $email = new Email,
            )));

        $dao->expects($this->once())->method('getUserFacilities')
            ->with($user->getId())->will($this->returnValue(array(
                'facilities' => array(
                    array('id' => $facilityId = 1234, 'name' => $facilityName = 'sdfg', 'companyName' => $companyName = 'ytre'),
                ),
                'clusters' => array($clusterId = 43321),
                'defaultClinic' => $defaultFacilityId = $facilityId,
            )));

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->any())->method('get')
            ->will($this->returnValueMap(array(
                array('EMRAdmin\Service\User\Marshaller\ArrayToSaveUserResponse', new ArrayToSaveUserResponse),
            )));

        /** @var Esb $dao */
        $dao->setServiceLocator($serviceLocator);

        /** @var User $result */
        $result = $dao->getUserById($user->getId());

        $this->assertInstanceOf('\EMRAdmin\Service\User\Dto\User', $result);
        $this->assertSame($user->getId(), $result->getId());
        $this->assertSame($userType, $result->getUserType());
        $this->assertSame($license, $result->getLicense());
        $this->assertSame($username, $result->getUserName());
        $this->assertSame($npi, $result->getNationalProviderId());
        $this->assertSame($fullName, $result->getFullName());
        $this->assertSame($firstName, $result->getFirstName());
        $this->assertSame($lastName, $result->getLastName());
        $this->assertSame($middleName, $result->getMiddleName());
        $this->assertSame($credentials, $result->getCredentials());
        $this->assertSame($alternateId, $result->getAlternateId());
        $this->assertSame($status, $result->getStatus());
        $this->assertSame($permissions, $result->getPermissions());
        $this->assertSame($createCalendar, $result->getCreateCalendar());
        $this->assertSame($email->getEmail(), $result->getEmail()->getEmail());
        $this->assertSame($defaultFacilityId, $result->getDefaultClinic());
        $this->assertSame(array($clusterId), $result->getClusters());
        $this->assertSame(array($facilityId), $result->getFacilities());
        $this->assertSame(array($emrApp, $docPortalApp), $result->getApplications());
    }

    /**
     * 
     * Test saveUser method of the DAO follows the correct workflow
     */
    public function testSaveUserDefault()
    {
        $data = array(
            'userName' => 'sdfg',
            'password' => 'asd',
            'confirmPassword' => 'wssf',
            'userType' => 'asd',
            'license' => 'wdsds',
            'nationalProviderId' => 'asd',
            'fullName' => 'wssf',
            'firstName' => 'asd',
            'lastName' => '1234567',
            'email' => 'wssf',
        );

        // A fake response.
        $esbResponse = json_encode(array(
            'content' => array(
                'response' => array(
                    'stuff' => 'things',
                ),
            ),
        ));

        $userDto = new User();

        $marshallUserToArray = $this->getMock('EMRAdmin\Service\User\Marshaller\SaveUserRequestToArray', array('marshall'));
        $marshallUserToArray->expects($this->once())->method('marshall')->with($userDto)->will($this->returnValue($data));

        $marshallArrayToUser = $this->getMock('EMRAdmin\Service\User\Marshaller\SuccessToSaveUserResponse', array('marshall'));
        $marshallArrayToUser->expects($this->once())->method('marshall')->with($this->anything())->will($this->returnValue(null));

        // Mock the routes config to return our fake route, always.
        $mockRoutes = $this->getMock('EMRCore\Config\Service\PrivateService\Esb\Routes');
        $mockRoutes->expects($this->once())->method('getRouteByName')
                ->with($this->anything())->will($this->returnValue($this->route));

        $this->serviceLocator->expects($this->any())->method('get')
            ->will($this->returnCallback(function($name) use ($marshallUserToArray, $mockRoutes, $marshallArrayToUser)
                {

                    if ($name == 'EMRAdmin\Service\User\Marshaller\SaveUserRequestToArray')
                    {
                        return $marshallUserToArray;
                    }
                    if ($name === 'EMRCore\Config\Service\PrivateService\Esb\Routes')
                    {
                        return $mockRoutes;
                    }
                    if ($name == 'EMRAdmin\Service\User\Marshaller\SuccessToSaveUserResponse')
                    {
                        return $marshallArrayToUser;
                    }
                    if ($name === 'EMRCore\Zend\module\Service\src\Response\Parser\Json')
                    {
                        return null;
                    } else
                    {
                        throw new InvalidArgumentException("Mocked ServiceManager cannot create name [$name].");
                    }
                }));

        // Mock the client wrapper and ensure that execute is called. This is how the ESB request is sent.
        $mockClientWrapper = $this->getMock('EMRCore\Zend\Http\ClientWrapper');
        $mockClientWrapper->expects($this->once())->method('execute')->will($this->returnValue($esbResponse));

        // Ensure that the ESB factory returns the mock client wrapper when supplied with the route parameters.
        $this->mockEsbFactory->expects($this->once())->method('getClient')
                ->with($this->equalTo($this->route->getUri()), $this->equalTo($this->route->getMethod()), $this->equalTo($data))
                ->will($this->returnValue($mockClientWrapper));

        $this->userDao->setServiceLocator($this->serviceLocator);
        $this->userDao->setEsbFactory($this->mockEsbFactory);

        $this->userDao->saveUser($userDto);
    }

    /**
     * Test generatePassword method of the DAO.
     * 
     * @throws InvalidArgumentException
     */
    public function testGeneratePassword()
    {

        $userDto = new User();

        $data = array(
            'emailAddress' => 'asd',
            'identityId' => 1
        );

        // A fake response.
        $esbResponse = (object) array(
                    'success' => true,
        );

        $response = new Success();
        $response->setPayload($esbResponse);

        $email = new Email();
        $email->setEmail('asd');

        $userDto->setEmail($email);
        $userDto->setId(1);

        // Mock the routes config to return our fake route, always.
        $mockRoutes = $this->getMock('EMRCore\Config\Service\PrivateService\Esb\Routes');
        $mockRoutes->expects($this->once())->method('getRouteByName')
                ->with($this->anything())->will($this->returnValue($this->route));

        // Mock the client wrapper and ensure that execute is called. This is how the ESB request is sent.
        $mockClientWrapper = $this->getMock('EMRCore\Zend\Http\ClientWrapper');
        $mockClientWrapper->expects($this->once())->method('execute')->will($this->returnValue($response));

        // Ensure that the ESB factory returns the mock client wrapper when supplied with the route parameters.
        $this->mockEsbFactory->expects($this->once())->method('getClient')
                ->with($this->equalTo($this->route->getUri()), $this->equalTo($this->route->getMethod()), $this->equalTo($data))
                ->will($this->returnValue($mockClientWrapper));

        $this->serviceLocator->expects($this->any())->method('get')
            ->will($this->returnCallback(function($name) use($mockRoutes)
                {
                    if ($name == 'EMRCore\Config\Service\PrivateService\Esb\Routes')
                    {
                        return $mockRoutes;
                    }
                    if ($name == 'EMRCore\Zend\module\Service\src\Response\Parser\Json')
                    {
                        return new Json;
                    } else
                    {
                        throw new InvalidArgumentException("Mocked ServiceManager cannot create name [$name].");
                    }
                }));

        $this->userDao->setServiceLocator($this->serviceLocator);
        $this->userDao->setEsbFactory($this->mockEsbFactory);

        $this->userDao->generatePassword($userDto);
    }

    public function testGetScrubUserById()
    {
        $dao = $this->getMock('\EMRAdmin\Service\User\Dao\Esb', array(
            'getUserByIdFromAuth',
        ));

        $application = new Application;
        $application->setApplicationId(Application::DOCUMENT_PORTAL);

        $user = new User;
        $user->setId(12);
        $user->addApplication($application);

        $dao->expects($this->once())->method('getUserByIdFromAuth')
            ->with($user->getId())->will($this->returnValue($user));

        $dao->expects($this->never())->method('getUserByIdFromDelegator');

        /** @var Esb $dao */
        $result = $dao->getScrubUserById($user->getId());

        $this->assertSame($user, $result);
        $this->assertSame(array($application), $result->getApplications());
    }

}

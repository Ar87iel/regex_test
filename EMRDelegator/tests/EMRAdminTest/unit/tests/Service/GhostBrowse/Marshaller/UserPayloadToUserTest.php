<?php

namespace EMRAdminTest\unit\tests\Service\GhostBrowse\Marshaller;

use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseUser;
use EMRAdmin\Service\GhostBrowse\Marshaller\Search\UserPayloadToUser;
use EMRCore\PrototypeFactory;
use PHPUnit_Framework_MockObject_MockObject;
use \stdClass;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserPayloadToUserTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var UserPayloadToUser
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new UserPayloadToUser();
    }

    /**
     * Test that the marshall will throw an exception when invoked with an unexpected data type parameter.
     * 
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshallsDueToInvalidItemType()
    {
        $this->marshaller->marshall(array());
    }

    /**
     * Test that the user payload provided is marshalled into a SearchGhostBrowseResponseUser object
     */
    public function testMarshallUserPayloadToUser()
    {

        /*
         * set up initial values for the user payload
         */
        $userId = 1;
        $userName = 'userName';
        $firstName = 'John';
        $lastName = 'Doe';
        $userType = 'PT';
        $companyAdmin = true;
        $facilityAdmin = false;
        $status = 'A';

        /*
         * create user payload
         */
        $userObject = new stdClass();
        $userObject->userId = $userId;
        $userObject->userName = $userName;
        $userObject->firstName = $firstName;
        $userObject->lastName = $lastName;
        $userObject->userType = $userType;
        $userObject->companyAdmin = $companyAdmin;
        $userObject->facilityAdmin = $facilityAdmin;
        $userObject->status = $status;

        /*
         * setup prototype factory mock
         */
        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
                ->will($this->returnCallback(
                function($name)
                {
                    switch ($name)
                    {
                        case 'EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseUser':
                            return new SearchGhostBrowseResponseUser();
                            break;
                        default:
                            throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                            break;
                    }
                }));

        /*
         * setup service locator mock
         */
        /** @var ServiceLocatorInterface|PHPUnit_Framework_MockObject_MockObject $serviceLocator */
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $serviceLocator->expects($this->any())->method('get')
                ->will($this->returnCallback(function($name) 
                {
                    switch ($name)
                    {
                        default:
                            throw new InvalidArgumentException("Mocked ServiceLocatorInterface cannot provide [$name].");
                            break;
                    }
                }));

        /*
         * add the prototype factory and service locator to the marshaller
         */                        
        $this->marshaller->setPrototypeFactory($prototypeFactory);
        $this->marshaller->setServiceLocator($serviceLocator);

        /*
         * invoke the marshaller with the created user payload object
         */
        $response = $this->marshaller->marshall($userObject);

        /*
         * assert that the initial user values remain the same after marshalling
         */
        $this->assertSame($userId, $response->getUserId(), 'Asserting that the passed userId is the same after marshalling');
        $this->assertSame($userName, $response->getUserName(), 'Asserting that the passed user name is the same after marshalling');
        $this->assertSame($firstName, $response->getFirstName(), 'Asserting that the passed first name is the same after marshalling');
        $this->assertSame($lastName, $response->getLastName(), 'Asserting that the passed last name is the same after marshalling');
        $this->assertSame($userType, $response->getUserType(), 'Asserting that the passed user type is the same after marshalling');
        $this->assertSame($companyAdmin, $response->getCompanyAdmin(), 'Asserting that the passed company admin is the same after marshalling');
        $this->assertSame($facilityAdmin, $response->getFacilityAdmin(), 'Asserting that the passed facility admin is the same after marshalling');
        $this->assertSame($status, $response->getStatus(), 'Asserting that the passed status is the same after marshalling');
    }

}
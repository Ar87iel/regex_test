<?php


namespace EMRAdminTest\unit\tests\Service\UniqueUsername\Marshaller;

use EMRAdmin\Service\UniqueUsername\Marshaller\SuccessToGetUniqueUsernameResponse;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use EMRAdmin\Service\UniqueUsername\Dto\GetUniqueUsernameRequest;
use EMRAdmin\Service\UniqueUsername\Dto\GetUniqueUsernameResponse;
use \stdClass;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class SuccessToGetUniqueUsernameResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SuccessToGetUniqueUsernameResponse
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new SuccessToGetUniqueUsernameResponse();
    }
    
    /**
     * Test that the marshaller will throw an exception when invoked with an unexpected data type parameter
     * 
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshallsDueToInvalidItemType()
    {
        $this->marshaller->marshall(array());
    }
    
    /**
     * Test that a success response is marshalled correctly to a ghost browse response object.
     */
    public function testMarshallSuccessToUniqueUsernameResponse()
    {
         /*
         * setup initial facility values
         */
            $username = "username";
            $isUnique = true;
            
         /*
         * create the success object to be marshalled
         */
        $success = new Success();
        
         /*
         * Create the payload
         */
        $payload = new stdClass();
        
        $payload->username = $username;
        $payload->isUnique = $isUnique;
        
         /*
         * add payload to the success object
         */
        $success->setPayload($payload);
        
        /*
         * create the search ghost response object to be returned by the mock
         */
        $searchUniqueUsernameResponse = new GetUniqueUsernameResponse();
        
         /*
         * setup the prototype factory mock
         */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);
        
        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name) use ($searchUniqueUsernameResponse)
                {
                    switch ($name)
                    {
                        case 'EMRAdmin\Service\UniqueUsername\Dto\GetUniqueUsernameResponse':
                            return $searchUniqueUsernameResponse;
                            break;
                        default:
                            throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                            break;
                    }
                }));
                
                
         /*
         * add the prototype factory and service locator to the marshaller
         */                        
        $this->marshaller->setPrototypeFactory($prototypeFactory);
        
        /*
         * invoke the marshaller
         */
        $response = $this->marshaller->marshall($success);
        
         /*
         * Get the elements contained within the response
         */
        //$elements = $response->getElements();
        
         /*
         * assert that the facility id and facility name are the same after marshalling
         */
        $this->assertSame($isUnique, $response->getIsUnique(), 'Asserting that the UniqueUsername id passed is the same as the one marshalled');

        $this->assertSame($username, $response->getUsername(), 'Asserting that the UniqueUsername name passed is the same as the one marshalled');

        
    }
}
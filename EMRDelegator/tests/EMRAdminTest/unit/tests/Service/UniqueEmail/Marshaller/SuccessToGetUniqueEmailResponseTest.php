<?php

namespace EMRAdminTest\unit\tests\Service\UniqueEmail\Marshaller;

use EMRCore\PrototypeFactory;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use InvalidArgumentException;
use EMRAdmin\Service\UniqueEmail\Marshaller\SuccessToGetUniqueEmailResponse;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use \stdClass;
use EMRAdmin\Service\UniqueEmail\Dto\GetUniqueEmailResponse;
use EMRAdmin\Service\UniqueEmail\Dto\GetUniqueEmailRequest;

class SuccessToGetUniqueEmailResponseTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var SuccessToGetUniqueEmailResponse 
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new SuccessToGetUniqueEmailResponse();
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
    public function testMarshallSuccessToGetUniqueEmailResponse()
    {
        /*
         * setup initial facility values
         */
        $email = "mail@mail.tst";
        $isUnique = true;

        /*
         * create the success object to be marshalled
         */
        $success = new Success();

        /*
         * Create the payload
         */
        $payload = new stdClass();

        $payload->email = $email;
        $payload->isUnique = $isUnique;

        /*
         * add payload to the success object
         */
        $success->setPayload($payload);

        $searchUniqueEmailResponse = new GetUniqueEmailResponse();

        /*
         * setup the prototype factory mock
         */
        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name) use ($searchUniqueEmailResponse)
                {
                    switch ($name)
                    {
                        case 'EMRAdmin\Service\UniqueEmail\Dto\GetUniqueEmailResponse':
                            return $searchUniqueEmailResponse;
                            break;
                        default:
                            throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                            break;
                    }
                }));

        $this->marshaller->setPrototypeFactory($prototypeFactory);

        /*
         * invoke the marshaller
         */
        $response = $this->marshaller->marshall($success);
        
        $this->assertSame($isUnique, $response->getIsUnique(), 'Asserting that the Unique Email flag passed is the same as the one marshalled');
        $this->assertSame($email, $response->getEmail(), 'Asserting that the email passed is the same as the one marshalled');
    }

}


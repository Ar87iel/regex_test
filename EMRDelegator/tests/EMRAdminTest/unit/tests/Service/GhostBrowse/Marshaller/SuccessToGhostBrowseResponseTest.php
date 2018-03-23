<?php

namespace EMRAdminTest\unit\tests\Service\GhostBrowse\Marshaller;

use EMRAdmin\Service\GhostBrowse\Marshaller\Search\SuccessToGhostBrowseResponse;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use EMRAdmin\Service\GhostBrowse\Marshaller\StdClassToSearchGhostBrowse;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseCollection;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponse;
use \stdClass;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class SuccessToGhostBrowseResponseTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SuccessToGhostBrowseResponse
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new SuccessToGhostBrowseResponse();
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
    public function testMarshallSuccessToGhostBrowseResponse()
    {

        /*
         * setup initial facility values
         */
        $facilityId = 1;
        $facilityName = 'My facility';

        /*
         * create the success object to be marshalled
         */
        $success = new Success();

        /*
         * Create the payload
         */
        $payload = new stdClass();

        $payload->facilities = array();
        $payload->facilities[0] = new stdClass();
        $payload->facilities[0]->id = $facilityId;
        $payload->facilities[0]->name = $facilityName;
        $payload->facilities[0]->users = array();

        /*
         * add payload to the success object
         */
        $success->setPayload($payload);

        /*
         * create the search ghost response object to be returned by the mock
         */
        $searchGhostBrowseResponse = new SearchGhostBrowseResponse();
        $searchGhostBrowseResponse->setId($facilityId);
        $searchGhostBrowseResponse->setName($facilityName);
        
        /*
         * Mock and setup the marshaller
         */
        $mockStdClassToSearchGhostBrowse = $this->getMock('EMRAdmin\Service\GhostBrowse\Marshaller\StdClassToSearchGhostBrowse', array(
                ), array(), '', false);

        $mockStdClassToSearchGhostBrowse->expects($this->any())
                ->method('marshall')
                ->with($payload->facilities[0])
                ->will($this->returnValue($searchGhostBrowseResponse));

        /*
         * setup the prototype factory mock
         */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

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
                        default:
                            throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                            break;
                    }
                }));

        /*
         * setup the service locator mock
         */
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(
                function($name) use ($mockStdClassToSearchGhostBrowse)
                {
                    switch ($name)
                    {
                        case 'EMRAdmin\Service\GhostBrowse\Marshaller\StdClassToSearchGhostBrowse':
                            return $mockStdClassToSearchGhostBrowse;
                            break;
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
         * invoke the marshaller
         */
        $response = $this->marshaller->marshall($success);

        /*
         * Get the elements contained within the response
         */
        $elements = $response->getElements();

        /*
         * assert that the facility id and facility name are the same after marshalling
         */
        $this->assertSame($facilityId, $elements[0]->getId(), 'Asserting that the facilityId passed is the same as the one marshalled');

        $this->assertSame($facilityName, $elements[0]->getName(), 'Asserting that the facility name passed is the same as the one marshalled');
    }

}


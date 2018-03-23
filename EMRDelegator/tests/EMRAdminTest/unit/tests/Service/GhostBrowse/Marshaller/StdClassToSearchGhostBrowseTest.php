<?php

namespace EMRAdminTest\unit\tests\Service\GhostBrowse\Marshaller;

use EMRAdmin\Service\GhostBrowse\Marshaller\StdClassToSearchGhostBrowse;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponse;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseUserCollection;
use EMRAdmin\Service\GhostBrowse\Marshaller\Search\UserPayloadToUser;
use EMRCore\PrototypeFactory;
use PHPUnit_Framework_MockObject_MockObject;
use \stdClass;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class StdClassToSearchGhostBrowseTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var StdClassToSearchGhostBrowse
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new StdClassToSearchGhostBrowse;
    }

    /**
     * Test that the marshaller will throw an exception when invoked with an unexpected data type parameter
     * 
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshallsDueToInvalidItemType()
    {
        $this->marshaller->marshall(array(
        ));
    }

    /**
     * Test that a facility extracted from the payload is being marshalled properly
     */
    public function testMarshallGhostBrowse()
    {

        /*
         * setup initial values for the facility
         */
        $facilityId = 1;
        $facilityName = 'My facility';

        /*
         * recreate the facility extracted from the payload
         */
        $response = new stdClass();
        $response->id = $facilityId;
        $response->name = $facilityName;
        $response->users = array();

        /*
         * setup prototype factory
         */
        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $this->marshaller->setPrototypeFactory($prototypeFactory);

        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name)
                {
                    switch ($name)
                    {
                        case 'EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponse':
                            return new SearchGhostBrowseResponse();
                            break;
                        case 'EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseUserCollection':
                            return new SearchGhostBrowseResponseUserCollection();
                            break;
                        default:
                            throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                            break;
                    }
                }));

        /*
         * setup service locator
         */
        /** @var ServiceLocatorInterface|PHPUnit_Framework_MockObject_MockObject $serviceLocator */
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $serviceLocator->expects($this->any())->method('get')
                ->will($this->returnCallback(function($name) 
                {
                    switch ($name)
                    {
                        case 'EMRAdmin\Service\GhostBrowse\Marshaller\Search\UserPayloadToUser':
                            return new UserPayloadToUser();
                            break;
                        default:
                            throw new InvalidArgumentException("Mocked ServiceLocatorInterface cannot provide [$name].");
                            break;
                    }
                }));

        /*
         * add prototype factory and service locator to the marshaller
         */
        $this->marshaller->setPrototypeFactory($prototypeFactory);
        $this->marshaller->setServiceLocator($serviceLocator);

        /*
         * Invoke the marshaller
         */
        $facility = $this->marshaller->marshall($response);

        /*
         * assert that the facility's initial values are still the same after marshalling
         */
        $this->assertSame($facilityId, $facility->getId(), 'Asserting that the facilityId passed is the same as the one marshalled');

        $this->assertSame($facilityName, $facility->getName(), 'Asserting that the facility name passed is the same as the one marshalled');
    }

}
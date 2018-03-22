<?php

namespace EMRAdminTest\unit\tests\Service\FacilityLogo\Marshaller;

use EMRAdmin\Service\Facility\Marshaller\SuccessToSaveFacilityResponse;
use EMRCore\Marshaller\MarshallerInterface;
use EMRCore\PrototypeFactory;
use EMRAdmin\Service\FacilityLogo\Dto\DeleteFacilityLogoResponse;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use InvalidArgumentException;
use stdClass;
use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\FacilityLogo\Marshaller\SuccessToDeleteFacilityLogoResponse;

/**
 *
 *
 * @category WebPT
 * @package EMRAdmin
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class SuccessToDeleteFacilityLogoResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SuccessToSaveFacilityResponse
     */
    private $marshaller;
    
    public function setUp()
    {
        $this->marshaller = new SuccessToDeleteFacilityLogoResponse;
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidItemType()
    {
        $this->marshaller->marshall(new stdClass);
    }
    
    public function testSuccessToDeleteFacilityLogoResponse()
    {
        /*
         * setup the service locator mock
         */
//        $serviceLocator = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');
//        $serviceLocator->expects($this->once())->method('get')
//            ->with($this->equalTo('EMRAdmin\Service\FacilityLogo\Dto\DeleteFacilityLogoResponse'))
//            ->will($this->returnValue(new DeleteFacilityLogoResponse));
        
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
                        case 'EMRAdmin\Service\FacilityLogo\Dto\DeleteFacilityLogoResponse':
                            return new DeleteFacilityLogoResponse;
                            break;

                            default:
                            throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                            break;
                    }
                }));
    
//       $this->marshaller->setServiceLocator($serviceLocator);       
       $this->marshaller->setprototypeFactory($prototypeFactory);  
       
        $success = new Success();
        $success->setPayload((object) array(
                'id' => 1,
                'success' => true,
            ));
        
        $response = $this->marshaller->marshall($success);
    }
    
}
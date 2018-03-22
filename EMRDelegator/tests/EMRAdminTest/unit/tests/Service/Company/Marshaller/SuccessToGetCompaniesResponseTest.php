<?php

namespace EMRAdminTest\unit\tests\Service\Company\Marshaller;

use EMRAdmin\Service\Company\Dto\CompanyFromDelegator;
use EMRAdmin\Service\Company\Dto\GetCompaniesResponse;
use EMRAdmin\Service\Company\Marshaller\SuccessToGetCompaniesResponse;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class SuccessToGetCompaniesResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SuccessToGetCompaniesResponse
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new SuccessToGetCompaniesResponse();
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
     * Test that a success response is marshalled correctly to a company response object.
     */
    public function testMarshallSuccessToGetCompaniesResponse()
    {   
        /*
         * setup initial company values
         */
        $companyId = 1;
        $name = 'myCompany';
        $onlineStatus = 'A';
        $clusterId = 'ok';
        
        /*
         * create the success object to be marshalled
         */
        $success =  new Success();
        
        /*
         * create the payload
         */
        $payLoad = new stdClass();
        
        $payLoad->companies = array();
        $payLoad->companies[0] = new stdClass();
        $payLoad->companies[0]->companyId = $companyId;
        $payLoad->companies[0]->companyName = $name;
        $payLoad->companies[0]->onlineStatus = $onlineStatus;
        $payLoad->companies[0]->clusterId = $clusterId;
        
        /*
         * add the payload to the success object
         */
        $success->setPayload($payLoad);
        
        /*
         * create the get company response to be rturned by the mock
         */
        $companyFromDelegator = new CompanyFromDelegator();
        
        $companyFromDelegator->setCompanyId($companyId);
        $companyFromDelegator->setName($name);
        $companyFromDelegator->setOnlineStatus($onlineStatus);
        $companyFromDelegator->setClusterId($clusterId);
        
        /*
         * setup the prototype factory mock
         */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name) use ($companyFromDelegator)
                {
                    switch ($name)
                    {
                        case 'EMRAdmin\Service\Company\Dto\GetCompaniesResponse':
                            return new GetCompaniesResponse();
                            break;

                         case 'EMRAdmin\Service\Company\Dto\CompanyFromDelegator':
                            return $companyFromDelegator;
                            break;

                        default:
                            throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                            break;
                    }
                }));
                
         /*
         * add the prototype factory to the marshaller
         */                        
        $this->marshaller->setPrototypeFactory($prototypeFactory);
        
        /*
         * invoke the marshaller
         */
        $response = $this->marshaller->marshall($success);
        
        /*
         * Get the elements contained within the response
         */
        $elements = $response->getElements();
        
        /*
         * assert that the Company id, Company name, Online Status and Cluster Id  are the same after marshalling
         */
        $this->assertSame($companyId, $elements[0]->getCompanyId(), 'Asserting that the companyId passed is the same as the one marshalled');

        $this->assertSame($name, $elements[0]->getName(), 'Asserting that the company name passed is the same as the one marshalled');
        
        $this->assertSame($onlineStatus, $elements[0]->getOnlineStatus(), 'Asserting that the Online Status passed is the same as the one marshalled');

        $this->assertSame($clusterId, $elements[0]->getClusterId(), 'Asserting that the Cluster Id passed is the same as the one marshalled');
        
    }
}

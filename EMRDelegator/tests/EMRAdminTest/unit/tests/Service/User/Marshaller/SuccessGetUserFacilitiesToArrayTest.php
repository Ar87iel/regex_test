<?php
/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 *
 */
namespace EMRAdminTest\unit\tests\Service\User\Marshaller;

use EMRAdmin\Service\User\Marshaller\SuccessGetUserFacilitiesToArray as Marshall;
use PHPUnit_Framework_TestCase;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use InvalidArgumentException;
use stdClass;

class SuccessGetUserFacilitiesToArrayTest extends PHPUnit_Framework_TestCase
{
     /**
     *
     * @var SuccessGetUserToArray 
     */
    private $marshaller;
    
    public function setUp()
    {
        $this->marshaller = new Marshall();
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testMarshallThrowsException()
    {
        $response = $this->marshaller->marshall('user');
    }
    
    /**
     * test marshaller gets Success Class with stdClass and return mixed[]
     */
    public function testMarshall()
    {
        $success = new Success();
        
        $company1 = new stdClass();
        $company1->companyName="asd";
        
        $company2 = new stdClass();
        $company2->companyName="asd";
        
        $facility1 = new stdClass();
        $facility2 = new stdClass();
        
        $facility3 = new stdClass();
        $facility4 = new stdClass();
        
        $clusterId1 = 1;
        $clusterId2 = 2;
        
        $facility1->facilityId = 1;
        $facility1->name = "asd";
        $facility2->facilityId = 2;
        $facility2->name = "asd";
        
        $facility3->facilityId = 3;
        $facility3->name = "asd";
        $facility4->facilityId = 4;
        $facility4->name = "asd";
        
        $company1->facilities[] = $facility1;
        $company1->facilities[] = $facility2;
        
        $company1->clusterId = $clusterId1;
        
        $company2->facilities[] = $facility3;
        $company2->facilities[] = $facility4;
        
        $company2->clusterId = $clusterId2;
        
        $companies[] = $company1;    
        $companies[] = $company2;
        
        $global = new stdClass();
        $global->companies = $companies;
        $global->defaultFacilityId = 1;
        
        $success->setPayload($global);
 
        $response = $this->marshaller->marshall($success);
        
        $this->assertEquals($facility1->facilityId , $response['facilities'][0]['id']);
        $this->assertEquals($facility2->facilityId , $response['facilities'][1]['id']);
        $this->assertEquals($facility3->facilityId , $response['facilities'][2]['id']);
        $this->assertEquals($facility4->facilityId , $response['facilities'][3]['id']);
        $this->assertEquals($clusterId1, $response['clusters'][0]);
        $this->assertEquals($clusterId2, $response['clusters'][1]);
        $this->assertEquals($global->defaultFacilityId, $response['defaultClinic']);
    }
}
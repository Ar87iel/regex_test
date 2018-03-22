<?php
namespace EMRAdminTest\unit\tests\Service\User\Marshaller;

use EMRAdmin\Service\User\Marshaller\SuccessGetUserToArray as Marshall;
use EMRAdmin\Service\User\Marshaller\SuccessGetUserToArray;
use PHPUnit_Framework_TestCase;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use stdClass;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 *
 */
class SuccessGetUserToArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var SuccessGetUserToArray 
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new Marshall;
    }
    
    /**
     * test marshaller gets Success Class with stdClass and return mixed[]
     */
    public function testMarshall()
    {
        $success = new Success();
        
        $stdClass = new stdClass();
        $stdClass->id = '1';
        $stdClass->username = 'User';
        $stdClass->userType = 'PT';
        $stdClass->license = '123';
        $stdClass->nationalProviderId = '1234567893';
        $stdClass->fullname = 'User Dev';
        $stdClass->nameGiven = 'User';
        $stdClass->nameFamily = 'Dev';
        $stdClass->middleName = 'tester';
        $stdClass->credentials = '12312313';
        $stdClass->alternateId = '213';
        $stdClass->status = 'A';
        $stdClass->permissions = '234';
        $stdClass->createCalendar = '432';
        $stdClass->email = 'asd@sdf.com';
        
        $success->setPayload($stdClass);

        $response = $this->marshaller->marshall($success);
        
        $this->assertEquals($stdClass->id, $response['id']);
        $this->assertEquals($stdClass->username, $response['userName']);
        $this->assertEquals($stdClass->userType, $response['userType']);
        $this->assertEquals($stdClass->license, $response['license']);
        $this->assertEquals($stdClass->nationalProviderId, $response['nationalProviderId']);
        $this->assertEquals($stdClass->fullname, $response['fullName']);
        $this->assertEquals($stdClass->nameGiven, $response['firstName']);
        $this->assertEquals($stdClass->nameFamily, $response['lastName']);
        $this->assertEquals($stdClass->middleName, $response['middleName']);
        $this->assertEquals($stdClass->credentials, $response['credentials']);
        $this->assertEquals($stdClass->alternateId, $response['alternateId']);
        $this->assertEquals($stdClass->status, $response['status']);
        $this->assertEquals($stdClass->permissions, $response['permissions']);
        $this->assertEquals($stdClass->createCalendar, $response['createCalendar']);
        $this->assertInstanceOf('EMRCore\Contact\Email\Dto\Email', $response['email']);
    }
    
}

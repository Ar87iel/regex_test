<?php
/**
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRAdminTest\unit\tests\Service\User\Marshaller;

use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\User\Dto\User;
use EMRAdmin\Service\User\Marshaller\SaveUserRequestToArray;

/**
 * Tests the SaveUserRequestToArray Marshaller
 */
class SaveUserRequestToArrayTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->marshaller = new SaveUserRequestToArray();   
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidDto()
    {
        $notAUser = array();
        $response = $this->marshaller->marshall($notAUser);
    }
    
    public function testMarshalling()
    {
        $this->user = new User();
        
        $userData = array(
            'userName' => 'sdfg',
            'password' => 'asd',
            'userType' => 'asd',
            'license' => 'wdsds',
            'nationalProviderId' => 'asd',
            'fullName' => 'wssf',
            'firstName' => 'asd',
            'lastName' => '1234567',
            'email' => 'wssf',
            'middleName' => 'asd',
            'credentials' => 'asd',
            'alternateId' => 'asd',
            'status' => 'asd',
            'permissionsOverride' => true,
            'permissions' => array(),
            'createCalendar' => false,
            'facilities' => array(),
        );
        
        $email = $this->getMock('EMRCore\Contact\Email\Dto\Email', array('getEmail'));
        $email->expects($this->any())->method('getEmail')->will($this->returnValue($userData['email']));
        
        $this->user->setUserName($userData['userName']);
        $this->user->setUserType($userData['userType']);
        $this->user->setPassword($userData['password']);
        $this->user->setLicense($userData['license']);
        $this->user->setNationalProviderId($userData['nationalProviderId']);
        $this->user->setFullName($userData['fullName']);
        $this->user->setFirstName($userData['firstName']);
        $this->user->setLastName($userData['lastName']);
        $this->user->setEmail($email);
        
        $this->user->setMiddleName($userData['middleName']);
        $this->user->setCredentials($userData['credentials']);
        $this->user->setAlternateId($userData['alternateId']);
        $this->user->setStatus($userData['status']);
        $this->user->setPermissionsOverride($userData['permissionsOverride']);
        $this->user->setPermissions($userData['permissions']);
        $this->user->setCreateCalendar($userData['createCalendar']);
        $this->user->setFacilities($userData['facilities']);
        
        $response = $this->marshaller->marshall($this->user);
        
        $this->assertInternalType('array', $response);        
        
        $this->assertEquals($this->user->getUserName(), $response['username']);
        $this->assertEquals($this->user->getUserType(), $response['type']);
        $this->assertEquals($this->user->getUserType(), $response['password']);
        $this->assertEquals($this->user->getLicense(), $response['ptId']);
        $this->assertEquals($this->user->getNationalProviderId(), $response['npi']);
        $this->assertEquals($this->user->getFullName(), $response['fullName']);
        $this->assertEquals($this->user->getEmail()->getEmail(), $response['emailAddress']);
        $this->assertEquals($this->user->getFacilities(), json_decode($response['facilities']));
        $this->assertEquals($this->user->getStatus(), $response['status']);
        $this->assertEquals($this->user->getPermissions(), json_decode($response['permissions']));
        $this->assertEquals($this->user->getPermissionsOverride(), $response['permissionsOverride']);
        $this->assertEquals($this->user->getCreateCalendar(), $response['createCalendar']);
    }
    
    
}


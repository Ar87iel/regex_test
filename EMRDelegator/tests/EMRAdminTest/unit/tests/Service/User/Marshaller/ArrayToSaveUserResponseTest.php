<?php
namespace EMRAdminTest\unit\tests\Service\User\Marshaller;

use EMRAdmin\Service\User\Marshaller\ArrayToSaveUserResponse as Marshall;
use PHPUnit_Framework_TestCase;
use InvalidArgumentException;
use EMRAdmin\Service\User\Dto\User;
use EMRCore\Contact\Email\Dto\Email;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 *
 */
class ArrayToSaveUserResponseTest extends PHPUnit_Framework_TestCase
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
        $user = new User();
        $email = new Email();

        $email->setEmail('dsa@sd.com');

        $facilities = array(1, 2, 3);

        $item = array(
            'id' => 1,
            'userName' => 'sdfg',
            'userType' => 'asd',
            'license' => 'wdsds',
            'nationalProviderId' => 'asd',
            'fullName' => 'wssf',
            'firstName' => 'asd',
            'lastName' => '1234567',
            'email' => $email,
            'middleName' => 'asd',
            'credentials' => 'asd',
            'alternateId' => 'asd',
            'status' => 'asd',
            'permissions' => true,
            'createCalendar' => true,
            'defaultClinic' => 1,
            'clusters' => 3
        );

        foreach ($facilities as $facility)
        {
            $item['facilities'][] = array(
                'id' => $facility,
                'name' => "test",
            );
        }

        $marshaller = new Marshall;

        $response = $marshaller->marshall($item);

        $this->assertEquals($item['id'], $response->getId());
        $this->assertEquals($item['userName'], $response->getUserName());
        $this->assertEquals($item['userType'], $response->getUserType());
        $this->assertEquals($item['license'], $response->getLicense());
        $this->assertEquals($item['nationalProviderId'], $response->getNationalProviderId());
        $this->assertEquals($item['fullName'], $response->getFullName());
        $this->assertEquals($item['firstName'], $response->getFirstName());
        $this->assertEquals($item['lastName'], $response->getLastName());
        $this->assertEquals($item['middleName'], $response->getMiddleName());
        $this->assertEquals($item['credentials'], $response->getCredentials());
        $this->assertEquals($item['alternateId'], $response->getAlternateId());
        $this->assertEquals($item['status'], $response->getStatus());
        $this->assertEquals($item['permissions'], $response->getPermissions());
        $this->assertEquals($item['createCalendar'], $response->getCreateCalendar());
        $this->assertEquals($item['defaultClinic'], $response->getDefaultClinic());
        $this->assertEquals($facilities, $response->getFacilities());
        $this->assertEquals($item['clusters'], $response->getClusters());

        $this->assertInstanceOf('EMRCore\Contact\Email\Dto\Email', $response->getEmail());
    }

}
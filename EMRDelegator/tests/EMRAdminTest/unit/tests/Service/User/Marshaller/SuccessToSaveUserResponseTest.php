<?php

/**
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRAdminTest\unit\tests\Service\User\Marshaller;

use EMRAdmin\Service\User\Dto\User;
use EMRAdmin\Service\User\Marshaller\SuccessToSaveUserResponse;
use EMRCore\Contact\Email\Dto\Email;
use EMRCore\PrototypeFactory;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use stdClass;

/**
 * Test Marshall SuccessToSaveUserResponse.
 */
class SuccessToSaveUserResponseTest extends PHPUnit_Framework_TestCase
{

    /**
     * Mocks a PrototypeFactory object.
     * @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $mockPrototypeFactory;
     */
    private $mockPrototypeFactory;

    /**
     * Mocks a Success object
     * @var Success|PHPUnit_Framework_MockObject_MockObject $success;
     */
    private $success;

    /**
     *
     * @var SuccessToSaveUserResponse 
     */
    private $marshall;

    public function setUp()
    {
        $this->mockPrototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $email = $this->getMock('EMRCore\Contact\Email\Dto\Email', array('getEmail'));
        $email->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue('email'));

        $this->mockPrototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name) use ($email)
                {
                    if ($name == 'EMRAdmin\Service\User\Dto\User')
                    {
                        return new User();
                    } elseif ($name == 'EMRCore\Contact\Email\Dto\Email')
                    {
                        return $email;
                    } else
                    {
                        throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                    }
                }));

        $this->success = $this->getMock('EMRCore\Zend\module\Service\src\Response\Dto\Success');


        $this->marshall = new SuccessToSaveUserResponse();
        $this->marshall->setPrototypeFactory($this->mockPrototypeFactory);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNotSuccessObjectException()
    {
        $notSuccess = array();
        $this->marshall->marshall($notSuccess);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPayloadNotStdObject()
    {
        $notStdClass = array();
        $this->success->expects($this->once())
            ->method('getPayload')
            ->will($this->returnValue($notStdClass));
        $this->marshall->marshall($this->success);
    }

    public function testMarshalling()
    {
        $userData = array(
            'userId' => '1',
            'username' => 'sdfg',
            'type' => 'asd',
            'ptId' => 'wdsds',
            'npi' => 'asd',
            'fullName' => 'wssf',
            'nameGiven' => 'asd',
            'nameFamily' => '1234567',
            'emailAddress' => 'email',
            'nameMiddle' => 'asd',
            'ptCredential' => 'asd',
            'altId' => 'asd',
            'status' => 'asd',
            'permissions' => array(),
            'createCalendar' => false,
            'facilities' => array(),
        );

        $data = (object) array('user' =>array());
        $data->user = (object) $userData;

        $this->success->expects($this->once())
            ->method('getPayload')
            ->will($this->returnValue($data));

        $response = $this->marshall->marshall($this->success);
              
        $this->assertInstanceOf('EMRAdmin\Service\User\Dto\User', $response);
        $this->assertInstanceOf('EMRCore\Contact\Email\Dto\Email', $response->getEmail());
        $this->assertEquals($userData['userId'], $response->getId());
        $this->assertEquals($userData['username'], $response->getUserName());
        $this->assertEquals($userData['type'], $response->getUserType());
        $this->assertEquals($userData['ptId'], $response->getLicense());
        $this->assertEquals($userData['npi'], $response->getNationalProviderId());
        $this->assertEquals($userData['fullName'], $response->getFullName());
        $this->assertEquals($userData['emailAddress'], $response->getEmail()->getEmail());
        $this->assertEquals($userData['status'], $response->getStatus());
        $this->assertEquals($userData['createCalendar'], $response->getCreateCalendar());
    }

}

?>

<?php

/**
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRAdminTest\unit\tests\Service\User\Marshaller;

use EMRAdmin\Service\User\Dto\User;
use EMRAdmin\Service\User\Marshaller\SuccessToSaveUserResponse;
use EMRAdmin\Service\User\Marshaller\SuccessToSaveUserResponseFromAuth;
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
class SuccessToSaveUserResponseFromAuthTest extends PHPUnit_Framework_TestCase
{

    /**
     * Mocks a PrototypeFactory object.
     * @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $mockPrototypeFactory;
     */
    private $mockProtototypeFactory;

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
        $this->mockProtototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $email = $this->getMock('EMRCore\Contact\Email\Dto\Email', array('getEmail'));
        $email->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue('asd'));

        $this->mockProtototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name) use ($email)
                {
                    if ($name == 'EMRAdmin\Service\User\Dto\User')
                    {
                        return new User();
                    } else if ($name == 'EMRCore\Contact\Email\Dto\Email')
                    {
                        return $email;
                    } else
                    {
                        throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                    }
                }));

        $this->success = $this->createMock('EMRCore\Zend\module\Service\src\Response\Dto\Success');


        $this->marshall = new SuccessToSaveUserResponseFromAuth();
        $this->marshall->setPrototypeFactory($this->mockProtototypeFactory);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNotSuccessObjectException()
    {
        $notSucces = array();
        $this->marshall->marshall($notSucces);
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
            'identityId' => 1,
            'username' => 'asd',
            'fullName' => 'asd',
            'nameGiven' => 'asd',
            'nameFamily'=> 'asd',
            'nameMiddle' => 'asd',
            'status' => 'asd',
            'emailAddress'=> 'asd'
        );

        $data = (object) array('identity' =>array());
        $data->identity = (object) $userData;

        $this->success->expects($this->once())
            ->method('getPayload')
            ->will($this->returnValue($data));

        $response = $this->marshall->marshall($this->success);
        
        $this->assertInstanceOf('EMRAdmin\Service\User\Dto\User', $response);
        $this->assertInstanceOf('EMRCore\Contact\Email\Dto\Email', $response->getEmail());
        $this->assertEquals($userData['identityId'], $response->getId());
        $this->assertEquals($userData['username'], $response->getUserName());
        $this->assertEquals($userData['fullName'], $response->getFullName());
        $this->assertEquals($userData['emailAddress'], $response->getEmail()->getEmail());
        $this->assertEquals($userData['status'], $response->getStatus());
    }

}

?>

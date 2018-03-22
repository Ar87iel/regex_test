<?php

namespace EMRAdminTest\unit\tests\Service\User\Marshaller;

use EMRAdmin\Service\User\Marshaller\SuccessToGetUserByIdResponse;
use EMRCore\PrototypeFactory;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use InvalidArgumentException;
use EMRAdmin\Service\User\Dto\User;
use EMRCore\Contact\Email\Dto\Email;

class SuccessToGetUserByIdResponseTest extends PHPUnit_Framework_TestCase
{

    public function testMarshaller()
    {
        $payload = (object) array(
                    'id' => '1',
                    'userType' => 'asd',
                    'license' => 'asd',
                    'username' => 'asd',
                    'nationalProviderId' => '12312',
                    'fullname' => 'asd',
                    'nameGiven' => 'asd',
                    'nameFamily' => 'asd',
                    'middleName' => 'asd',
                    'credentials' => 'asd',
                    'alternateId' => 'asd',
                    'status' => 'active',
                    'permissionsOverride' => true,
                    'permissions' => array(),
                    'createCalendar' => false,
                    'facilities' => array(),
                    'email' => 'asd@g.com',
        );

        /** @var Success $success */
        $success = new Success;
        $success->setPayload($payload);

        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactoryMock */
        $prototypeFactoryMock = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $prototypeFactoryMock->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name)
                {
                    if ($name == 'EMRAdmin\Service\User\Dto\User')
                    {
                        return new User;
                    }
                    if ($name == 'EMRCore\Contact\Email\Dto\Email')
                    {
                        return new Email;
                    }
                    throw new InvalidArgumentException("Mocked prototypeFactory cannot create name [$name].");
                }));

        /** @var SuccessToGetUserByIdResponse $marshaller */
        $marshaller = new SuccessToGetUserByIdResponse();
        $marshaller->setPrototypeFactory($prototypeFactoryMock);

        /** @var User $response */
        $response = $marshaller->marshall($success);

        $this->assertInstanceOf('EMRCore\Contact\Email\Dto\Email', $response->getEmail());
        $this->assertInstanceOf('EMRAdmin\Service\User\Dto\User', $response);
        $this->assertEquals($payload->fullname, $response->getFullName());
        $this->assertTrue(is_array($response->getPermissions()));
        $this->assertTrue(is_array($response->getFacilities()));
    }

}
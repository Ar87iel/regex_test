<?php

namespace EMRAdminTest\unit\tests\Service\User\Marshaller;

use EMRAdmin\Service\User\Marshaller\SuccessToGetUserByIdFromAuthResponse;
use PHPUnit_Framework_TestCase;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use EMRAdmin\Service\User\Dto\User;
use Zend\Stdlib\Hydrator\Reflection;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class SuccessToGetUserByIdFromAuthResponseTest extends PHPUnit_Framework_TestCase
{

    public function testMarshaller()
    {
        // Subject under test.
        $marshaller = new SuccessToGetUserByIdFromAuthResponse;

        // Payload will be encoded and decoded during runtime execution. Best to simulate here.
        $payload = json_decode(json_encode(array(
            'identity' => array(
                'identityId' => 1,
                'username' => 'asd',
                'fullName' => 'asd',
                'nameGiven' => 'asd',
                'nameFamily' => 'asd',
                'nameMiddle' => 'asd',
                'status' => 'asd',
                'emailAddress' => 'asd',
                'applications' => array(
                    array('id' => $applicationId = 1, 'name' => $applicationName = 'asdf'),
                ),
            ),
            'isSuperUser' => true,
        )));

        $success = new Success;
        $success->setPayload($payload);

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->any())->method('get')
            ->will($this->returnValueMap(array(
                array('Zend\Stdlib\Hydrator\Reflection', new Reflection),
            )));

        /** @var User $response */
        $response = $marshaller->marshall($success);

        $this->assertInstanceOf('EMRCore\Contact\Email\Dto\Email', $response->getEmail());
        $this->assertInstanceOf('EMRAdmin\Service\User\Dto\User', $response);
        $this->assertEquals($payload->identity->fullName, $response->getFullName());
        $this->assertTrue($response->getIsSuperUser());
        $this->assertCount(1, $applications = $response->getApplications());
        $this->assertSame($applicationId, $applications[0]->getApplicationId());
        $this->assertSame($applicationName, $applications[0]->getName());
    }

}
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alexosuna
 * Date: 9/4/13
 * Time: 3:34 PM
 * To change this template use File | Settings | File Templates.
 */

namespace EMRAdminTest\unit\tests\Service\User\Marshaller;

use EMRAdmin\Service\User\Marshaller\SuccessGetUserToArray;
use PHPUnit_Framework_MockObject_MockObject;
use Service\Controller\Marshaller\UserWithRoles\ArrayToUserRolesCollection;
use PHPUnit_Framework_TestCase;
use EMRModel\User\UserRole;
use InvalidArgumentException;
use EMRAdmin\Service\User\Dto\UserRoleCollection;
use EMRCore\PrototypeFactory;


class ArrayToUserRolesCollectionTest extends PHPUnit_Framework_TestCase
{
    /** @var SuccessGetUserToArray */
    private $marshaller;

    /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject */
    private $prototypeFactoryMock;

    public function setUp()
    {
        $this->prototypeFactoryMock = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);
    }

    /**
     * test marshaller gets Success Class with stdClass and return mixed[]
     */
    public function testMarshallUserRoleCollection()
    {
        $userRole = new UserRole;

        $data = array(
           $userRole,
        );

        $collection = new UserRoleCollection;

        $this->prototypeFactoryMock->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(function($name) use($collection)
            {
                if ($name == 'EMRAdmin\Service\User\Dto\UserRoleCollection')
                {
                    return $collection;
                }

                throw new InvalidArgumentException("Mocked prototypeFactory cannot create name [$name].");
            }));

        $marshall = new ArrayToUserRolesCollection();

        $marshall->setPrototypeFactory($this->prototypeFactoryMock);

        $response = $marshall->marshall($data);

        $this->assertInstanceOf('EMRAdmin\Service\User\Dto\UserRoleCollection', $response);
        $this->assertTrue(is_array($response->getUserRoleCollection()));

    }
}
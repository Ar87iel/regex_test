<?php

namespace EMRAdminTest\unit\tests\Service\User\Validator;

use EMRAdmin\Service\User\Dto\Permission as PermissionDto;
use EMRAdmin\Service\User\Dto\PermissionCollection;
use EMRAdmin\Service\User\Permission;
use EMRAdmin\Service\User\Validator\ValidatorPermission;
use EMRCore\PrototypeFactory;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Wpt\UserPermissions\PermissionConstants;
use Zend\Validator\ValidatorInterface;

/**
 * Responsible to test all behavior for ValidatorPermission class.
 */
class ValidatorPermissionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->validator = new ValidatorPermission();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        unset($this->validator);
    }

    /**
     * @return Permission[]
     */
    public function validatorProvider()
    {
        $permission = new Permission();
        $permissionValid = new Permission([
            PermissionConstants::INSURANCE_ADMIN_RIGHTS => PermissionConstants::INSURANCE_ADMIN_TAG
        ]);

        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMockBuilder(PrototypeFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnValueMap([
                ['EMRAdmin\Service\User\Dto\PermissionCollection', [], new PermissionCollection()],
                ['EMRAdmin\Service\User\Dto\Permission', [], new PermissionDto()],
            ]));

        $permission->setPrototypeFactory($prototypeFactory);
        $permissionValid->setPrototypeFactory($prototypeFactory);

        return [
            'Permission no valid' => [$permission, false, 1],
            'Permission valid' => [$permissionValid, true, 0],
        ];
    }

    /**
     * Verifies validator behavior.
     *
     * @param Permission $permission
     * @param bool       $validExpected
     * @param int        $messageCountExpected
     *
     * @dataProvider validatorProvider
     */
    public function testValidator($permission, $validExpected, $messageCountExpected)
    {
        static::assertEquals($validExpected, $this->validator->isValid($permission));
        static::assertEquals($messageCountExpected, count($this->validator->getMessages()));
    }
    
    /**
     * Test Exception when trying to validate an impossible value.
     *
     * @expectedException EMRAdmin\Service\User\Exception\PermissionException
     */
    public function testValidatorException()
    {
        $this->validator->isValid(false);
    }
}

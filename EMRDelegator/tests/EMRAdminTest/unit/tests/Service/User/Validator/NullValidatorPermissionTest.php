<?php

namespace EMRAdminTest\unit\tests\Service\User\Validator;

use EMRAdmin\Service\User\Validator\NullValidatorPermission;
use EMRAdmin\Service\User\Permission;
use PHPUnit_Framework_TestCase;

/**
 * Responsible to test NullValidatorPermission class.
 */
class NullValidatorPermissionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Verifies valid and empty message for null behavior.
     */
    public function testValidator()
    {
        $validator = new NullValidatorPermission();

        static::assertTrue($validator->isValid(new Permission()));
        static::assertEmpty($validator->getMessages());
    }
}

<?php

namespace EMRAdminTest\unit\tests\Service\User;

use Assert\InvalidArgumentException;
use EMRAdmin\Service\User\Exception\PermissionException;
use EMRAdmin\Service\User\PermissionEntityCollectionToArray;
use EmrDomain\User\PermissionEntity;
use PHPUnit_Framework_TestCase;
use stdClass;
use Zend\Stdlib\ArrayObject;

/**
 * Responsible to test all behavior on PermissionEntityToArray class.
 */
class PermissionEntityCollectionToArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * Verifies if createService method return an instance of Permissions.
     */
    public function testTransform()
    {
        $entity = $this->getEntity();
        $permissionEntity = new PermissionEntityCollectionToArray();
        $transform = $permissionEntity->transform($entity);

        static::assertEquals(
            $this->getExpectResult(),
            $transform,
            'The transformater iresult is not as expected.'
        );
    }

    /**
     * Verifies transform with exception.
     */
    public function testTransformWithException()
    {
        $permissionEntity = new PermissionEntityCollectionToArray();
        static::setExpectedException(PermissionException::class);
        $permissionEntity->transform(new stdClass());
    }

    /**
     * Return collection of permission entity.
     *
     * @return ArrayObject
     */
    private function getEntity()
    {
        $permission1 = new PermissionEntity();
        $permission1->setCode(1);
        $permission1->setDescription('view patient');
        $permission2 = new PermissionEntity();
        $permission2->setCode(2);
        $permission2->setDescription('Insurance Manager');
        $permissionEntities = new ArrayObject();
        $permissionEntities->append($permission1);
        $permissionEntities->append($permission2);

        return $permissionEntities;
    }

    /**
     * Return expected result
     *
     * @return String[]
     */
    private function getExpectResult()
    {
        return [
            1 => 'view patient',
            2 => 'Insurance Manager',
        ];
    }
}

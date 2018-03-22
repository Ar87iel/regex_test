<?php
/**
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRAdminTest\unit\tests\Service\User\Marshaller;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use EMRAdmin\Service\User\Marshaller\SuccessToSearchUserByCriteria;
use EMRCore\PrototypeFactory;
use InvalidArgumentException;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use EMRAdmin\Service\User\Dto\UserModelCollection;
use EMRModel\User\User;
use stdClass;

class SuccessToSearchUserByCriteriaTest extends PHPUnit_Framework_TestCase
{
    public function testMarshall()
    {
        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name)
                {
                    switch ($name)
                    {
                        case 'EMRAdmin\Service\User\Dto\UserModelCollection':
                            return new UserModelCollection();
                            break;
                        case 'EMRModel\User\User':
                            return new User();
                            break;
                        default:
                            throw new InvalidArgumentException("Mocked prototype factory cannot provide '[{$name}]'");
                            break;
                    }
                }));

        $identity = new stdClass();
        $identity->identityId = 1;
        $identity->username = 'foo';
        $identity->status = 'A';
        $identity->fullName = 'foo foo';
        $identity->nameFamily = 'foo';
        $identity->nameGiven = 'foo';

        $payload = new stdClass();
        $payload->identities = array($identity);

        $success = new Success();
        $success->setPayload($payload);

        $marshaller = new SuccessToSearchUserByCriteria();

        $marshaller->setPrototypeFactory($prototypeFactory);

        /** @var $rs UserModelCollection */
        $rs = $marshaller->marshall($success);

        $this->assertTrue($rs instanceof UserModelCollection, 'Asserting that the returned object is of
        UserModelCollection class');

        $this->assertSame($rs->count(), count($payload->identities), 'Asserting that the same number of identities
        provided are being returned');

        /** @var $marshalledIdentity User */
        $marshalledIdentity = $rs->current();

        $this->assertSame($marshalledIdentity->getUserId(), $identity->identityId, 'asserting that the marshalled
        identityId matches the provided id');

    }
}
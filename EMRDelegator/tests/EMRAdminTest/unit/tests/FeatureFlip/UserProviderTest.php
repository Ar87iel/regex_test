<?php

namespace EMRAdminTest\unit\tests\src\FeatureFlip;

use EMRAdmin\FeatureFlip\UserProvider;
use EMRCore\Session\SessionInterface;
use Exception;
use LaunchDarkly\LDUser;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * Checks user for LD provider
 */
class UserProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SessionInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $session;

    /**
     * @var UserProvider
     */
    private $sut;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->session = $this->createMock(SessionInterface::class);

        $this->sut = new UserProvider($this->session);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();

        unset($this->sut);
    }

    /**
     * Verifies get user returns a correct instance
     */
    public function testGetUser()
    {
        $result = $this->sut->getUser();

        self::assertInstanceOf(LDUser::class, $result);
    }

    /**
     * Verifies get user returns a correct instance if there is a missing option
     */
    public function testGetUserWhenNoOptionExist()
    {
        $this->session->expects(static::any())
            ->method('get')
            ->will(static::throwException(new Exception()));

        $result = $this->sut->getUser();

        self::assertInstanceOf(LDUser::class, $result);
    }
}


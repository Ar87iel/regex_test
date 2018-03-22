<?php

namespace EMRAdminTest\Exception;

use EMRAdmin\Exception\ExpectedClassException;

class ExpectedClassExceptionTest extends \PHPUnit_Framework_TestCase
{
    /** @var ExpectedClassException */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->sut = new ExpectedClassException('\stdClass', 'StuffAndThings');
    }

    public function testInstanceOfUnexpectedValueException()
    {
        self::assertInstanceOf('\EMRAdmin\Exception\UnexpectedValueException', $this->sut);
    }

    public function testActualIsScalar()
    {
        $this->sut = new ExpectedClassException($expected = '\stdClass', $actual = 'StuffAndThings');

        self::assertInternalType('string', $this->sut->getMessage());
        self::assertNotEmpty($this->sut->getMessage());
    }

    public function testActualIsObject()
    {
        $this->sut = new ExpectedClassException($expected = '\stdClass', $actual = new \Exception());

        self::assertInternalType('string', $this->sut->getMessage());
        self::assertNotEmpty($this->sut->getMessage());
    }

    public function testActualIsNull()
    {
        $this->sut = new ExpectedClassException($expected = '\stdClass', $actual = null);

        self::assertInternalType('string', $this->sut->getMessage());
        self::assertNotEmpty($this->sut->getMessage());
    }
}

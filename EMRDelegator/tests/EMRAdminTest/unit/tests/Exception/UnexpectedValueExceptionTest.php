<?php

namespace EMRAdminTest\Exception;

use EMRAdmin\Exception\UnexpectedValueException;

class UnexpectedValueExceptionTest extends \PHPUnit_Framework_TestCase
{
    /** @var UnexpectedValueException */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->sut = new UnexpectedValueException();
    }
    
    public function testInstanceOfUnexpectedValueException()
    {
        self::assertInstanceOf('\UnexpectedValueException', $this->sut);
    }
    
    public function testInstanceOfExceptionInterface()
    {
        self::assertInstanceOf('\EMRAdmin\Exception\ExceptionInterface', $this->sut);
    }
}

<?php

namespace EMRAdminTest\Exception;

use EMRAdmin\Exception\UnexpectedTypeException;

class UnexpectedTypeExceptionTest extends \PHPUnit_Framework_TestCase
{
    /** @var UnexpectedTypeException */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->sut = new UnexpectedTypeException('array', 'StuffAndThings');
    }
    
    public function testInstanceOfUnexpectedValueException()
    {
        self::assertInstanceOf('\EMRAdmin\Exception\UnexpectedValueException', $this->sut);
    }
    
    public function testActualIsScalar()
    {
        $this->sut = new UnexpectedTypeException($expected = 'array', $actual = 'StuffAndThings');
        
        self::assertInternalType('string', $this->sut->getMessage());
        self::assertNotEmpty($this->sut->getMessage());
    }
    
    public function testActualIsObject()
    {
        $this->sut = new UnexpectedTypeException($expected = 'array', $actual = new \stdClass());
        
        self::assertInternalType('string', $this->sut->getMessage());
        self::assertNotEmpty($this->sut->getMessage());
    }
    
    public function testActualIsNull()
    {
        $this->sut = new UnexpectedTypeException($expected = 'array', $actual = null);
        
        self::assertInternalType('string', $this->sut->getMessage());
        self::assertNotEmpty($this->sut->getMessage());
    }
}

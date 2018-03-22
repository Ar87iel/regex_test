<?php

namespace EMRAdminTest\unit\tests\Service\Company\Dto;

use EMRAdmin\Service\Company\Dto\SaveCompanyRequest;
use EMRCore\Contact\Address\Dto\Address;
use EMRCore\Contact\Email\Dto\Email;
use EMRCore\Contact\Telephone\Dto\Telephone;
use PHPUnit_Framework_TestCase;

/**
 * Responsible to check SaveCompanyRequest accessors
 */
class SaveCompanyRequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SaveCompanyRequest
     */
    private $dto;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->dto = new SaveCompanyRequest();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();

        unset($this->dto);
    }

    /**
     * Checks accessors set and return the correct instances
     */
    public function testAccessorMethods()
    {
        // Id accessors
        static::assertNull($this->dto->getId());
        $this->dto->setId($expected = 1);
        static::assertSame($expected, $this->dto->getId());

        // Name accessors
        static::assertNull($this->dto->getName());
        $this->dto->setName($expected = 'Requestmon');
        static::assertSame($expected, $this->dto->getName());

        // Status accessors
        static::assertNull($this->dto->getOnlineStatus());
        $this->dto->setOnlineStatus($expected = 'Alive');
        static::assertSame($expected, $this->dto->getOnlineStatus());

        // ClusterId accessors
        static::assertNull($this->dto->getClusterId());
        $this->dto->setClusterId($expected = 1);
        static::assertSame($expected, $this->dto->getClusterId());

        // Address accessors
        static::assertNull($this->dto->getAddress());
        $this->dto->setAddress($expected = new Address());
        static::assertSame($expected, $this->dto->getAddress());

        // Telephone accessors
        static::assertNull($this->dto->getTelephone());
        $this->dto->setTelephone($expected = new Telephone());
        static::assertSame($expected, $this->dto->getTelephone());

        // Email accessors
        static::assertNull($this->dto->getEmail());
        $this->dto->setEmail($expected = new Email());
        static::assertSame($expected, $this->dto->getEmail());

        // Account type id accessors
        static::assertNull($this->dto->getAccountTypeId());
        $this->dto->setAccountTypeId($expected = 1);
        static::assertSame($expected, $this->dto->getAccountTypeId());

        // Module list id accessors
        static::assertNull($this->dto->getModuleList());
        $this->dto->setModuleList($expected = '1,2,3');
        static::assertSame($expected, $this->dto->getModuleList());
    }
}

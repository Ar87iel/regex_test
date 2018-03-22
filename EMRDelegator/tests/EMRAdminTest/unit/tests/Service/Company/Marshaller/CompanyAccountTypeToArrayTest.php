<?php
namespace EMRAdminTest\unit\tests\Service\Company\Marshaller;

use EMRModel\Company\CompanyAccountType;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\Company\AccountType\Marshaller\CompanyAccountTypeToArray;
use stdClass;

class CompanyAccountTypeToArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CompanyAccountTypeToArray
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new CompanyAccountTypeToArray();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidItemType()
    {
        $this->marshaller->marshall(new stdClass);
    }

    public function testMarshalsCompanyAccountType()
    {
        $companyAccountType = new CompanyAccountType();
        $companyAccountType->setId(1);
        $companyAccountType->setName('Coyotes');
        $companyAccountType->setStatus('I');

        $marshalled = $this->marshaller->marshall($companyAccountType);

        $this->assertArrayHasKey('id', $marshalled);
        $this->assertSame($companyAccountType->getId(), $marshalled['id']);
        $this->assertSame($companyAccountType->getName(), $marshalled['name']);
        $this->assertSame($companyAccountType->getStatus(), $marshalled['status']);
    }
}
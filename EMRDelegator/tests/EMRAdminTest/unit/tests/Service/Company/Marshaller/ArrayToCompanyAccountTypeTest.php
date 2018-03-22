<?php
namespace EMRAdminTest\unit\tests\Service\Company\Marshaller;

use EMRAdmin\Service\Company\Marshaller\CompanyToArray;
use EMRModel\Company\CompanyAccountType;
use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\Company\AccountType\Marshaller\ArrayToCompanyAccountType;


class ArrayToCompanyAccountTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CompanyToArray
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new ArrayToCompanyAccountType();
    }

    public function testMarshalsArrayToCompany()
    {
        $request = array();
        $request['id'] = 1;
        $request['name'] = 'Coyotes';
        $request['status'] = CompanyAccountType::STATUS_INACTIVE;

        /** @var CompanyAccountType $marshalled */
        $marshalled = $this->marshaller->marshall($request);

        $this->assertInstanceOf(get_class(new CompanyAccountType), $marshalled);
        $this->assertSame($request['id'], $marshalled->getId());
        $this->assertSame($request['name'], $marshalled->getName());
        $this->assertSame($request['status'], $marshalled->getStatus());
    }
    public function testMarshalsArrayToCompanyWithNoStatusDefaultsToActive()
    {
        $request = array();
        $request['id'] = 1;
        $request['name'] = 'Coyotes';

        /** @var CompanyAccountType $marshalled */
        $marshalled = $this->marshaller->marshall($request);

        $this->assertInstanceOf(get_class(new CompanyAccountType), $marshalled);
        $this->assertSame($request['id'], $marshalled->getId());
        $this->assertSame($request['name'], $marshalled->getName());
        $this->assertSame(CompanyAccountType::STATUS_ACTIVE, $marshalled->getStatus());
    }
}
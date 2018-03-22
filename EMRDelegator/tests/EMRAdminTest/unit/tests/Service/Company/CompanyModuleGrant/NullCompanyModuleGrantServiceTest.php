<?php

namespace EMRAdminTest\Service\Company\CompanyModuleGrant;

use EMRAdmin\Service\Company\CompanyModuleGrant\CompanyModuleGrantServiceInterface;
use EMRAdmin\Service\Company\CompanyModuleGrant\NullCompanyModuleGrantService;
use PHPUnit_Framework_TestCase;

/**
 * In charge to test CompanyModuleGrantService functionality
 */
class NullCompanyModuleGrantServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CompanyModuleGrantServiceInterface
     */
    private $service;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();


        $this->service = new NullCompanyModuleGrantService();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();

        unset($this->companyModuleService, $this->service);
    }

    public function providerTestGetCompanyModules()
    {
        return [
            'Existing company id' => [1],
            'New company' => [null],
        ];
    }
    /**
     * Checks the getCompanyModules function returns the correct empty array
     *
     * @dataProvider providerTestGetCompanyModules
     *
     * @param int|null $companyId
     */
    public function testGetCompanyModules($companyId = null)
    {
        $result = $this->service->getCompanyModules($companyId);

        static::assertEmpty($result);
    }
}

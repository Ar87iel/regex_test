<?php

namespace EMRAdminTest\Service\Company\CompanyModuleGrant;

use EMRAdmin\Service\Company\CompanyModuleGrant\CompanyModuleGrantServiceInterface;
use EMRAdmin\Service\Company\CompanyModuleGrant\CompanyModuleGrantService;
use EmrDomain\CompanyOrganization\CompanyModule\CompanyModuleEntity;
use EmrPersistenceSdk\Service\CompanyModulesServiceInterface;
use EmrPersistenceSdk\Service\CompanyModuleGrantServiceInterface as SdkCompanyModuleGrantServiceInterface;
use Guzzle\Http\Exception\RequestException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_MockObject_Stub;
use PHPUnit_Framework_TestCase;
use Psr\Log\LoggerInterface;

/**
 * In charge to test CompanyModuleGrantService functionality
 */
class CompanyModuleGrantServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CompanyModulesServiceInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $companyModuleService;

    /**
     * @var SdkCompanyModuleGrantServiceInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $sdkCompanyModuleGrantService;

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

        $this->companyModuleService = $this->createMock(CompanyModulesServiceInterface::class);

        $this->sdkCompanyModuleGrantService = $this->createMock(SdkCompanyModuleGrantServiceInterface::class);

        $this->service = new CompanyModuleGrantService(
            $this->companyModuleService,
            $this->sdkCompanyModuleGrantService
        );
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();

        unset($this->companyModuleService, $this->service);
    }

    /**
     * Provides data with different scenarios testGetCompanyModules
     *
     * @return array
     */
    public function providerTestGetCompanyModules()
    {
        $module = new CompanyModuleEntity();
        $module->setId(1);

        return [
            'Company id not null, company has one module marked' => [
                [['id' => 1, ], ['id' => 2, ], ],
                [$module],
                [['id' => 1, 'checked' => 'true'], ['id' => 2, ], ],
                1,
            ],
            'Company id not null, company has no module marked' => [
                [['id' => 1, ], ['id' => 2, ], ],
                [],
                [['id' => 1, ], ['id' => 2, ], ],
                1,
            ],
            'Company id is null, company has no module marked' => [
                [['id' => 1, ], ['id' => 2, ], ],
                [],
                [['id' => 1, ], ['id' => 2, ], ],
                null,
            ],
        ];
    }

    /**
     * Checks the getCompanyModules function returns the correct array
     *
     * @dataProvider providerTestGetCompanyModules
     *
     * @param array $companyModules
     * @param array $companyModuleGrants
     * @param array $expectedResult
     * @param int|null $companyId
     */
    public function testGetCompanyModules(
        array $companyModules,
        array $companyModuleGrants,
        array $expectedResult,
        $companyId = null
    ) {
        $this->companyModuleService->expects(static::any())
            ->method('getCompanyModules')
            ->will(static::returnValue($companyModules));
        $this->sdkCompanyModuleGrantService->expects(static::any())
            ->method('getCompanyModuleGrants')
            ->will(static::returnValue($companyModuleGrants));

        $result = $this->service->getCompanyModules($companyId);

        static::assertEquals($expectedResult, $result);
    }

    /**
     * Checks the getCompanyModules function returns the correct array
     */
    public function testGetCompanyModulesExpectingException()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($loggerSpy = static::atLeastOnce())->method('error');

        $this->service = new CompanyModuleGrantService(
            $this->companyModuleService,
            $this->sdkCompanyModuleGrantService,
            $logger
        );

        $this->companyModuleService->expects(static::any())
            ->method('getCompanyModules')
            ->will(static::throwException(new RequestException()));

        $result = $this->service->getCompanyModules(1);

        static::assertEmpty($result);
    }
}

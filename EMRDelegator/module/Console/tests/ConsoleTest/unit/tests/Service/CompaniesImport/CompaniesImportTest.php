<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/09/13 11:27 AM
 */
namespace ConsoleTest\Unit;

use Console\Etl\Service\CompaniesImport\Dao\Dto\CompanyResult;
use EMRCoreTest\Helper\Reflection as Helper;
use Console\Etl\Service\CompaniesImport\CompaniesImport;
use Console\Etl\Service\CompaniesImport\Dao\Dto\DelegatorCompany;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\ClassMethods;

class CompaniesImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CompaniesImport
     */
    private $importService;

    public function setup()
    {
        $this->importService = new CompaniesImport();
    }

    public function testWriteCompanyToDelegatorCallsDaoCreateCompany()
    {
        $companyInfo = new CompanyResult();
        $companyInfo->setCompanyId(1);
        $companyInfo->setName('My Company');

        $delegatorCompany = new DelegatorCompany();

        $service = $this->getMock('Console\Etl\Service\CompaniesImport\CompaniesImport', array('getDelegatorCompany'));
        $service->expects($this->once())->method('getDelegatorCompany')
            ->with($companyInfo->getCompanyId(), $companyInfo->getName())
            ->will($this->returnValue($delegatorCompany));

        $dao = $this->getMock('Console\Etl\Service\CompaniesImport\Dao\Dao');
        $dao->expects($this->once())->method('createCompany')
            ->with($delegatorCompany);

        /** @var \Console\Etl\Service\CompaniesImport\CompaniesImport $service */
        $service->setCompaniesImportDao($dao);

        Helper::invoke($service,'writeCompanyToDelegator', array($companyInfo));
    }

    /**
     * @expectedException \Console\Etl\Service\CompaniesImport\Exception\NoCompaniesFound
     */
    public function testImportFromLegacyThrowsExceptionWhenNoCompanies()
    {
        $result = new HydratingResultSet();

        $dao = $this->getMock('Console\Etl\Service\CompaniesImport\Dao\Dao');
        $dao->expects($this->once())->method('getCompanies')
            ->will($this->returnValue($result));

        $this->importService->setCompaniesImportDao($dao);
        $this->importService->importFromLegacy();
    }

    public function testImportFromLegacyCallsWriteForEachCompany()
    {
        $companyInfo1 = array('companyId'=>1,'name'=>'My Company 1');
        $companyInfo2 = array('companyId'=>2,'name'=>'My Company 2');

        $companies = array($companyInfo1, $companyInfo2);

        $companiesResult = new HydratingResultSet(new ClassMethods(), new CompanyResult());
        $companiesResult->initialize($companies);

        $dao = $this->getMock('Console\Etl\Service\CompaniesImport\Dao\Dao');
        $dao->expects($this->once())->method('getCompanies')
            ->will($this->returnValue($companiesResult));

        $dao->expects($this->once())->method('turnOffKeyConstraints');
        $dao->expects($this->once())->method('turnOnKeyConstraints');

        $service = $this->getMock('Console\Etl\Service\CompaniesImport\CompaniesImport',
            array('writeCompanyToDelegator'));
        $service->expects($this->exactly(2))->method('writeCompanyToDelegator');

        /** @var \Console\Etl\Service\CompaniesImport\CompaniesImport $service */
        $service->setCompaniesImportDao($dao);
        $service->importFromLegacy();
    }
}
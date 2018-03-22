<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/08/13 5:05 PM
 */
namespace Console\Etl\Service\CompaniesImport;

use Console\Etl\Service\CompaniesImport\Dao\Dao as CompaniesImportDao;
use Console\Etl\Service\CompaniesImport\Dao\Dto\CompanyResult;
use Console\Etl\Service\CompaniesImport\Dao\Dto\DelegatorCompany;
use Console\Etl\Service\CompaniesImport\Dao\RequiresDaoDiInterface;
use Console\Etl\Service\CompaniesImport\Dto\ImportFromLegacyResult;
use Console\Etl\Service\CompaniesImport\Exception\NoCompaniesFound;
use Logger;

class CompaniesImport implements RequiresDaoDiInterface
{

    /**
     * @var CompaniesImportDao
     */
    private $dao;

    /**
     * @var ImportFromLegacyResult
     */
    private $result;

    /**
     * @var boolean;
     */
    private $verbose;

    /**
     * @return ImportFromLegacyResult
     */
    protected function createImportFromLegacyResult()
    {
        $result = new ImportFromLegacyResult();
        return $result;
    }

    /**
     * @param $id
     * @param $name
     * @return DelegatorCompany
     */
    protected function getDelegatorCompany($id, $name)
    {
        $delegatorCompany = new DelegatorCompany();
        $delegatorCompany->setCompanyId($id)
            ->setName($name);

        return $delegatorCompany;
    }

    /**
     * @param CompanyResult $company
     * @return array
     */
    protected function writeCompanyToDelegator(CompanyResult $company)
    {
        $delegatorCompany = $this->getDelegatorCompany(
            $company->getCompanyId(),
            $company->getName()
        );
        $this->dao->createCompany($delegatorCompany);
        $this->result->setCompanyCount($this->result->getCompanyCount()+1);
    }

    /**
     * If in verbose mode will echo status to STDOUT
     */
    protected function displayStatus()
    {
        if($this->verbose){
            if($this->result->getCompanyCount() % 100 === 0){
                echo 'Migrated '.$this->result->getCompanyCount().' of '.$this->result->getRecordCount().' Companies'.PHP_EOL;
            }
        }
    }


    public function __construct()
    {
        $this->result = $this->createImportFromLegacyResult();
    }

    /**
     * @throws NoCompaniesFound
     * @return ImportFromLegacyResult
     */
    public function importFromLegacy()
    {
        $companies = $this->dao->getCompanies();
        $count = $companies->count();
        if ($count < 1) {
            throw new NoCompaniesFound();
        }
        $this->result->setRecordCount($count);

        $this->dao->turnOffKeyConstraints();

        // clear existing associations
        $this->dao->truncateDelegatorCompanies();

        // iterate through all companies
        foreach ($companies as $company) {
            $this->writeCompanyToDelegator($company);
            $this->displayStatus();
        }

        $this->dao->turnOnKeyConstraints();

        return $this->result;
    }

    /**
     * @param CompaniesImportDao $dao
     * @return mixed
     */
    public function setCompaniesImportDao(CompaniesImportDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param $verbose
     */
    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
    }
}
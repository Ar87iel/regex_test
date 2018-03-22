<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/08/13 5:05 PM
 */
namespace Console\Etl\Service\FacilitiesImport;

use Console\Etl\Service\FacilitiesImport\Dao\Dao as FacilitiesImportDao;
use Console\Etl\Service\FacilitiesImport\Dao\Dao;
use Console\Etl\Service\FacilitiesImport\Dao\Dto\DelegatorFacility;
use Console\Etl\Service\FacilitiesImport\Dao\Dto\FacilityResult;
use Console\Etl\Service\FacilitiesImport\Dao\RequiresDaoDiInterface;
use Console\Etl\Service\FacilitiesImport\Dto\ImportFromLegacyResult;
use Console\Etl\Service\FacilitiesImport\Exception\NoFacilitiesFound;

class FacilitiesImport implements RequiresDaoDiInterface 
{
    /**
     * @var FacilitiesImportDao
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
     * If in verbose mode will echo status to STDOUT
     */
    protected function displayStatus()
    {
        if($this->verbose){
            if($this->result->getFacilityCount() % 100 === 0){
                echo 'Migrated '.$this->result->getFacilityCount().' of '.$this->result->getRecordCount().' Facilities'.PHP_EOL;
            }
        }
    }

    /**
     * @param $id
     * @param $name
     * @return DelegatorFacility
     */
    protected function getDelegatorFacility($id, $name, $companyId)
    {
        $delegatorFacility = new DelegatorFacility();
        $delegatorFacility->setFacilityId($id)
            ->setCompanyId($companyId)
            ->setName($name);

        return $delegatorFacility;
    }

    /**
     * @param FacilityResult $facility
     */
    protected function writeFacilityToDelegator(FacilityResult $facility)
    {
        $delegatorFacility = $this->getDelegatorFacility(
            $facility->getFacilityId(),
            $facility->getName(),
            $facility->getCompanyId()
        );
        $this->dao->createFacility($delegatorFacility);
        $this->result->setFacilityCount($this->result->getFacilityCount()+1);
    }

    public function __construct()
    {
        $this->result = $this->createImportFromLegacyResult(0,0);
    }

    /**
     * @throws NoFacilitiesFound
     * @return ImportFromLegacyResult
     */
    public function importFromLegacy()
    {
        $facilities = $this->dao->getFacilities();
        $count = $facilities->count();
        if ($count < 1) {
            throw new NoFacilitiesFound();
        }
        $this->result->setRecordCount($count);

        $this->dao->turnOffKeyConstraints();

        // clear existing associations
        $this->dao->truncateDelegatorFacilities();

        // iterate through all companies
        foreach ($facilities as $facility) {
            $this->writeFacilityToDelegator($facility);
            $this->displayStatus();
        }

        $this->dao->turnOnKeyConstraints();

        return $this->result;
    }

    /**
     * @param $verbose
     */
    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
    }

    /**
     * @param Dao $dao
     * @return mixed
     */
    public function setFacilitiesImportDao(Dao $dao)
    {
        $this->dao = $dao;
    }
}
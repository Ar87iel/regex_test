<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/08/13 5:05 PM
 */
namespace Console\Etl\Service\UserHasFacilityImport;

use Console\Etl\Service\UserHasFacilityImport\Dao\Dao as UserHasFacilityImportDao;
use Console\Etl\Service\UserHasFacilityImport\Dao\Dto\FacilityHasUser;
use Console\Etl\Service\UserHasFacilityImport\Dao\Dto\DelegatorUserHasFacility;
use Console\Etl\Service\UserHasFacilityImport\Dao\RequiresDaoDiInterface;
use Console\Etl\Service\UserHasFacilityImport\Dto\ImportFromLegacyResult;
use Console\Etl\Service\UserHasFacilityImport\Exception\NoRelationshipsFound;
use EMRDelegator\Service\Facility\Dao\Facility;
use EMRDelegator\Service\Facility\Dao\RequireFacilityDaoDiInterface;
use Logger;

class UserHasFacilityImport implements RequiresDaoDiInterface, RequireFacilityDaoDiInterface
{

    /**
     * @var UserHasFacilityImportDao
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
     * @var Facility
     */
    private $facilityDao;

    /**
     * @return ImportFromLegacyResult
     */
    protected function createImportFromLegacyResult()
    {
        $result = new ImportFromLegacyResult();
        return $result;
    }

    /**
     * @param $identityId
     * @param $facilityId
     * @param $isDefault
     * @return DelegatorUserHasFacility
     */
    protected function getDelegatorUserHasFacility($identityId, $facilityId, $isDefault)
    {
        $userHasFacility = new DelegatorUserHasFacility();
        $userHasFacility->setIdentityId($identityId)
            ->setFacilityId($facilityId)
            ->setIsDefault($isDefault);

        return $userHasFacility;
    }

    /**
     * @param FacilityHasUser $facilityUser
     */
    protected function writeRelationshipToDelegator(FacilityHasUser $facilityUser)
    {
        $userHasFacility = $this->getDelegatorUserHasFacility(
            $facilityUser->getUserId(),
            $facilityUser->getFacilityId(),
            $facilityUser->getDefaultClinic()
        );
        $this->dao->createUserHasFacility($userHasFacility);
        $this->result->addToWrittenCount();
    }

    /**
     * If in verbose mode will echo status to STDOUT
     */
    protected function displayStatus()
    {
        if($this->verbose){
            if($this->result->getWrittenCount() % 100 === 0){
                echo 'Migrated '.$this->result->getWrittenCount().' of '.$this->result->getRecordCount().' UserHasFacility records'.PHP_EOL;
            }
        }
    }

    /**
     * @param FacilityHasUser $facilityHasUser
     * @return bool
     */
    protected function facilityExists(FacilityHasUser $facilityHasUser)
    {
        return $this->dao->facilityExists($facilityHasUser->getFacilityId());
    }


    public function __construct()
    {
        $this->result = $this->createImportFromLegacyResult();
    }

    /**
     * @throws NoRelationshipsFound
     * @return ImportFromLegacyResult
     */
    public function importFromLegacy()
    {
        $relationships = $this->dao->getUserHasFacility();
        $count = $relationships->count();
        if ($count < 1) {
            throw new NoRelationshipsFound();
        }
        $this->result->setRecordCount($count);

        // clear existing associations
        $this->dao->truncateDelegatorUserHasFacility();

        // iterate through all companies
        foreach ($relationships as $facilityHasUser) {
            if($this->facilityExists($facilityHasUser)){
                $this->writeRelationshipToDelegator($facilityHasUser);
            }else{
                $this->result->addFacilityMissing();
            }

            $this->displayStatus();
        }

        return $this->result;
    }

    /**
     * @param UserHasFacilityImportDao $dao
     * @return mixed
     */
    public function setUserHasFacilityImportDao(UserHasFacilityImportDao $dao)
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

    /**
     * @param Facility $facilityDao
     */
    public function setFacilityDao(Facility $facilityDao)
    {
        $this->facilityDao = $facilityDao;
    }

}
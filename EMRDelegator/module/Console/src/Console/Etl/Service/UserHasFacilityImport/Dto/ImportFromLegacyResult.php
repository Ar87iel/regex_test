<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/08/13 4:53 PM
 */

namespace Console\Etl\Service\UserHasFacilityImport\Dto;

class ImportFromLegacyResult
{
    /**
     * @var  int
     */
    private $writtenCount = 0;

    /**
     * @var  int
     */
    private $recordCount = 0;

    /**
     * @var bool
     */
    private $success = true;

    /**
     * @var int
     */
    private $facilityMissing = 0;

    /**
     * Add one to the current facility missing count
     */
    public function addFacilityMissing()
    {
        $this->facilityMissing++;
    }

    /**
     * @param int $facilityMissing
     */
    public function setFacilityMissing($facilityMissing)
    {
        $this->facilityMissing = $facilityMissing;
    }

    /**
     * @return int
     */
    public function getFacilityMissing()
    {
        return $this->facilityMissing;
    }

    /**
     * @param $success
     * @return $this
     */
    public function setSuccess($success)
    {
        $this->success = $success;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @param int $recordCount
     * @return ImportFromLegacyResult
     */
    public function setRecordCount($recordCount)
    {
        $this->recordCount = $recordCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getRecordCount()
    {
        return $this->recordCount;
    }

    /**
     * Increments the writtenCount by one
     */
    public function addToWrittenCount()
    {
        $this->writtenCount++;
    }

    /**
     * @param int $writtenCount
     * @return ImportFromLegacyResult
     */
    public function setWrittenCount($writtenCount)
    {
        $this->writtenCount = $writtenCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getWrittenCount()
    {
        return $this->writtenCount;
    }

}
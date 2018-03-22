<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/08/13 4:53 PM
 */

namespace Console\Etl\Service\FacilitiesImport\Dto;

class ImportFromLegacyResult
{
    /**
     * @var  int
     */
    protected $facilityCount;

    /**
     * @var  int
     */
    protected $recordCount;

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
     * @param int $facilityCount
     * @return ImportFromLegacyResult
     */
    public function setFacilityCount($facilityCount)
    {
        $this->facilityCount = $facilityCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getFacilityCount()
    {
        return $this->facilityCount;
    }

}
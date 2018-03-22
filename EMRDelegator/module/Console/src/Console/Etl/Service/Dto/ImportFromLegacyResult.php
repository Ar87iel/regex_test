<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 5/8/13 5:01 PM
 */

namespace Console\Etl\Service\Dto;


class ImportFromLegacyResult {
    /** @var  int */
    protected $expectedRecordCount;
    /** @var  int */
    protected $actualRecordCount;

    /**
     * @param int $actualRecordCount
     * @return ImportFromLegacyResult
     */
    public function setActualRecordCount($actualRecordCount)
    {
        $this->actualRecordCount = $actualRecordCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getActualRecordCount()
    {
        return $this->actualRecordCount;
    }

    /**
     * @param int $expectedRecordCount
     * @return ImportFromLegacyResult
     */
    public function setExpectedRecordCount($expectedRecordCount)
    {
        $this->expectedRecordCount = $expectedRecordCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpectedRecordCount()
    {
        return $this->expectedRecordCount;
    }

}
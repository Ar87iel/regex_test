<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/08/13 4:53 PM
 */

namespace Console\Etl\Service\CompaniesImport\Dto;

class ImportFromLegacyResult
{
    /**
     * @var  int
     */
    private $companyCount = 0;

    /**
     * @var  int
     */
    private $recordCount = 0;

    /**
     * @var int
     */
    private $exceptionCount = 0;

    /**
     * @var bool
     */
    private $success = true;

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
     * @param int $companyCount
     * @return ImportFromLegacyResult
     */
    public function setCompanyCount($companyCount)
    {
        $this->companyCount = $companyCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getCompanyCount()
    {
        return $this->companyCount;
    }

    /**
     * Adds one to the current exception count
     * @return $this
     */
    public function addExceptionCount()
    {
        $this->exceptionCount++;
        return $this;
    }

    /**
     * @param $exceptionCount
     * @return $this
     */
    public function setExceptionCount($exceptionCount)
    {
        $this->exceptionCount = $exceptionCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getExceptionCount()
    {
        return $this->exceptionCount;
    }



}
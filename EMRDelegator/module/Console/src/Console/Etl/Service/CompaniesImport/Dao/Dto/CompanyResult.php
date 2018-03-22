<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/08/13 5:17 PM
 */

namespace Console\Etl\Service\CompaniesImport\Dao\Dto;


class CompanyResult
{
    /**
     * @var  int
     */
    protected $companyId;

    /**
     * @var  string
     */
    protected $name;

    /**
     * @param int $companyId
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * @return int
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}
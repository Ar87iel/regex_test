<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kevinkucera
 * Date: 5/9/13
 * Time: 3:16 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Console\Etl\Service\FacilitiesImport\Dao\Dto;

class FacilityResult
{
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
     * @param int $facilityId
     */
    public function setFacilityId($facilityId)
    {
        $this->facilityId = $facilityId;
    }

    /**
     * @return int
     */
    public function getFacilityId()
    {
        return $this->facilityId;
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

    /**
     * @var int
     */
    private $facilityId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $companyId;

}
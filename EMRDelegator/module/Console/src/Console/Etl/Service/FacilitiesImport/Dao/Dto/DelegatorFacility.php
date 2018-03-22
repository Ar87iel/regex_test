<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kevinkucera
 * Date: 5/9/13
 * Time: 3:16 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Console\Etl\Service\FacilitiesImport\Dao\Dto;

class DelegatorFacility {

    /**
     * @var int
     */
    private $facilityId;

    /**
     * @var int
     */
    private $companyId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $created;

    /**
     * @var string
     */
    private $lastModified;

    /**
     * Create the object and set defaults
     */
    public function __construct()
    {
        $this->created = gmdate('Y-m-d H:i:s');
        $this->lastModified = gmdate('Y-m-d H:i:s');
    }

    /**
     * @param $facilityId
     * @return $this
     */
    public function setFacilityId($facilityId)
    {
        $this->facilityId = $facilityId;
        return $this;
    }

    /**
     * @return int
     */
    public function getFacilityId()
    {
        return $this->facilityId;
    }

    /**
     * @param $companyId
     * @return $this
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
        return $this;
    }

    /**
     * @return int
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @param $created
     * @return $this
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param $lastModified
     * @return $this
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


}
<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/09/13 5:05 PM
 */

namespace Console\Etl\Service\UserHasFacilityImport\Dao\Dto;

class DelegatorUserHasFacility {

    /**
     * @var int
     */
    private $identityId;

    /**
     * @var boolean
     */
    private $isDefault;

    /**
     * @var int
     */
    private $facilityId;

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
     * @param $identityId
     * @return $this
     */
    public function setIdentityId($identityId)
    {
        $this->identityId = $identityId;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdentityId()
    {
        return $this->identityId;
    }

    /**
     * @param $isDefault
     * @return $this
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = (bool) $isDefault;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsDefault()
    {
        return $this->isDefault;
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

}
<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/09/13 5:05 PM
 */

namespace Console\Etl\Service\UserHasFacilityImport\Dao\Dto;


class FacilityHasUser
{

    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $defaultClinic;

    /**
     * @var int
     */
    private $facilityId;

    /**
     * @param $defaultClinic
     * @return $this
     */
    public function setDefaultClinic($defaultClinic)
    {
        $this->defaultClinic = $defaultClinic;
        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultClinic()
    {
        return $this->defaultClinic;
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
     * @param $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }



}
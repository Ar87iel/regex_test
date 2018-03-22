<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 */

namespace Application\Service\Delegation\Dto;

class Delegate {

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $token;

    /**
     * @var int
     */
    private $ghostId;

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
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param int $unGhostId
     */
    public function setGhostId($unGhostId)
    {
        $this->ghostId = $unGhostId;
    }

    /**
     * @return int
     */
    public function getGhostId()
    {
        return $this->ghostId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @var int
     */
    private $facilityId;

}
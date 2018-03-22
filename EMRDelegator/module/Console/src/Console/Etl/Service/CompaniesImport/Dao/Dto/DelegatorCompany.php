<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kevinkucera
 * Date: 5/8/13
 * Time: 5:44 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Console\Etl\Service\CompaniesImport\Dao\Dto;

use EMRDelegator\Model\Company;

class DelegatorCompany {

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
    private $onlineStatus;

    /**
     * @var int
     */
    private $clusterId;

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
        $this->clusterId = 1;
        $this->onlineStatus = Company::STATUS_ALL;
    }

    /**
     * @param int $clusterId
     */
    public function setClusterId($clusterId)
    {
        $this->clusterId = $clusterId;
        return $this;
    }

    /**
     * @return int
     */
    public function getClusterId()
    {
        return $this->clusterId;
    }

    /**
     * @param int $companyId
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
     * @param string $created
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
     * @param string $lastModified
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
     * @param string $name
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

    /**
     * @param string $onlineStatus
     */
    public function setOnlineStatus($onlineStatus)
    {
        $this->onlineStatus = $onlineStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getOnlineStatus()
    {
        return $this->onlineStatus;
    }

}
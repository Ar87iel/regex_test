<?php
namespace Console\Controller;

use Console\Controller\SystemController;
use EMRDelegator\Service\System\HealthCheck\DatabaseConnector\AuthSqlConnector;
use EMRDelegator\Service\System\HealthCheck\DatabaseConnector\AuthSqlConnectorDiInterface;
use EMRDelegator\Service\System\HealthCheck\DatabaseConnector\MultiTenantSqlConnector;
use EMRDelegator\Service\System\HealthCheck\DatabaseConnector\MultiTenantSqlConnectorDiInterface;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class DelegatorSystemController extends SystemController implements
    MultiTenantSqlConnectorDiInterface,
    AuthSqlConnectorDiInterface
{
    /**
     * @param AuthSqlConnector $service
     * @return mixed
     */
    public function setAuthSqlConnectorHealthCheck(AuthSqlConnector $service)
    {
        $this->healthCheckServices[] = $service;
    }

    /**
     * @param MultiTenantSqlConnector $service
     * @return mixed
     */
    public function setMultiTenantSqlConnectorHealthCheck(MultiTenantSqlConnector $service)
    {
        $this->healthCheckServices[] = $service;
    }
}
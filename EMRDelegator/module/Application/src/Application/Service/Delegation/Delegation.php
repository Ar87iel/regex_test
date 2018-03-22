<?php
namespace Application\Service\Delegation;

use Application\Service\Delegation\Dto\Delegate;
use Application\Service\Delegation\Dto\Delegation as DelegationDto;
use EMRCore\Logger\LoggerDiInterface;
use EMRDelegator\Model\Company;
use EMRDelegator\Service\UserHasFacility\Exception\DefaultCompanyNotFound;
use EMRDelegator\Service\UserHasFacility\UserHasFacility;
use EMRDelegator\Service\UserHasFacility\UserHasFacilityDiInterface;
use Logger;
use stdClass;
use Zend\Config\Config;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 4/9/13 9:37 AM
 */
class Delegation implements ServiceLocatorAwareInterface, LoggerDiInterface, UserHasFacilityDiInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var UserHasFacility
     */
    protected $userHasFacilityService;

    /**
     * @param Delegate $delegateDto
     * @return DelegationDto
     */
    public function delegate(Delegate $delegateDto)
    {
        $userId = ($delegateDto->getGhostId() ? $delegateDto->getGhostId() : $delegateDto->getUserId());
        $facilityId = $delegateDto->getFacilityId();

        try
        {
            $companyModel = $this->getCompanyModel($userId, $facilityId);
        }
        catch (DefaultCompanyNotFound $exception)
        {
            $this->logger->error($exception);

            $companyModel = $this->getCompanyModel($userId);

            $facility = $this->userHasFacilityService->getIdentityDefaultFacility($userId);
            $facilityId = $facility->getFacilityId();
        }

        // Delegation to the appropriate cluster.
        $clusterId = $companyModel->getCluster()->getClusterId();
        $cookie = $this->getCookie($clusterId);

        $url = $this->getInterceptorRedirectUrl(
            $delegateDto->getToken(),
            $companyModel->getCompanyId(),
            $facilityId,
            $delegateDto->getGhostId());

        return $this->getDto($url, $cookie);
    }

    /**
     * @param $token string
     * @param $companyId string
     * @param int|null $facilityId
     * @return string
     */
    public function getInterceptorRedirectUrl($token, $companyId, $facilityId = null, $ghostId = null)
    {
        $url = $this->getInterceptorBaseUrl()
            . "?companyId=" . $companyId
            . "&wpt_sso_token=" . $token;
        if($facilityId) {
            $url .= "&facilityId=".$facilityId;
        }
        if($ghostId){
            $url .= "&ghostId=".$ghostId;
        }
        return $url;
    }

    /**
     * @param $clusterId int
     * @return \Zend\Http\Header\SetCookie
     */
    protected function getCookie($clusterId)
    {
        /** @var $cookie \Zend\Http\Header\SetCookie */
        $cookie = $this->serviceLocator->get('Zend\Http\Header\SetCookie');
        $cookie->setName($this->getCookieName());
        $cookie->setValue($clusterId);
        $cookie->setSecure(true);
        $cookie->setDomain($this->getCookieDomain());
        $cookie->setPath('/');
        
        return $cookie;
    }

    /**
     * @return stdClass
     */
    protected function getCookieSettings()
    {
        return $this->getConfig()->cookie_settings;
    }

    /**
     * @return stdClass
     */
    protected function getClusterAssignmentCookieSettings()
    {
        return $this->getCookieSettings()->cluster_assignment;
    }

    /**
     * @return string
     */
    protected function getCookieDomain()
    {
        return $this->getClusterAssignmentCookieSettings()->domain;
    }

    /**
     * @return string
     */
    protected function getCookieName()
    {
        return $this->getClusterAssignmentCookieSettings()->name;
    }

    /**
     * @return string
     */
    protected function getInterceptorBaseUrl()
    {
        // make sure interceptor URL has a trailing /,
        // redirects without it result in 301 redirects to the trailing slash version
        // which tends to break IE (DE1836)
        return rtrim($this->getConfig()->slices->interceptor->base,'/') . '/';
    }

    /**
     * @return string
     */
    protected function getSsoBaseUrl()
    {
        return $this->getConfig()->slices->sso->base;
    }

    /**
     * @return Config
     */
    protected function getConfig()
    {
        if (!$this->config) {
            $this->config = new Config($this->serviceLocator->get('Config'));
        }
        return $this->config;
    }

    /**
     * @param $userId
     * @param $userId
     * @param int|null $facilityId
     * @return Company
     */
    protected function getCompanyModel($userId, $facilityId = null)
    {
        if ($facilityId != null)
        {
            return $this->userHasFacilityService->getCompanyByIdentityIdAndFacilityId($userId, $facilityId);
        }

        return $this->userHasFacilityService->getIdentityDefaultCompany($userId);
    }

    /**
     * @param $url
     * @param $cookie
     * @return DelegationDto
     */
    protected function getDto($url, $cookie)
    {
        $delegation = new DelegationDto();
        $delegation->setCookie($cookie);
        $delegation->setUrl($url);
        return $delegation;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param Logger $logger
     * @return void
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param UserHasFacility $service
     * @return void
     * @setter
     */
    public function setUserHasFacilityService(UserHasFacility $service)
    {
        $this->userHasFacilityService = $service;
    }

    /**
     * @return UserHasFacility
     */
    public function getUserHasFacilityService()
    {
        return $this->userHasFacilityService;
    }
}
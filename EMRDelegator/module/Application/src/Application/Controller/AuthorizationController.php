<?php
/**
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2013 WebPT, INC
 */
namespace Application\Controller;

use EMRCore\EsbFactory;
use EMRCore\Service\Auth\Token\Dto\AuthorizeReturn;
use EMRCore\Service\Auth\Token\Exception\Authentication as TokenAuthenticationException;
use EMRCore\Service\Auth\Token\Token;
use EMRCore\Service\Session\Authorization;
use EMRCore\Zend\Mvc\Controller\ActionControllerAbstract;
use EMRDelegator\Service\Session\Exception\SessionRegistryNotFound;
use EMRDelegator\Service\Session\Registry;
use Zend\Config\Config;
use Zend\Http\Request;
use Zend\Mvc\Controller\Plugin\Forward;
use Zend\Mvc\Controller\Plugin\Redirect;

class AuthorizationController extends ActionControllerAbstract
{

    /**
     * @var Registry
     */
    private $sessionRegistryService;

    /**
     * @return Registry
     */
    protected function getSessionRegistryService()
    {
        if(empty($this->sessionRegistryService)){
            $this->sessionRegistryService = $this->getServiceLocator()->get('EMRDelegator\Service\Session\Registry');
        }
        return $this->sessionRegistryService;
    }

    /**
     * @return bool
     */
    protected function isEvictionEnabled()
    {
        $config = $this->serviceLocator->get('config');
        return (bool) $config['evictionEnabled'];
    }

    /**
     * @return bool
     */
    protected function isEvictionDisabled()
    {
        return ! $this->isEvictionEnabled();
    }

    /**
     * Check if the user already has a different session register and if so forward to eviction.
     * @param AuthorizeReturn $authorizedUser
     * @return bool
     */
    protected function evictionRequired(AuthorizeReturn $authorizedUser)
    {
        // don't require eviction for admin (super) users.
        if ($authorizedUser->isAdmin()){
            return false;
        }
        if ($this->isEvictionDisabled())
        {
            return false;
        }
        try{
            $sessionRegistry = $this->getSessionRegistryService()->getByIdentityId($authorizedUser->getUserId());

            if($sessionRegistry->getSsoToken() !== $authorizedUser->getSessionId()){
                return true;
            }
        }catch(SessionRegistryNotFound $ex){
            // great the user is not already delegated
            $this->registerSession($authorizedUser);
        }
        return false;
    }

    public function authorizeAction()
    {
        /** @var $redirect Redirect */
        $redirect = $this->plugin('redirect');

        /** @var $request Request */
        $request = $this->getEvent()->getRequest();

        $token = $request->getQuery('wpt_sso_token', false);
        $ghostId = $request->getQuery('ghostId',false);
        $facilityId = $request->getQuery('facilityId', null);

        $config = new Config($this->serviceLocator->get('Config'));
        $ssoRedirect = base64_encode($config->slices->delegator->base);
        $redirectToVerifyUrl = $config->slices->sso->verify.'?r=' . $ssoRedirect;
        $redirectToLogoutUrl = $config->slices->sso->base.$config->slices->sso->logout;

        if($token === false)
        {
            $this->logger->debug("token [$token] is invalid.");

            return $redirect->toUrl($redirectToVerifyUrl);
        }

        try {
            /** @var $tokenService Token */
            $tokenService = $this->serviceLocator->get('EMRCore\Service\Auth\Token\Token');
            $authorizedUser = $tokenService->authorize($token);

            /** @var $sessionAuthorizationService Authorization */
            $sessionAuthorizationService = $this->serviceLocator->get('EMRCore\Service\Session\Authorization');
            $sessionAuthorizationService->hydrateAuthorizationSession($authorizedUser);

            if($this->evictionRequired($authorizedUser))
            {
                /** @var $forward Forward */
                $forward = $this->getPluginManager()->get('forward');

                // Forward to the EvictionController to display the notice.
                return $forward->dispatch('Application\Controller\Eviction', array(
                    'action' => 'display',
                    'wpt_sso_token' => $authorizedUser->getSessionId()
                ));
            }
            
            if(empty($facilityId)){
                $userId = $ghostId ?: $authorizedUser->getUserId();
                $facilityId = $this->getDefaultFacilityId($userId);
            }

        }
        catch (TokenAuthenticationException $e)
        {
            $this->logger->debug($e);

            return $redirect->toUrl($redirectToLogoutUrl);
        }

        if(!empty($ghostId))
        {
            $this->logger->debug("Delegating SuperUser token [$token] ghostId [$ghostId] facilityId [$facilityId]");

            return $this->getPluginManager()
                ->get('forward')
                ->dispatch('Application\Controller\SuperUserDelegation',
                    array('action'=>'delegate','token' => $token,'ghostId' => $ghostId, 'facilityId' => $facilityId));

        }
        else
        {
            $this->logger->debug("Delegating User token [$token] facilityId [$facilityId]");

            return $this->getPluginManager()
                ->get('forward')
                ->dispatch('Application\Controller\Delegation',
                    array('action'=>'delegate','token' => $token,'facilityId' => $facilityId));
        }

    }

    /**
     * @return \EMRDelegator\Service\UserHasFacility\UserHasFacility
     */
    protected function getUserHasFacilityService()
    {
        return $this->getServiceLocator()->get('EMRDelegator\Service\UserHasFacility\UserHasFacility');
    }

    /**
     * @param $userId
     * @return int
     */
    protected function getDefaultFacilityId($userId)
    {
        $service = $this->getUserHasFacilityService();
        $facility = $service->getIdentityDefaultFacility($userId);
        return $facility->getFacilityId();
    }

    /**
     * @param AuthorizeReturn $authorizedUser
     */
    protected function registerSession(AuthorizeReturn $authorizedUser)
    {
        $this->getSessionRegistryService()->create(
            $authorizedUser->getUserId(),
            $authorizedUser->getSessionId()
        );
    }


}
<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2012 WebPT, INC
 */
namespace Application;

use EMRCore\Config\Application;
use EMRCore\Session\Exception\Expired;
use EMRCore\Session\Exception\NotHydrated;
use EMRCore\Zend\Config\Utility;
use EMRCore\Zend\Module\Application\ModuleEventAbstract;
use Zend\Config\Config as ZendConfig;
use Zend\EventManager\StaticEventManager;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Logger;

class Module extends ModuleEventAbstract
{
    /**
     * Each slice module should override this method to determine if controllers should expect
     * companyId being provided. Slices like SSO/Delegator don't need companyId specified as they have their own
     * schema that is not related to tenants.
     * @return bool
     */
    protected function checkForCompanyIdParam()
    {
        return false;
    }

    /**
     * Get the module config.
     * @return array
     */
    public function getConfig()
    {
        $coreModuleConfig = new ZendConfig( parent::getConfig() );
        $sliceModuleConfig = new ZendConfig( include __DIR__ . '/config/module.config.php' );

        $mergedConfig = $coreModuleConfig->merge( $sliceModuleConfig );
        return $mergedConfig->toArray();
    }

    /**
     * handle dispatch errors after they have occurred.
     * @param MvcEvent $mvcEvent
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function onDispatchError(MvcEvent $mvcEvent)
    {
        $target = $mvcEvent->getTarget();

        if ($target instanceof AbstractActionController) {

            $exception = $mvcEvent->getParam('exception');

            if ($exception instanceof NotHydrated || $exception instanceof Expired) {

                $applicationConfig = Application::getInstance()->getConfiguration();
                $redirectConfig = Utility::toArray($applicationConfig, 'slices');

                // Redirect back to EMRAuth.
                $redirectUrl = $redirectConfig['sso']['base']; // TODO add a message.

                /** @var $redirect \Zend\Mvc\Controller\Plugin\Redirect */
                $redirect = $mvcEvent->getTarget()->plugin('redirect');
                return $redirect->toUrl($redirectUrl);
            }
        }

        return parent::onDispatchError($mvcEvent);
    }

    /**
     * Handle dispatches
     * @param MvcEvent $mvcEvent
     */
    public function onDispatch(MvcEvent $mvcEvent)
    {
        $csrfPlugin = $mvcEvent->getApplication()
                               ->getServiceManager()
                               ->get('WebPT\EMR\Csrf\CsrfPlugin');
        $csrfPlugin->startEngine();

        $csrfPlugin->tokenListener();

        parent::onDispatch($mvcEvent);
    }
}
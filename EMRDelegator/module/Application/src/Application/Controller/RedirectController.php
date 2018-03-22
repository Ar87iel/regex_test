<?php
/**
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2013 WebPT, INC
 */
namespace Application\Controller;

use EMRCore\PrototypeFactory;
use EMRCore\Utility;
use EMRDelegator\Model\Company;
use EMRDelegator\Service\UserHasFacility\UserHasFacility as UserHasFacilityService;
use Zend\Config\Config;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\Plugin\Redirect;

class RedirectController extends ProtectedByAuthorizationSessionActionControllerAbstract
{

    public function redirectAction()
    {
        $url = $this->getEvent()->getRouteMatch()->getParam('url');

        /** @var $redirect Redirect */
        $redirect = $this->plugin('redirect');

        $this->getAuthorizationSession()->destroy();

        return $redirect->toUrl($url);
    }

    /**
     * @return UserHasFacilityService
     */
    private function getUserHasFacilityService()
    {
        return $this->serviceLocator->get('EMRDelegator\Service\UserHasFacility\UserHasFacility');
    }

}

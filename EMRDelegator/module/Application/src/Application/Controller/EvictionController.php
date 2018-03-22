<?php
/**
 * Eviction controller.
 *
 * Authorize forwards a user here if necessary, where they are presented with a UI with two options via displayAction.
 *
 * The two options are actions here "evict" or "cancel".
 *
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2012 WebPT, INC
 */
namespace Application\Controller;

use EMRCore\Session\SessionFactory;


use Zend\View\Model\ViewModel;

class EvictionController extends ProtectedByAuthorizationSessionActionControllerAbstract
{
    /**
     * @return mixed
     */
    protected function getSsoBaseUrl(){
        /** @var  $config */
        $config = $this->serviceLocator->get('Config');
        return $config['slices']['sso']['base'];
    }

    /**
     * Endpoint for user wanting to evict the current user(s).
     * @return bool
     */
    public function evictAction()
    {
        /** @var $evictionService \EMRDelegator\Service\Session\Evict */
        $evictionService = $this->serviceLocator->get('EMRDelegator\Service\Session\Evict');

        $userId = $this->getUserId();

        // Evict the users
        $evictionService->evictUser($userId);

        return $this->redirectToRoute('default/authorization');
    }

    /**
     * Endpoint for user not wanting to evict current user(s).
     */
    public function cancelAction()
    {
        /** @var $redirect \Zend\Mvc\Controller\Plugin\Redirect */
        $redirect = $this->plugin('redirect');

        $url = $this->getSsoBaseUrl();

        return $redirect->toUrl($url.'/logout/');
    }


    public function displayAction()
    {
        $userId = $this->getUserId();

        $viewModel = new ViewModel();
        $viewModel->setTemplate('application/eviction/eviction_notifier.phtml');
        $viewModel->setVariable('userId', $userId);

        return $viewModel;
    }

    /**
     * Get the currenet user's id from the authorization session.
     *
     * @return int
     */
    private function getUserId()
    {
        return (int)$this->getAuthorizationSession()->get('userId');
    }

    /**
     * @param $route
     * @param array $params
     * @param array $options
     * @return \Zend\Http\Response
     */
    private function redirectToRoute($route, $params = array(), $options = array())
    {
        /** @var $redirect \Zend\Mvc\Controller\Plugin\Redirect */
        $redirect = $this->plugin('redirect');

        return $redirect->toRoute($route, $params, $options);
    }

}

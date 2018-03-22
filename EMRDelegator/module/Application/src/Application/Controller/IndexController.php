<?php
/**
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2012 WebPT, INC
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use EMRCore\Session\SessionInterface;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    /**
     * @var \EMRCore\Session\SessionInterface
     */
    private $applicationSession;

    public function indexAction()
    {
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent("WebPT EMRDelegator Application");

        return $response;
    }

    /**
     * Demonstration page for DataTables and styled UI
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function demoAction()
    {
        $viewModel = new ViewModel();

        $this->layout('emr_sidebar_layout');

        return $viewModel;

    }

    public function setApplicationSession(SessionInterface $session)
    {
        $this->applicationSession = $session;
        return $this;
    }

}
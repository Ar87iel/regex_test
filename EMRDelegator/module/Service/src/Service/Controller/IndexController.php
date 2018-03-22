<?php
/**
 * The service index page. Might be used for public facing documentation in the future.
 *
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2012 WebPT, INC
 */
namespace Service\Controller;

use EMRCore\Zend\Mvc\Controller\ActionControllerAbstract;

class IndexController extends ActionControllerAbstract
{
    public function indexAction()
    {
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent("WebPT EMRDelegator Restful Web Services");

        return $response;
    }
}
<?php
/**
 * @category WebPT 
 * @package EMRDelegator
 * @author: kevinkucera
 * 5/24/13 11:25 AM
 */

namespace Service\Controller;

use EMRCore\EsbFactory;
use EMRCore\Zend\Http\ClientWrapper;
use EMRCore\Zend\Mvc\Controller\ActionControllerAbstract;
use InvalidArgumentException;
use Zend\Mvc\Controller\Plugin\Params;
use EMRCore\Zend\Module\Service\Response\Content;

class LogoutController extends ActionControllerAbstract
{

    /**
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function getSsoToken()
    {
        $params = $this->getParamsPlugin();
        $ssoToken = $params->fromQuery('wpt_sso_token');
        if(empty($ssoToken)){
            throw new InvalidArgumentException('Parameter wpt_sso_token is required.');
        }
        return $ssoToken;
    }

    /**
     * @return Params
     */
    protected function getParamsPlugin()
    {
        return $this->getPluginManager()->get('params');
    }

    /**
     * @return \Zend\Http\Response
     */
    protected function getPreparedResponse()
    {
        /** @var $response Content */
        $response = $this->getServiceLocator()->get('ServiceResponseContent');
        $response->setContent(array(
            'success' => true,
        ));

        return $response;
    }

    /**
     * @return string[]
     */
    protected function getLogoutUrls()
    {
        $config = $this->serviceLocator->get('Config');
        return $config['logout-urls'];
    }

    /**
     * @param string $url
     * @param int $method
     * @param array $params
     */
    protected function callEsb($url, $method, $params=array())
    {
        /** @var $clientWrapper ClientWrapper */
        $clientWrapper = EsbFactory::get($url, $method, $params);
        $clientWrapper->execute();
    }

    /**
     * @return \EMRDelegator\Service\Session\Logout
     */
    protected function getLogoutService()
    {
        return $this->getServiceLocator()->get('EMRDelegator\Service\Session\Logout');
    }

    /**
     * Destroys the session registry record for the provided token.
     * @return \Zend\Http\Response
     */
    public function logoutAction()
    {
        $token = $this->getSsoToken();

        $this->getLogoutService()->logout($token);

        return $this->getPreparedResponse();
    }

}
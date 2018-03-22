<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jkozel
 * 10/18/13 4:08 PM 
 */
namespace Service\Controller;

use EMRCore\Zend\Grant\IpGrantDiInterface;
use EMRCore\Zend\Mvc\Controller\ActionControllerAbstract;
use EMRCore\Zend\Module\Service\Response\Content;
use EMRDelegator\Service\Session\Registry;
use InvalidArgumentException;
use Zend\Http\Request;

class SessionRegistryController extends ActionControllerAbstract implements IpGrantDiInterface
{
    /**
     * Receives an array of SsoTokens and then evicts these Tokens
     * from the SessionRegistry
     * @return Content
     */
    public function deleteSessionsAction()
    {
        /** @var $request Request */
        $request = $this->getEvent()->getRequest();

        $jsonSsoTokens = $request->getPost('wpt_sso_tokens');
        $ssoTokens = json_decode($jsonSsoTokens);

        if(!is_array($ssoTokens)) {
            $ssoTokens = json_decode(urldecode($jsonSsoTokens));
        }

        if(!is_array($ssoTokens)){
            $this->getLogger()->error("Wpt SSO tokens were not parsed, raw tokens were: $jsonSsoTokens");
            throw new InvalidArgumentException('wpt_sso_tokens parameter must contain a json encoded array of tokens');
        }

        /** @var Registry $registryService */
        $registryService = $this->serviceLocator->get('EMRDelegator\Service\Session\Registry');

        //evict all ssoTokens
        foreach($ssoTokens as $ssoToken)
        {
            $registryService->deleteBySsoToken($ssoToken);
        }

        return $this->getContentResponse(array(
            'success' => true,
        ));
    }

}
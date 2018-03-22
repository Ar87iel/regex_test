<?php
/**
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2013 WebPT, INC
 */
namespace Application\Controller;

use Application\Service\Delegation\Dto\Delegate;
use EMRCore\EsbFactory;
use EMRDelegator\Model\Agreement;
use EMRDelegator\Model\Company;
use EMRDelegator\Service\Agreement\Agreement as AgreementService;
use Application\Service\Delegation\Delegation;
use EMRDelegator\Service\Announcement\Announcement;
use Zend\Config\Config;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\Plugin\Forward;
use Zend\Mvc\Controller\Plugin\Params;

class DelegationController extends ProtectedByAuthorizationSessionActionControllerAbstract {

    /**
     * @return int|null
     */
    protected function getFacilityId()
    {
        return $this->getParamFromAny('facilityId', null);
    }

    /**
     * @return int
     */
    protected function getUserId()
    {
        return $this->getAuthorizationSession()->get('userId');
    }

    /**
     * @return mixed
     */
    public function delegateAction()
    {
        // handle unseen announcements
        $announcements = $this->getUnseenAnnouncements();
        if($announcements){
            return $this->forwardToAnnouncements($announcements);
        }

        // handle outstanding agreements
        $agreement = $this->getOutstandingAgreement();
        if($agreement) {
            return $this->forwardToAgreements($agreement);
        }

        $delegateDto = new Delegate();
        $delegateDto->setUserId($this->getUserId());
        $delegateDto->setFacilityId($this->getFacilityId());

        return $this->delegate($delegateDto);
    }

    /**
     * Get any unseen announcements
     * @return \EMRDelegator\Model\Announcement[]
     */
    protected function getUnseenAnnouncements()
    {
        /** @var $announcementService Announcement */
        $announcementService = $this->getServiceLocator()->get('EMRDelegator\Service\Announcement\Announcement');
        return $announcementService->getOutstandingAnnouncements($this->getUserId());
    }

    /**
     * @param $announcements
     * @return mixed
     */
    protected function forwardToAnnouncements($announcements)
    {
        return $this->getPluginManager()
            ->get('forward')
            ->dispatch('Application\Controller\Announcements',
                array(
                    'action' => 'display',
                    AnnouncementsController::DISPLAY_ANNOUNCEMENTS_PARAM_NAME => $announcements,
                    'facilityId' => $this->getFacilityId(),
                    'token' => $this->getToken(),
                ));
    }

    /**
     * @param int $userId
     * @param null|int $facilityId
     * @return mixed
     */
    protected function delegate(Delegate $delegateDto){
        $delegateDto->setToken($this->getToken());

        /** @var $delegationService Delegation */
        $delegationService = $this->getServiceLocator()->get('Application\Service\Delegation\Delegation');

        $delegationDto = $delegationService->delegate($delegateDto);
        $url = $delegationDto->getUrl();

        if($delegationDto->getCookie()){
            /** @var $response Response */
            $response = $this->getResponse();
            $response->getHeaders()->addHeader($delegationDto->getCookie());
        }

        return $this->forwardToRedirect($url);
    }

    /**
     * @param string[] $types
     * @return Agreement
     */
    protected function getOutstandingAgreement($types=null)
    {
        /** @var AgreementService $agreementService */
        $agreementService = $this->serviceLocator->get('EMRDelegator\Service\Agreement\Agreement');
        return $agreementService->getOutstanding($this->getUserId(),$types);
    }

    /**
     * @param Agreement $agreement
     * @return mixed
     */
    protected function forwardToAgreements(Agreement $agreement)
    {
        return $this->getForwardPlugin()->dispatch('Application\Controller\Agreement', array(
            'action' => 'display',
            AgreementController::DISPLAY_AGREEMENT_PARAM_NAME => $agreement,
            'facility' => $this->getFacilityId(),
            'token' => $this->getToken(),
        ));
    }

    /**
     * @return Forward
     */
    protected function getForwardPlugin()
    {
        return $this->getPluginManager()
            ->get('forward');
    }

    /**
     * @return string
     */
    protected function getToken()
    {
        return $this->getParamFromAny('token');
    }

    protected function forwardToRedirect($url)
    {
        return $this->getForwardPlugin()
            ->dispatch('Application\Controller\Redirect', array('action'=>'redirect','url' => $url));
    }

    /**
     * @return Params
     */
    protected function getParamsPlugin()
    {
        return $this->getPluginManager()->get('params');
    }

    protected function getParamFromAny($name) {
        $value = $this->getParamsPlugin()->fromRoute($name,null);
        if(!$value)
            $value = $this->getParamsPlugin()->fromPost($name,null);
        return $value;
    }

}
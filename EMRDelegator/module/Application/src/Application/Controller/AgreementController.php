<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 5/15/13 4:57 PM
 */

namespace Application\Controller;

use EMRDelegator\Service\Agreement\Dao\Dto\SignData;
use Zend\Mvc\Controller\Plugin\Params;
use Zend\View\Model\ViewModel;
use EMRDelegator\Service\Agreement\Agreement as AgreementService;

class AgreementController extends ProtectedByAuthorizationSessionActionControllerAbstract {
    const DISPLAY_AGREEMENT_PARAM_NAME = 'agreement';

    /**
     * Acknowledge agreements
     */
    public function agreedAction() {
        $agreementId = $this->getAgreementIdFromRequest();
        $this->signAgreement($agreementId);
        return $this->forwardToDelegation($this->getToken(), $this->getFacilityId(), $this->getGhostId());
    }

    /**
     * Display Agreements
     * @return ViewModel
     */
    public function displayAction() {
        $identityId = $this->getUserId();
        $agreement = $this->getAgreement();

        $viewModel = $this->getViewModel();

        if($agreement->getAgreementType()->getTypeKey() === 'BAA'){
            /** @var $agreementService AgreementService */
            $agreementService = $this->serviceLocator->get('EMRDelegator\Service\Agreement\Agreement');
            $agreement = $agreementService->convertAgreementToHtmlFromJson($agreement);
            $userData = $agreementService->getBaaUserInfo($identityId);
            $viewModel->setTemplate('application/agreement/baAgreement.phtml');
            $viewModel->setVariable('userData', $userData);
        }else{
            $viewModel->setTemplate('application/agreement/agreement.phtml');
        }

        $viewModel->setVariable('agreement', $agreement);
        $viewModel->setVariable('logoutUrl', $this->getLogoutUrl());
        $viewModel->setVariable('token', $this->getToken());
        $viewModel->setVariable('facilityId', $this->getFacilityId());
        $viewModel->setVariable('ghostId', $this->getGhostId());

        return $viewModel;
    }

    /**
     * @return ViewModel
     */
    protected function getViewModel()
    {
        return new ViewModel();
    }

    /**
     * @return \EMRDelegator\Model\Agreement
     */
    protected function getAgreement()
    {
        // Get the agreement that was loaded and set in AuthorizeController->authorizeAction();
        $agreement = $this->getAgreementFromRouteMatch();

        if (empty($agreement)) {
            /** @var $agreementService AgreementService */
            $agreementService = $this->serviceLocator->get('EMRDelegator\Service\Agreement\Agreement');
            $agreement = $agreementService->getOutstanding($this->getUserId());
        }
        return $agreement;
    }

    /**
     * @return mixed
     */
    protected function getLogoutUrl(){
        /** @var  $config */
        $config = $this->serviceLocator->get('Config');
        return rtrim($config['slices']['sso']['base'], '/') . '/logout/';
    }

    protected function getToken() {
        return $this->getParamFromAny('token');
    }

    protected function getFacilityId() {
        return $this->getParamFromAny('facilityId');
    }

    protected function getGhostId() {
        return $this->getParamFromAny('ghostId');
    }

    protected function getAgreementFromRouteMatch()
    {
        return $this->getEvent()->getRouteMatch()->getParam(self::DISPLAY_AGREEMENT_PARAM_NAME, null);
    }

    /**
     * @return string|null
     */
    protected function getUserId()
    {
        return $this->getAuthorizationSession()->get('userId');
    }

    /**
     * @return string|null
     */
    protected function getAgreementIdFromRequest()
    {
        return $this->getParamsPlugin()->fromPost('agreementId');
    }

    /**
     * @return string|null
     */
    protected function getJobTitle()
    {
        return $this->getParamsPlugin()->fromPost('jobTitle');
    }

    /**
     * @param int $agreementId
     */
    protected function signAgreement($agreementId)
    {
        if (0 < $agreementId) {

            /** @var $agreementService AgreementService */
            $agreementService = $this->serviceLocator->get('EMRDelegator\Service\Agreement\Agreement');

            $signData = new SignData();
            $signData->setAgreementId($agreementId);
            $signData->setUserId($this->getUserId());
            $signData->setIpAddress($this->getRemoteIp());
            $signData->setJobTitle($this->getJobTitle());

            $agreementService->sign($signData);
        }
    }

    /**
     * @return string
     */
    protected function getRemoteIp()
    {
        /** @var \Zend\Http\PhpEnvironment\Request $request **/
        $request = $this->getEvent()->getRequest();
        return $request->getServer('REMOTE_ADDR');
    }

    protected function forwardToDelegation($token, $facilityId, $ghostId)
    {
        if($ghostId){
            return $this->getForwardPlugin()
                ->dispatch('Application\Controller\SuperUserDelegation',
                    array('action'=>'delegate','token' => $token,'facilityId' => $facilityId, 'ghostId'=>$ghostId));
        }
        return $this->getForwardPlugin()
            ->dispatch('Application\Controller\Delegation',
                array('action'=>'delegate','token' => $token,'facilityId' => $facilityId));
    }

    protected function getForwardPlugin()
    {
        return $this->getPluginManager()->get('forward');
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
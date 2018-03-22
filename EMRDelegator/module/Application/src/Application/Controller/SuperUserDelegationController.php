<?php
/**
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2013 WebPT, INC
 */
namespace Application\Controller;

use Application\Service\Delegation\Dto\Delegate;
use EMRCore\EsbFactory;
use EMRCore\Zend\Grant\RequiresSuperUserGrantDiInterface;
use EMRDelegator\Model\Agreement;
use Zend\Config\Config;
use Zend\Http\Request;
use Zend\Http\Response;

class SuperUserDelegationController extends DelegationController implements RequiresSuperUserGrantDiInterface
{

    /**
     * @return string
     */
    protected function getGhostId()
    {
        return $this->getParamFromAny('ghostId');
    }

    /**
     * Over rides the delegate action to delegate for the ghost id.
     * @return mixed
     */
    public function delegateAction()
    {

        // handle outstanding agreements
        $agreement = $this->getOutstandingAgreement(array('HIPAA'));
        if($agreement) {
            return $this->forwardToAgreements($agreement);
        }

        $delegateDto = new Delegate();
        $delegateDto->setUserId($this->getUserId());
        $delegateDto->setFacilityId($this->getFacilityId());
        $delegateDto->setGhostId($this->getGhostId());

        return $this->delegate($delegateDto);
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
            'facilityId' => $this->getFacilityId(),
            'token' => $this->getToken(),
            'ghostId' => $this->getGhostId()
        ));
    }

}
<?php
/**
 * @category WebPT 
 * @package EMRDelegator
 * @author: kevinkucera
 * 10/12/13 3:38 PM
 */

namespace Service\Controller;

use EMRCore\Zend\Grant\IpGrantDiInterface;
use EMRCore\Zend\Mvc\Controller\RestfulControllerAbstract;
use EMRCore\Zend\Module\Service\Response\Content;
use EMRDelegator\Service\Agreement\Agreement as AgreementService;
use EMRDelegator\Service\Agreement\Exception\AgreementNotFound;
use EMRDelegator\Service\Agreement\Marshal\AgreementToArray;

class AgreementController extends RestfulControllerAbstract implements IpGrantDiInterface
{

    /**
     * @return AgreementService
     */
    protected function getAgreementService()
    {
        return $this->getServiceLocator()->get('EMRDelegator\Service\Agreement\Agreement');
    }

    /**
     * @return AgreementToArray
     */
    protected function getAgreementToArrayMarshaller()
    {
        return $this->getServiceLocator()->get('EMRDelegator\Service\Agreement\Marshal\AgreementToArray');
    }

    /**
     * @param $agreement
     * @return array
     */
    protected function getMarshaledAgreement($agreement)
    {
        return $this->getAgreementToArrayMarshaller()->marshall($agreement);
    }

    /**
     * @param mixed $type
     * @return Content
     */
    public function get($type)
    {
        $agreementService = $this->getAgreementService();
        $agreement = $agreementService->getLatestAgreementByType($type);

        if(empty($agreement)){
            throw new AgreementNotFound('An agreement with type ['.$type.'] could not be found.');
        }

        return $this->getContentResponse(array(
            'agreement' => $this->getMarshaledAgreement($agreement),
        ));
    }

}
<?php
/**
 * @category WebPT 
 * @package EMRDelegator
 * @author: kevinkucera
 * 10/16/13 8:07 AM
 */

namespace Service\Controller;

use EMRCore\Zend\Grant\IpGrantDiInterface;
use EMRCore\Zend\Mvc\Controller\RestfulControllerAbstract;
use EMRCore\Zend\Module\Service\Response\Content;
use EMRDelegator\Service\UserHasAgreement\UserHasAgreement as UserHasAgreementService;
use EMRDelegator\Service\UserHasAgreement\Exception\UserHasAgreementNotFound;
use EMRDelegator\Service\UserHasAgreement\Marshal\UserHasAgreementToArray;
use Zend\Http\Request;

class UserHasAgreementController extends RestfulControllerAbstract implements IpGrantDiInterface
{

    /**
     * @return UserHasAgreementService
     */
    protected function getUserHasAgreementService()
    {
        return $this->getServiceLocator()->get('EMRDelegator\Service\UserHasAgreement\UserHasAgreement');
    }

    /**
     * @return UserHasAgreementToArray
     */
    protected function getUserHasAgreementToArrayMarshaller()
    {
        return $this->getServiceLocator()->get('EMRDelegator\Service\UserHasAgreement\Marshal\UserHasAgreementToArray');
    }

    /**
     * @param $userHasAgreement
     * @return mixed
     */
    protected function getMarshaledUserHasAgreement($userHasAgreement)
    {
        return $this->getUserHasAgreementToArrayMarshaller()->marshall($userHasAgreement);
    }

    /**
     * @return Content
     * @throws UserHasAgreementNotFound
     */
    public function getUserAgreementAction()
    {

        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();
        $query = $request->getQuery();

        if ($request->getMethod() === Request::METHOD_POST) {
            $userIdPost = $request->getPost('userId');
            $usersIds = json_decode($userIdPost);

            if (is_array($usersIds)) {
                return $this->getBulkAgreement($usersIds);
            }
        }

        $userId = $query->get('userId');
        $agreementId = $query->get('agreementId');

        $agreementService = $this->getUserHasAgreementService();
        $userHasAgreement = $agreementService->getUserHasAgreement($userId, $agreementId);

        if(empty($userHasAgreement)){
            throw new UserHasAgreementNotFound('User with id ['.$userId.'] and agreement id ['.$agreementId.'] not found.');
        }

        return $this->getContentResponse(array(
            'userHasAgreement' => $this->getMarshaledUserHasAgreement($userHasAgreement),
        ));
    }

    /**
     * Responded to request to get all user if have any accepted document like TOS (Terms of Service),
     * HIPAA (HIPAA Agreement), CPT (CPT Agreement), BAA (Business Associate Agreement).
     *
     * @param string[] $usersIds
     *
     * @return Content|null
     *
     * @throws UserHasAgreementNotFound
     */
    private function getBulkAgreement(array $usersIds)
    {
        if (empty($usersIds)) {
            throw new UserHasAgreementNotFound('User with empty ids not found.');
        }

        $agreementService = $this->getUserHasAgreementService();

        return $this->getContentResponse([
            'bulkAgreement' => $agreementService->getBulkUserHasAgreement($usersIds),
        ]);
    }
}
<?php

/**
 * Services for UserHasFacility data
 *
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2012 WebPT, INC
 */

namespace Service\Controller;

use EMRCore\Service\Auth\Token\Exception\Authentication as AuthenticationException;
use EMRCore\Service\Auth\Token\Token as TokenService;
use EMRCore\Zend\Grant\IpGrantDiInterface;
use EMRCore\Zend\Mvc\Controller\RestfulControllerAbstract;
use EMRCore\Zend\Module\Service\Response\Content;
use EMRDelegator\Service\Session\Exception\SessionRegistryNotFound;
use EMRDelegator\Service\Session\Registry;
use EMRDelegator\Service\UserHasFacility\Marshaller\SearchUserHasFacilityResultsToArray;
use EMRDelegator\Service\UserHasFacility\UserHasFacility as UserHasFacilityService;
use InvalidArgumentException;
use Service\Controller\Form\UserHasFacility\Create;
use Service\Controller\Form\UserHasFacility\GetByToken;
use Service\Controller\Form\UserHasFacility\GetList;
use Zend\Http\Request;
use EMRDelegator\Service\UserHasFacility\Dto\SearchUsersHasFacilityResults;
use EMRDelegator\Service\UserHasFacility\Marshaller\SearchUsersHasFacilityResultsToArray;

class UserHasFacilityController extends RestfulControllerAbstract implements IpGrantDiInterface
{

    /**
     * @return UserHasFacilityService
     */
    protected function getUserHasFacilityService()
    {
        return $this->serviceLocator->get('EMRDelegator\Service\UserHasFacility\UserHasFacility');
    }

    /**
     * @return Registry
     */
    protected function getSessionRegistryService()
    {
        return $this->serviceLocator->get('EMRDelegator\Service\Session\Registry');
    }

    /**
     * @param $identityId
     * @return Content
     */
    protected function getFacilityListResponse($identityId)
    {
        $results = $this->getUserHasFacilityService()->searchUserHasFacilityByIdentityId($identityId);
        $marshaller = new SearchUserHasFacilityResultsToArray();
        return $this->getContentResponse($marshaller->marshall($results));
    }

    /**
     * @return TokenService
     */
    protected function getTokenAuthorizationService()
    {
        return $this->serviceLocator->get('EMRCore\Service\Auth\Token\Token');
    }

    /**
     * @param mixed $rawData
     * @return Content|\EMRCore\Zend\Module\Service\Response\ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function create($rawData)
    {
        $data = $this->createPrepareFormData($rawData);

        // replace the user facility relationships
        $this->getUserHasFacilityService()->replaceUserFacilities(
                $data['identityId'], json_decode($data['facilityIds']), $data['defaultFacilityId']
        );

        return $this->getContentResponse(array('success' => true));
    }

    /**
     * @param $rawData
     * @return array
     * @throws \InvalidArgumentException
     */
    public function createPrepareFormData($rawData)
    {
        $form = new Create();
        $form->setData($rawData);

        if (!$form->isValid())
        {
            $messages = $this->getFormHelper()->getValidationMessagesAsString($form);
            if (!empty($messages))
            {
                throw new InvalidArgumentException($messages);
            }
        }

        // Get filtered data.
        return $form->getData();
    }

    /**
     * @throws InvalidArgumentException
     * @return Content
     */
    public function getList()
    {
        /** @var Request $request */
        $request = $this->getRequest();

        $rawData = array(
            'identityId' => $request->getQuery('identityId', 0),
        );

        $identityId = $this->prepareGetList($rawData);

        return $this->getFacilityListResponse($identityId);
    }

    /**
     * @param mixed[] $rawData
     * @throws InvalidArgumentException
     * @return int
     */
    public function prepareGetList(array $rawData = array())
    {
        $form = new GetList();
        $form->setData($rawData);

        if (!$form->isValid())
        {
            $messages = $this->getFormHelper()->getValidationMessagesAsString($form);
            if (!empty($messages))
            {
                throw new InvalidArgumentException($messages);
            }
        }

        $data = $form->getData();

        return $data['identityId'];
    }

    /**
     * @throws InvalidArgumentException
     * @return Content
     */
    public function getByTokenAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();

        // if this is a standard user logout then we only expect an wpt_sso_token
        // if this is a ghosted session then we expect a wpt_sso_token for the admin's session
        // plus the userid (identityId) of the user they are ghosted as
        $identityId = $request->getQuery('userId', 0);
        $rawData = array(
            'wpt_sso_token' => $request->getQuery('wpt_sso_token', 0),
        );

        $wptSsoToken = $this->prepareGetListByToken($rawData);

        try {
            if (empty($identityId))
            {
                $sessionRegistry = $this->getSessionRegistryService()->getBySsoToken($wptSsoToken);
                $identityId = $sessionRegistry->getIdentityId();
            } else
            {
                $this->validateTokenIsAdmin($wptSsoToken);
            }

            return $this->getFacilityListResponse($identityId);
        } catch (SessionRegistryNotFound $e) {
            $this->getLogger()->debug($e);
            return $this->getWarningResponse(
                array('error' => 'Identity was not found for token')
            );
        }
    }

    /**
     * @param $wptSsoToken
     * @throws \EMRCore\Service\Auth\Token\Exception\Authentication
     */
    protected function validateTokenIsAdmin($wptSsoToken)
    {
        $tokenService = $this->getTokenAuthorizationService();
        $authorizedUser = $tokenService->authorize($wptSsoToken);
        if (!$authorizedUser->isAdmin())
        {
            throw new AuthenticationException("Action restricted to users with access to Admin application");
        }
    }

    /**
     * @return GetByToken
     */
    protected function getByTokenZendForm()
    {
        return new GetByToken();
    }

    /**
     * @param mixed[] $rawData
     * @throws InvalidArgumentException
     * @return int
     */
    protected function prepareGetListByToken(array $rawData = array())
    {
        $form = $this->getByTokenZendForm();
        $form->setData($rawData);

        if (!$form->isValid())
        {
            $messages = $this->getFormHelper()->getValidationMessagesAsString($form);
            if (!empty($messages))
            {
                throw new InvalidArgumentException($messages);
            }
        }

        $data = $form->getData();

        return $data['wpt_sso_token'];
    }

    /**
     * @return Content
     */
    public function getDefaultCompanyByIdentityIdAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();

        $identityId = $request->getQuery('identityId');

        $company = $this->getUserHasFacilityService()->getIdentityDefaultCompany($identityId);

        return $this->getContentResponse(array(
                    'companyId' => $company->getCompanyId(),
        ));
    }

    /**
     * @return Content
     */
    public function getListGhostBrowseAction()
    {

        /** @var Request $request */
        $request = $this->getRequest();

        $identities = json_decode($request->getPost('identities'));

        $response = $this->getUserHasFacilityService()->searchUserHasFacilityByIdentities($identities);

        $marshaller = new SearchUsersHasFacilityResultsToArray;
        return $this->getContentResponse($marshaller->marshall($response));
    }

}

<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 10/30/13 12:30 PM
 */

namespace Application\Controller;


use EMRCore\Session\Instance\Authorization;
use EMRCore\Session\Instance\RequireAuthorizationSessionDiInterface;
use EMRCore\Zend\Mvc\Controller\ActionControllerAbstract;

class ProtectedByAuthorizationSessionActionControllerAbstract extends ActionControllerAbstract
    implements RequireAuthorizationSessionDiInterface {

    /**
     * @var Authorization
     */
    protected $authorizationSession;

    /**
     * @return int
     */
    protected function getSessionUserId() {
        return $this->authorizationSession->get('userId');
    }

    /**
     * @param Authorization $session
     * @return mixed
     */
    public function setAuthorizationSession(Authorization $session)
    {
        $this->authorizationSession = $session;
    }

    /**
     * @return Authorization
     */
    public function getAuthorizationSession() {
        return $this->authorizationSession;
    }
}
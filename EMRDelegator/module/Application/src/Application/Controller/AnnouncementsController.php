<?php
/**
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2012 WebPT, INC
 */
namespace Application\Controller;

use EMRDelegator\Service\Announcement\Announcement;
use EMRDelegator\Service\IdentityAnnouncements\IdentityAnnouncements;
use Zend\View\Model\ViewModel;

class AnnouncementsController extends ProtectedByAuthorizationSessionActionControllerAbstract
{
    const DISPLAY_ANNOUNCEMENTS_PARAM_NAME = 'announcements';

    /**
     * We do not track acknowledgement.
     * Announcements will be displayed until the last login time is updated, so we need to start the session here
     * before redirecting back to auth.
     * @return bool
     */
    public function acknowledgedAction()
    {
        $this->acknowledgeAnnouncements();
        // get token & facility from form
        $token = $this->getTokenFromRequest();
        $facility = $this->getFacilityFromRequest();
        return $this->forwardToDelegation($token, $facility);
    }

    public function displayAction()
    {
        $announcements = $this->getAnnouncements();

        $viewModel = new ViewModel();
        $viewModel->setTemplate('application/announcements/announcements.phtml');
        $viewModel->setVariable('announcements', $announcements);
        $viewModel->setVariable('token', $this->getTokenFromRouteMatch());
        $viewModel->setVariable('facility', $this->getFacilityFromRouteMatch());
        return $viewModel;
    }

    /**
     * Get the current user's id from the authorization session.
     *
     * @return int
     */
    protected function getUserId()
    {
        return (int)$this->getAuthorizationSession()->get('userId');
    }

    /**
     * @return array
     */
    protected function getAnnouncements()
    {
        $announcements = $this->getAnnouncementsFromRouteMatch();

        if (empty($announcements)) {
            $announcements = $this->getAnnouncementsFromService();
        }
        return $announcements;
    }

    /**
     * Forward to delegate controller/action
     * @param $token string
     * @param $facilityId int
     * @return mixed
     */
    protected function forwardToDelegation($token, $facilityId)
    {
        return $this->getForwardPlugin()
            ->dispatch('Application\Controller\Delegation',
                array('action'=>'delegate','token' => $token,'facilityId' => $facilityId));
    }


    /**
     * @return int
     */
    protected function getFacilityFromRouteMatch()
    {
        return $this->getEvent()->getRouteMatch()->getParam('facilityId');
    }

    /**
     * @return int
     */
    protected function getFacilityFromRequest() {
        return $this->getEvent()->getParam('facility');
    }

    protected function getForwardPlugin()
    {
        return $this->getPluginManager()->get('forward');
    }

    /**
     * @return string
     */
    protected function getTokenFromRouteMatch() {
        return $this->getEvent()->getRouteMatch()->getParam('token');
    }

    /**
     * @return string
     */
    protected function getTokenFromRequest() {
        return $this->getEvent()->getParam('token');
    }

    protected function acknowledgeAnnouncements()
    {
        /** @var IdentityAnnouncements $announcement */
        $identityDelegation = $this->serviceLocator->get('EMRDelegator\Service\IdentityAnnouncements\IdentityAnnouncements');
        $identityDelegation->addAcknowledgement($this->getUserId());
    }

    /**
     * @return mixed
     */
    protected function getAnnouncementsFromRouteMatch()
    {
        return $this->getEvent()->getRouteMatch()->getParam(self::DISPLAY_ANNOUNCEMENTS_PARAM_NAME);
    }

    /**
     * @return \EMRDelegator\Model\Announcement[]
     */
    protected function getAnnouncementsFromService()
    {
        /** @var $announcementService Announcement */
        $announcementService = $this->serviceLocator->get('EMRDelegator\Service\Announcement\Announcement');
        return $announcementService->getOutstandingAnnouncements($this->getUserId());
    }

}

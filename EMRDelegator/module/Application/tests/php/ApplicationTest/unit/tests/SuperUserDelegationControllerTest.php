<?php

namespace ApplicationTest\unit\tests;
use Application\Controller\SuperUserDelegationController;
use Application\Service\Delegation\Dto\Delegate as DelegateDto;
use EMRDelegator\Model\Agreement;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Mvc\Router\SimpleRouteStack;

class SuperUserDelegationControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \Zend\Http\PhpEnvironment\Request
     */
    private $request;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $redirectMock;

    /**
     * @var \Zend\Mvc\Router\SimpleRouteStack
     */
    private $router;

    /**
     * @var \Zend\Mvc\Router\RouteMatch
     */
    private $routeMatch;

    /**
     * @var \Zend\Mvc\MvcEvent
     */
    private $mvcEvent;

    public function setUp() {
        $this->request = new Request();
        $this->router = new SimpleRouteStack();
        $this->routeMatch = new RouteMatch(array('controller' => 'superuserdelegation' , 'action' => 'delegation'));
        $this->mvcEvent = new MvcEvent();
        $this->mvcEvent->setRouter($this->router);
        $this->mvcEvent->setRouteMatch($this->routeMatch);
        $this->mvcEvent->setRequest($this->request);
    }

    public function testSuperUserDelegationActionCallsParentDelegateWithDto(){
        $userId = 3;
        $ghostId = 7;
        $facilityId = 11;

        $delegateDto = new DelegateDto();
        $delegateDto->setUserId($userId);
        $delegateDto->setFacilityId($facilityId);
        $delegateDto->setGhostId($ghostId);

        $superUserDelegationControllerMock = $this->getMock('Application\Controller\SuperUserDelegationController',array(
            'getOutstandingAgreement',
            'delegate'
        ));
        $superUserDelegationControllerMock->expects($this->once())
            ->method('getOutstandingAgreement')
            ->withAnyParameters()
            ->will($this->returnValue(null));
        $superUserDelegationControllerMock->expects($this->once())
              ->method('delegate')
              ->with($delegateDto)
              ->will($this->returnValue(true));

        $authMock = $this->getMock('EMRCore\Session\Instance\Authorization');
        $authMock->expects($this->once())
            ->method('get')
            ->with('userId')
            ->will($this->returnValue($userId));

        $this->mvcEvent->getRouteMatch()->setParam('ghostId',$ghostId);
        $this->mvcEvent->getRouteMatch()->setParam('facilityId',$facilityId);
        /** @var $superUserDelegationControllerMock SuperUserDelegationController */
        $superUserDelegationControllerMock->setEvent($this->mvcEvent);
        $superUserDelegationControllerMock->setAuthorizationSession($authMock);
        $result = $superUserDelegationControllerMock->delegateAction();
        $this->assertTrue($result);

    }

    public function testSuperUserDelegationActionForwardsToAgreement(){
        $agreement = new Agreement();
        $expected = 'OK';

        $superUserDelegationControllerMock = $this->getMock('Application\Controller\SuperUserDelegationController',array(
            'getOutstandingAgreement',
            'forwardToAgreements'
        ));
        $superUserDelegationControllerMock->expects($this->once())
            ->method('getOutstandingAgreement')
            ->withAnyParameters()
            ->will($this->returnValue($agreement));
        $superUserDelegationControllerMock->expects($this->once())
            ->method('forwardToAgreements')
            ->with($agreement)
            ->will($this->returnValue($expected));

        /** @var $superUserDelegationControllerMock SuperUserDelegationController */
        $result = $superUserDelegationControllerMock->delegateAction();
        $this->assertEquals($expected,$result);

    }
}




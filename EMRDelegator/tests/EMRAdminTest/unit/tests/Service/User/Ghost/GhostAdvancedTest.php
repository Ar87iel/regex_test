<?php

namespace EMRAdminTest\unit\tests\Service\User\Ghost;

use EMRAdmin\Service\User\Ghost\GhostAdvanced;
use EMRAdmin\Service\User\Ghost\Dto\Ghost;
use PHPUnit_Framework_TestCase;
use EMRCore\Session\SessionInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use EMRCore\PrototypeFactory;

/**
 * Class GhostTest - for Ghost Business Service
 * @package EMRAdminTest\unit\tests\Service\User\Ghost
 */
class GhostAdvancedTest extends PHPUnit_Framework_TestCase {
    /**
     * @var ServiceLocatorInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $serviceLocatorMock;

    /**
     * @var SessionInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $applicationSessionMock;

    /**
     * @var Ghost
     */
    private $ghost;

    function setUp()
    {
        $this->applicationSessionMock = $this->getMock('EMRCore\Session\SessionInterface');
        $this->serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        /** @var Ghost ghost */
        $this->ghost = new GhostAdvanced();
        $this->ghost->setapplicationSession($this->applicationSessionMock);
        $this->ghost->setServiceLocator($this->serviceLocatorMock);

        self::assertSame($this->serviceLocatorMock, $this->ghost->getServiceLocator());
        self::assertSame($this->applicationSessionMock, $this->ghost->getApplcationSession());
    }

    function testGetGhostData()
    {
        $baseURL = '';
        $facilityId = 1;
        $ghostId = 5;
        $ssoToken = 'foo';

        $config = array(
            'ghost' => array(
                'default' => array(
                    'facilityId' => $facilityId,
                    'userId' => $ghostId
                ),
            ),
            'slices' => array(
                'delegator' => array(
                    'base' => $baseURL,
                    'logout' => '' //uri to ESB route to initiate cluster logout
                )
            )
        );

        $this->applicationSessionMock->expects(self::once())
            ->method('get')
            ->with('authSessionId')
            ->will(self::returnValue($ssoToken));
        $this->serviceLocatorMock->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));

        $ghostURL = $baseURL . "/ghost-browse/check-in?ghostAsIdentityId=".$ghostId."&facilityId=".$facilityId;
        $ghostDto = $this->ghost->getGhostData();

        self::assertInstanceOf(Ghost::class, $ghostDto);
        self::assertEquals($ghostDto->getGhostLink(), $ghostURL);
        self::assertEquals($ghostDto->getGhostId(), $ghostId);
    }

}

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jkozel
 * Date: 9/19/13
 * Time: 2:47 PM
 * To change this template use File | Settings | File Templates.
 */

namespace EMRAdminTest\unit\tests\Service\User\Ghost;
use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\User\Ghost\Dto\Ghost as GhostDto;
use EMRAdmin\Service\User\Ghost\Ghost;
use EMRCore\Session\SessionInterface;
use Zend\Config\Config;
use Zend\ServiceManager\ServiceLocatorInterface;
use EMRCore\PrototypeFactory;

/**
 * Class GhostTest - for Ghost Business Service
 * @package EMRAdminTest\unit\tests\Service\User\Ghost
 */
class GhostTest extends PHPUnit_Framework_TestCase {

    /**
     * @var PrototypeFactory
     */
    private $prototypeFactory;

    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * @var SessionInterface
     */
    private $applicationSession;

    /**
     * @var Ghost
     */
    private $ghost;

    function setUp()
    {
        $this->applicationSession = $this->createMock('EMRCore\Session\SessionInterface');
        $this->serviceLocator = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');

        /** @var Ghost ghost */
        $this->ghost = $this->getMock('EMRAdmin\Service\User\Ghost\Ghost', array('getGhostLink','getGhostDto'));
        $this->ghost->setApplicationSession($this->applicationSession);
        $this->ghost->setServiceLocator($this->serviceLocator);
    }

    function testGetGhostData()
    {
        $baseURL = 'https://delegator.emr.localhost';
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
        $this->serviceLocator->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));

        $this->ghost->getApplcationSession()->expects($this->once())
            ->method('get')
            ->with('authSessionId')
            ->will($this->returnValue($ssoToken));

        $ghostURL = $baseURL . "/authorization/?ghostId=".$ghostId."&facilityId=".$facilityId."&wpt_sso_token=".$ssoToken;

        $ghostDto = $this->createMock('EMRAdmin\Service\User\Ghost\Dto\Ghost');

        $this->ghost->expects($this->once())
            ->method('getGhostLink')
            ->with($facilityId, $ghostId, $ssoToken, $baseURL)
            ->will($this->returnValue($ghostURL));
        $this->ghost->expects($this->once())
            ->method('getGhostDto')
            ->will($this->returnValue($ghostDto));


        $ghostDto->expects($this->once())
            ->method('setGhostLink')
            ->with($ghostURL);

        $ghostDto->expects($this->once())
            ->method('setGhostId')
            ->with($ghostId);

        $this->ghost->getGhostData();
    }

}

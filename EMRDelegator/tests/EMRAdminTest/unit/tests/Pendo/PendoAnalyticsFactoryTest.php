<?php
/**
 * @category WebPT
 * @package EMRAdmin
 * @copyright Copyright (c) 2016 WebPT, INC
 * @author Tim Bradley (timothy.bradley@webpt.com)
 */
namespace EMRAdminTest\Pendo;

use EMRAdmin\Pendo\PendoAnalyticsFactory;
use EMRCore\Session\SessionInterface;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Wpt\FeatureFlip\FeatureFlipInterface;
use Zend\ServiceManager\ServiceManager;

class PendoAnalyticsFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var PendoAnalyticsFactory */
    private $sut;

    public function setUp()
    {
        $this->sut = new PendoAnalyticsFactory;
    }

    /**
     * @expectedException \EMRAdmin\Pendo\MissingConfigurationException
     * @expectedExceptionMessage Required configuration missing: pendo/default_pendo_options
     */
    public function testCreateServiceWithFeatureFlipOnAndBadConfigAndSessionPresentThrowsException()
    {
        $userId = mt_rand(500,700);

        /** @var FeatureFlipInterface | PHPUnit_Framework_MockObject_MockObject  $featureFlip  */
        $featureFlip = $this->createMock('\Wpt\FeatureFlip\FeatureFlipInterface');
        $featureFlip->expects(self::once())->method('enabled')
                    ->with('pendo-snippet')
                    ->will(self::returnValue(true));

        /** @var SessionInterface | PHPUnit_Framework_MockObject_MockObject $session */
        $session = $this->getMock('\EMRCore\Session\SessionInterface', array(), array(), '', false);
        $session->expects(self::any())->method('get')
                ->willReturnMap(
                    array(
                        array('userId', $userId),
                    )
                );

        $sm = new ServiceManager();
        $sm->setService('\EMRCore\Session\Instance\Application', $session);
        $sm->setService('Wpt\FeatureFlip', $featureFlip);
        $sm->setService('config', array(
            'pendo' => array()
        ));

        $this->sut->createService($sm);
    }

    public function testCreateServiceWithFeatureFlipOnAndSessionPresent()
    {
        $userId = mt_rand(500,700);

        /** @var FeatureFlipInterface | PHPUnit_Framework_MockObject_MockObject  $featureFlip  */
        $featureFlip = $this->createMock('\Wpt\FeatureFlip\FeatureFlipInterface');
        $featureFlip->expects($this->any())->method('enabled')->will(self::returnValueMap([
            ['pendo-snippet', true],
            ['pendo-agent-2', false],
        ]));

        /** @var SessionInterface | PHPUnit_Framework_MockObject_MockObject $session */
        $session = $this->getMock('\EMRCore\Session\SessionInterface', array(), array(), '', false);
        $session->expects(self::any())->method('get')
                ->willReturnMap(
                    array(
                        array('userId', $userId),
                    )
                );

        $sm = new ServiceManager();
        $sm->setService('EMRCore\Session\Instance\Application', $session);
        $sm->setService('Wpt\FeatureFlip', $featureFlip);
        $sm->setService('config', array(
            'pendo' => array(
                'default_pendo_options' => array(
                    'apiKey' => 'SomeApiKey',
                )
            )
        ));

        self::assertInstanceOf('\WebPT\Pendo\PendoAnalytics', $this->sut->createService($sm));
    }

    public function testCreateServiceWithFeatureFlipOff()
    {
        /** @var FeatureFlipInterface | PHPUnit_Framework_MockObject_MockObject  $featureFlip  */
        $featureFlip = $this->createMock('\Wpt\FeatureFlip\FeatureFlipInterface');
        $featureFlip->expects(self::once())->method('enabled')
                    ->with('pendo-snippet')
                    ->will(self::returnValue(false));

        $sm = new ServiceManager();
        $sm->setService('Wpt\FeatureFlip', $featureFlip);

        self::assertInstanceOf('\WebPT\Pendo\DummyPendoAnalytics', $this->sut->createService($sm));
    }

    /**
     * @expectedException \EMRAdmin\Pendo\MissingConfigurationException
     * @expectedExceptionMessage Required configuration missing: pendo/default_pendo_options
     */
    public function testCreateServiceWithFeatureFlipOnAndBadConfigAndSessionMissingThrowsException()
    {
        /** @var FeatureFlipInterface | PHPUnit_Framework_MockObject_MockObject  $featureFlip  */
        $featureFlip = $this->createMock('\Wpt\FeatureFlip\FeatureFlipInterface');
        $featureFlip->expects(self::once())->method('enabled')
                    ->with('pendo-snippet')
                    ->will(self::returnValue(true));

        $sm = new ServiceManager();
        $sm->setService('Wpt\FeatureFlip', $featureFlip);
        $sm->setService('config', array(
            'pendo' => array()
        ));

        $this->sut->createService($sm);
    }

    public function testCreateServiceWithFeatureFlipOnAndSessionMissing()
    {
        /** @var FeatureFlipInterface | PHPUnit_Framework_MockObject_MockObject  $featureFlip  */
        $featureFlip = $this->createMock('\Wpt\FeatureFlip\FeatureFlipInterface');
        $featureFlip->expects($this->any())->method('enabled')->will(self::returnValueMap([
            ['pendo-snippet', true],
            ['pendo-agent-2', false],
        ]));

        $sm = new ServiceManager();
        $sm->setService('Wpt\FeatureFlip', $featureFlip);
        $sm->setService('config', array(
            'pendo' => array(
                'default_pendo_options' => array(
                    'apiKey' => 'SomeApiKey',
                )
            )
        ));

        self::assertInstanceOf('\WebPT\Pendo\PendoAnalytics', $this->sut->createService($sm));
    }

    public function testCreateServiceWithFeatureFlipOnAndAgentV2Disabled()
    {
        /** @var FeatureFlipInterface | PHPUnit_Framework_MockObject_MockObject  $featureFlip  */
        $featureFlip = $this->createMock('\Wpt\FeatureFlip\FeatureFlipInterface');
        $featureFlip->expects($this->any())->method('enabled')->will(self::returnValueMap([
            ['pendo-snippet', true],
            ['pendo-agent-2', false],
        ]));

        $sm = new ServiceManager();
        $sm->setService('Wpt\FeatureFlip', $featureFlip);
        $sm->setService('config', array(
            'pendo' => array(
                'default_pendo_options' => array(
                    'apiKey' => 'SomeApiKey',
                ),
            )
        ));

        self::assertInstanceOf('\WebPT\Pendo\PendoAnalytics', $this->sut->createService($sm));

    }
    public function testCreateServiceWithFeatureFlipOnAgentV2Enabled()
    {
        /** @var FeatureFlipInterface | PHPUnit_Framework_MockObject_MockObject  $featureFlip  */
        $featureFlip = $this->createMock('\Wpt\FeatureFlip\FeatureFlipInterface');
        $featureFlip->expects($this->any())->method('enabled')->will(self::returnValueMap([
            ['pendo-snippet', true],
            ['pendo-agent-2', true],
        ]));

        $sm = new ServiceManager();
        $sm->setService('Wpt\FeatureFlip', $featureFlip);
        $sm->setService('config', array(
            'pendo' => array(
                'default_pendo_options' => array(
                    'apiKey' => 'SomeApiKey',
                )
            )
        ));

        self::assertInstanceOf('\WebPT\Pendo\PendoAnalytics2', $this->sut->createService($sm));

    }
}

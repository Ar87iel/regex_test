<?php
/**
 * @category WebPT
 * @package EMRAdmin
 * @copyright Copyright (c) 2016 WebPT, INC
 * @author Tim Bradley (timothy.bradley@webpt.com)
 */
namespace EMRAdminTest\Pendo;

use EMRAdmin\Pendo\PendoListenerFactory;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use WebPT\Pendo\PendoAnalyticsInterface;
use Zend\ServiceManager\ServiceManager;

class PendoListenerFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var PendoListenerFactory */
    private $sut;

    public function setUp()
    {
        $this->sut = new PendoListenerFactory;
    }

    public function testCreateService()
    {
        /** @var PendoAnalyticsInterface | PHPUnit_Framework_MockObject_MockObject $pendo */
        $pendo = $this->getMock('\WebPT\Pendo\PendoAnalyticsInterface');

        $sm = new ServiceManager();
        $sm->setService('WebPT\Pendo\PendoAnalyticsInterface', $pendo);

        self::assertInstanceOf('\EMRAdmin\Pendo\PendoListener', $this->sut->createService($sm));
    }
}

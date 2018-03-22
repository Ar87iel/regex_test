<?php

namespace EMRAdminTest\FeatureFlip;

use EMRAdmin\FeatureFlip\SimpleArrayFeatureFlipFactory;
use Zend\ServiceManager\ServiceManager;

class SimpleArrayFeatureFlipFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ServiceManager */
    private $serviceLocator;
    
    /** @var SimpleArrayFeatureFlipFactory */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->serviceLocator = new ServiceManager();
        
        $this->sut = new SimpleArrayFeatureFlipFactory();
    }
    
    public function testCreateService()
    {
        $this->serviceLocator->setService('config', array());
        
        $actual = $this->sut->createService($this->serviceLocator);
        
        self::assertInstanceOf('\Wpt\FeatureFlip\SimpleArrayFeatureFlip', $actual);
    }
}

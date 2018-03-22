<?php

namespace EMRAdminTest\ServiceManager\ServiceRegistry;

use EMRAdmin\ServiceManager\ServiceRegistry;
use Zend\ServiceManager\ServiceManager;

class ServiceRegistryTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ServiceManager */
    private $serviceLocator;

    protected function setUp()
    {
        parent::setUp();

        $this->serviceLocator = new ServiceManager();
    }
    
    public function testGetConfig()
    {
        $this->serviceLocator->setService('config', array());
        
        $actual = ServiceRegistry::getConfig($this->serviceLocator);
        
        self::assertInternalType('array', $actual);
    }

    /**
     * @expectedException \EMRAdmin\Exception\UnexpectedTypeException
     */
    public function testNotGetConfigDueToInvalidServiceName()
    {
        self::assertFalse($this->serviceLocator->has('config'));
        
        ServiceRegistry::getConfig($this->serviceLocator);
    }
    
    /**
     * @expectedException \EMRAdmin\Exception\UnexpectedTypeException
     */
    public function testNotGetConfigDueToInvalidServiceType()
    {
        $this->serviceLocator->setService('config', new \stdClass());

        ServiceRegistry::getConfig($this->serviceLocator);
    }
    
    public function testGetFacilityModuleService()
    {
        $this->serviceLocator->setService(
            'EMRAdmin\Service\Facility\Module\Modules',
            $this->createMock('\EMRAdmin\Service\Facility\Module\ModulesInterface')
        );
        
        $actual = ServiceRegistry::getFacilityModuleService($this->serviceLocator);
        
        self::assertInstanceOf('\EMRAdmin\Service\Facility\Module\ModulesInterface', $actual);
    }

    /**
     * @expectedException \EMRAdmin\Exception\ExpectedClassException
     */
    public function testNotGetFacilityModuleServiceDueToInvalidServiceName()
    {
        self::assertFalse($this->serviceLocator->has('EMRAdmin\Service\Facility\Module\Modules'));
        
        ServiceRegistry::getFacilityModuleService($this->serviceLocator);
    }
    
    /**
     * @expectedException \EMRAdmin\Exception\ExpectedClassException
     */
    public function testNotGetFacilityModuleServiceDueToInvalidServiceType()
    {
        $this->serviceLocator->setService(
            'EMRAdmin\Service\Facility\Module\Modules',
            new \stdClass()
        );

        ServiceRegistry::getFacilityModuleService($this->serviceLocator);
    }
    
    public function testGetBillingFeedService()
    {
        $this->serviceLocator->setService(
            'EMRAdmin\Service\BillingFeed\BillingFeed',
            $this->createMock('\EMRAdmin\Service\BillingFeed\BillingFeedInterface')
        );
        
        $actual = ServiceRegistry::getBillingFeedService($this->serviceLocator);
        
        self::assertInstanceOf('\EMRAdmin\Service\BillingFeed\BillingFeedInterface', $actual);
    }

    /**
     * @expectedException \EMRAdmin\Exception\ExpectedClassException
     */
    public function testNotGetBillingFeedServiceDueToInvalidServiceName()
    {
        self::assertFalse($this->serviceLocator->has('EMRAdmin\Service\BillingFeed\BillingFeed'));
        
        ServiceRegistry::getBillingFeedService($this->serviceLocator);
    }
    
    /**
     * @expectedException \EMRAdmin\Exception\ExpectedClassException
     */
    public function testNotGetBillingFeedServiceDueToInvalidServiceType()
    {
        $this->serviceLocator->setService(
            'EMRAdmin\Service\BillingFeed\BillingFeed',
            new \stdClass()
        );

        ServiceRegistry::getBillingFeedService($this->serviceLocator);
    }
}

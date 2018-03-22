<?php

namespace EMRAdminTest\Unit;

use WebPT\EMR\Csrf\Controller\Plugin\CsrfPlugin;
use WebPT\EMR\Csrf\Service;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManager;
use Zend\Mvc\Application;
use Zend\Server\Reflection\ReflectionClass;
use Zend\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockBuilder;
use Service\Module;
use ReflectionProperty;
use EMRCore\Collection\ObjectCollection;
use EMRCore\Zend\Mvc\Controller\Validator\Validation;
use Zend\Mvc\Controller\AbstractController;
use Logger;

/**
 * Class ModuleTest
 * Testing class for Service\Module.php class where we listen to all requests made on the application
 */
class ServiceModuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Module
     */
    private $module;

    /**
     * @var MvcEvent | PHPUnit_Framework_MockObject_MockBuilder
     */
    private $mvcEvent;

    /**
     * @var CsrfPlugin | PHPUnit_Framework_MockObject_MockBuilder
     */
    private $csrfPlugin;

    /**
     * @var Service\CsrfService | PHPUnit_Framework_MockObject_MockBuilder
     */
    private $stubCsrfManager;

    /**
     * Test for onDispatch method without firing exception
     */
    public function testOnDispatch()
    {
        $this->csrfPlugin->expects($spy = $this->any())
            ->method('tokenListener')
            ->will($this->returnValue(true));

        $this->module->onDispatch($this->mvcEvent);

        $invocations = $spy->getInvocations();
        $this->atLeastOnce(count($invocations));
    }

    /**
     * Set up local environment to test the Application module
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->module = new Module;

        $this->stubCsrfManager = $this->getMockBuilder('\WebPT\EMR\Csrf\CsrfManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->csrfPlugin = $this->getMockBuilder('\WebPT\EMR\Csrf\Controller\Plugin\CsrfPluginInterface')
            ->getMock();
        $this->csrfPlugin->expects($this->any())
            ->method('startEngine')
            ->will($this->returnValue(true));

        $eventManager = $this->getMockBuilder('\Zend\EventManager\EventManagerInterface')
            ->getMock();
        $eventManager->expects($this->any())
            ->method('attach')
            ->will($this->returnValue(true));

        $objectCollection = $this->getMockBuilder('\EMRCore\Collection\ObjectCollection')
            ->disableOriginalConstructor()
            ->getMock();

        $validation = $this->getMockBuilder('\EMRCore\Zend\Mvc\Controller\Validator\ValidationInterface')
            ->getMock();
        $validation->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(true));
        $validation->expects($this->any())
            ->method('getExceptions')
            ->will($this->returnValue($objectCollection));

        $serviceManager = $this->getMockBuilder('\Zend\ServiceManager\ServiceLocatorInterface')
            ->getMock();
        $serviceManager->expects($this->at(0))
            ->method('get')
            ->will($this->returnValue($this->csrfPlugin));
        $serviceManager->expects($this->at(1))
            ->method('get')
            ->will($this->returnValue($validation));

        $zendApplication = $this->getMockBuilder('\Zend\Mvc\ApplicationInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $zendApplication->expects($this->any())
            ->method('getServiceManager')
            ->will($this->returnValue($serviceManager));
        $zendApplication->expects($this->any())
            ->method('getEventManager')
            ->will($this->returnValue($eventManager));

        $abstractController = $this->getMockBuilder('\Zend\Mvc\Controller\AbstractController')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mvcEvent = $this->getMockBuilder('\Zend\Mvc\MvcEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mvcEvent->expects($this->any())
            ->method('getApplication')
            ->will($this->returnValue($zendApplication));
        $this->mvcEvent->expects($this->any())
            ->method('isMocked')
            ->will($this->returnValue(true));
        $this->mvcEvent->expects($this->any())
            ->method('getTarget')
            ->will($this->returnValue($abstractController));

        $logger = $this->getMockBuilder('Logger')
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects($this->any())
            ->method('info')
            ->will($this->returnValue(true));

        /**
         * This reflection process will replace the original logger class into EventAbstract class
         */
        $reflection = new ReflectionProperty('\EMRCore\Zend\Mvc\Module\EventAbstract', 'logger');
        $reflection->setAccessible(true);
        $reflection->setValue($this->module, $logger);
    }

    /**
     * Tear down local variables
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();

        unset(
            $this->module,
            $this->mvcEvent,
            $this->stubCsrfManager,
            $this->csrfPlugin
        );
    }
}

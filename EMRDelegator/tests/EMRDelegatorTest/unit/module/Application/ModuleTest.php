<?php

namespace EMRDelegatorTest\Unit;

use Application\Module;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockBuilder;
use ReflectionProperty;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;

/**
 * Class ModuleTest
 */
class ModuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Module
     */
    private $module;

    /**
     * @var CsrfPlugin | PHPUnit_Framework_MockObject_MockBuilder
     */
    private $csrfPlugin;

    /**
     * @var EventManager | PHPUnit_Framework_MockObject_MockBuilder
     */
    private $eventManager;

    /**
     * @var ObjectCollection | PHPUnit_Framework_MockObject_MockBuilder
     */
    private $objectCollection;

    /**
     * @var Validation | PHPUnit_Framework_MockObject_MockBuilder
     */
    private $validation;

    /**
     * @var ServiceManager | PHPUnit_Framework_MockObject_MockBuilder
     */
    private $serviceManager;

    /**
     * @var Application | PHPUnit_Framework_MockObject_MockBuilder
     */
    private $zendApplication;

    /**
     * @var AbstractController | PHPUnit_Framework_MockObject_MockBuilder
     */
    private $abstractController;

    /**
     * @var MvcEvent | PHPUnit_Framework_MockObject_MockBuilder
     */
    private $mvcEvent;

    /**
     * @var Logger | PHPUnit_Framework_MockObject_MockBuilder
     */
    private $logger;

    /**
     * @var ReflectionProperty
     */
    private $reflection;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->csrfPlugin = $this->getMockBuilder('\WebPT\EMR\Csrf\Controller\Plugin\CsrfPluginInterface')
            ->getMock();
        $this->csrfPlugin->expects($this->any())
            ->method('startEngine')
            ->will($this->returnValue(true));
        $this->csrfPlugin->expects($this->any())
            ->method('getLogMessage')
            ->will($this->returnValue('Exception thrown for testing purposes'));

        $this->eventManager = $this->getMockBuilder('\Zend\EventManager\EventManagerInterface')
            ->getMock();
        $this->eventManager->expects($this->any())
            ->method('attach')
            ->will($this->returnValue(true));

        $this->objectCollection = $this->getMockBuilder('\EMRCore\Collection\ObjectCollection')
            ->disableOriginalConstructor()
            ->getMock();

        $this->validation = $this->getMockBuilder('\EMRCore\Zend\Mvc\Controller\Validator\ValidationInterface')
            ->getMock();
        $this->validation->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(true));
        $this->validation->expects($this->any())
            ->method('getExceptions')
            ->will($this->returnValue($this->objectCollection));

        $this->serviceManager = $this->getMockBuilder('\Zend\ServiceManager\ServiceLocatorInterface')
            ->getMock();
        $this->serviceManager->expects($this->at(0))
            ->method('get')
            ->will($this->returnValue($this->csrfPlugin));
        $this->serviceManager->expects($this->at(1))
            ->method('get')
            ->will($this->returnValue($this->validation));

        $this->zendApplication = $this->getMockBuilder('\Zend\Mvc\ApplicationInterface')
            ->getMock();
        $this->zendApplication->expects($this->any())
            ->method('getServiceManager')
            ->will($this->returnValue($this->serviceManager));
        $this->zendApplication->expects($this->any())
            ->method('getEventManager')
            ->will($this->returnValue($this->eventManager));

        $this->abstractController = $this->getMockBuilder('\Zend\Mvc\Controller\AbstractController')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mvcEvent = $this->getMockBuilder('\Zend\Mvc\MvcEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mvcEvent->expects($this->any())
            ->method('getApplication')
            ->will($this->returnValue($this->zendApplication));
        $this->mvcEvent->expects($this->any())
            ->method('getTarget')
            ->will($this->returnValue($this->abstractController));

        $this->logger = $this->getMockBuilder('Logger')
            ->disableOriginalConstructor()
            ->getMock();
        $this->logger->expects($this->any())
            ->method('info')
            ->will($this->returnValue(true));

        $this->module = new Module;

        /**
         * This reflection process will replace the original logger class into EventAbstract class.
         */
        $this->reflection = new ReflectionProperty('\EMRCore\Zend\Mvc\Module\EventAbstract', 'logger');
        $this->reflection->setAccessible(true);
        $this->reflection->setValue($this->module, $this->logger);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();

        unset($this->module,
            $this->reflection,
            $this->csrfPlugin,
            $this->eventManager,
            $this->objectCollection,
            $this->validation,
            $this->serviceManager,
            $this->zendApplication,
            $this->abstractController,
            $this->mvcEvent,
            $this->logger);
    }

    /**
     * Test for onDispatch method without firing exception
     */
    public function testOnDispatchEventWithoutException()
    {
        $this->csrfPlugin->expects($spy = $this->any())
            ->method('tokenListener')
            ->will($this->returnValue(true));

        $this->module->onDispatch($this->mvcEvent);

        $invocations = $spy->getInvocations();
        $this->assertEquals(1, count($invocations), "tokenListener method has been invoked one time.");
    }

}

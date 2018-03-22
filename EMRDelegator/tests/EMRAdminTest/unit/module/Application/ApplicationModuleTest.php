<?php
namespace EMRAdminTest\Unit;

use EMRCore\Http\HeaderService;
use PHPUnit_Framework_MockObject_MockObject;
use WebPT\EMR\Csrf\Controller\Plugin\CsrfPlugin;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\ApplicationInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;
use ReflectionProperty;
use Application\Module;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockBuilder;
use Zend\View\View;

/**
 * Class ModuleTest
 * Testing class for Application\Module.php class where we listen to all requests made on the application
 */
class ApplicationModuleTest extends PHPUnit_Framework_TestCase
{
    /** @var  ServiceLocatorInterface | PHPUnit_Framework_MockObject_MockObject */
    private $serviceManager;

    /** @var  ApplicationInterface | PHPUnit_Framework_MockObject_MockObject */
    private $application;

    /** @var  EventManagerInterface | PHPUnit_Framework_MockObject_MockObject */
    private $eventManager;

    /** @var  View */
    private $view;

    /** @var  EventManagerInterface | PHPUnit_Framework_MockObject_MockObject */
    private $viewEventManager;

    /** @var array */
    private $config = array(
        'php' => array(
            'ini' => array(),
        ),
    );

    /** @var HeaderService | PHPUnit_Framework_MockObject_MockObject */
    private $headerService;

    /**
     * @var Module
     */
    private $module;

    /**
     * @var CsrfPlugin | PHPUnit_Framework_MockObject_MockBuilder
     */
    private $csrfPlugin;

    /**
     * @var MvcEvent | PHPUnit_Framework_MockObject_MockBuilder
     */
    private $mvcEvent;

    /** @var  ListenerAggregateInterface | PHPUnit_Framework_MockObject_MockObject */
    private $pendoListenerAggregate;

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
        $this->atLeastOnce(count($invocations));
    }

    public function testOnBootstrap()
    {
        $this->pendoListenerAggregate->expects(self::once())->method('attach')
            ->with($this->viewEventManager);
        $this->module->onBootstrap($this->mvcEvent);
    }

    /**
     * Set up local environment to test the Application module
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

        $this->eventManager = $this->getMock('\Zend\EventManager\EventManagerInterface');
        $this->eventManager->expects(self::any())
                           ->method('attach')
                           ->willReturn(true);

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

        $this->viewEventManager = $this->getMock('\Zend\EventManager\EventManagerInterface');

        $this->view = $this->getMock('\Zend\View\View', array(), array(), '', false);
        $this->view->expects(self::any())->method('getEventManager')
            ->willReturn($this->viewEventManager);

        $this->pendoListenerAggregate = $this->getMock('\Zend\EventManager\ListenerAggregateInterface');

        $controllerValidationEventManager = $this->getMock('\Zend\EventManager\EventManagerInterface');
        $sessionRequestContextListener = $this->getMock('\Zend\EventManager\ListenerAggregateInterface');
        $sessionLoggerContextListener = $this->getMock('\Zend\EventManager\ListenerAggregateInterface');

        $this->serviceManager = $this->getMockBuilder('\Zend\ServiceManager\ServiceLocatorInterface')
                                     ->getMock();
        $this->serviceManager->expects(self::any())->method('get')
            ->willReturnMap(
                array(
                    array('WebPT\EMR\Csrf\CsrfPlugin', $this->csrfPlugin),
                    array('ControllerValidationService', $validation),
                    array('EMRAdmin\Pendo\PendoListener', $this->pendoListenerAggregate),
                    array('View', $this->view),
                    array('Config', $this->config),
                    array('ControllerValidationEventManager', $controllerValidationEventManager),
                    array('SessionRequestContextListener', $sessionRequestContextListener),
                    array('SessionLoggerContextListener', $sessionLoggerContextListener),
                )
            );

        $this->application = $this->getMockBuilder('\Zend\Mvc\ApplicationInterface')
                                  ->getMock();
        $this->application->expects(self::any())
                          ->method('getServiceManager')
                          ->willReturn($this->serviceManager);
        $this->application->expects(self::any())
                          ->method('getEventManager')
                          ->willReturn($this->eventManager);

        $abstractController = $this->getMockBuilder('\Zend\Mvc\Controller\AbstractController')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mvcEvent = $this->getMockBuilder('\Zend\Mvc\MvcEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mvcEvent->expects($this->any())
            ->method('getApplication')
            ->willReturn($this->application);
        $this->mvcEvent->expects($this->any())
            ->method('isMocked')
            ->will($this->returnValue(true));
        $this->mvcEvent->expects($this->any())
            ->method('getTarget')
            ->willReturn($abstractController);

        $logger = $this->getMockBuilder('Logger')
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects($this->any())
            ->method('info')
            ->will($this->returnValue(true));

        $this->headerService = $this->getMock('\EMRCore\Http\HeaderService', array(), array(), '', false);
        $reflProp = new ReflectionProperty('\EMRCore\Http\HeaderService', 'instance');
        $reflProp->setAccessible(true);
        $reflProp->setValue(null, $this->headerService);
        $reflProp->setAccessible(false);

        $this->module = new Module;

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
        $reflProp = new ReflectionProperty('\EMRCore\Http\HeaderService', 'instance');
        $reflProp->setAccessible(true);
        $reflProp->setValue(null, null);
        $reflProp->setAccessible(false);

        parent::tearDown();

        unset(
            $this->module,
            $this->csrfPlugin,
            $this->mvcEvent
        );
    }
}

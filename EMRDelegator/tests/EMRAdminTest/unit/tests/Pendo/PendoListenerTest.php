<?php
/**
 * @category WebPT
 * @package EMRAdmin
 * @copyright Copyright (c) 2016 WebPT, INC
 * @author Tim Bradley (timothy.bradley@webpt.com)
 */
namespace EMRAdminTest\Pendo;

use EMRAdmin\Pendo\PendoListener;
use MyProject\Proxies\__CG__\OtherProject\Proxies\__CG__\stdClass;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use WebPT\Pendo\PendoAnalyticsInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\View\Helper\HeadScript;
use Zend\View\Model\ModelInterface;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Renderer\RendererInterface;
use Zend\View\ViewEvent;

class PendoListenerTest extends PHPUnit_Framework_TestCase
{
    /** @var PendoListener */
    private $sut;

    /** @var  PendoAnalyticsInterface | PHPUnit_Framework_MockObject_MockObject */
    private $pendo;

    public function setUp()
    {
        $this->pendo = $this->createMock('\WebPT\Pendo\PendoAnalyticsInterface');
        $this->sut = new PendoListener($this->pendo);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ViewEvent
     */
    private function prepareExpectedEvent()
    {
        /** @var HeadScript | PHPUnit_Framework_MockObject_MockObject $headScript */
        $headScript = $this->getMock('\Zend\View\Helper\HeadScript', array(), array(), '', false);

        /** @var PhpRenderer | PHPUnit_Framework_MockObject_MockObject $renderer */
        $renderer = $this->createMock('\Zend\View\Renderer\PhpRenderer');
        $renderer->expects(self::any())->method('__call')
            ->with('headScript')
            ->willReturn($headScript);

        $options = array(
            'has_parent' => false,
        );

        /** @var ModelInterface | PHPUnit_Framework_MockObject_MockObject $model */
        $model = $this->createMock('\Zend\View\Model\ModelInterface');
        $model->expects(self::any())->method('getOptions')
            ->will(self::returnValue($options));

        /** @var ViewEvent | PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMock('\Zend\View\ViewEvent', array(), array(), '', false);
        $event->expects(self::any())->method('getRenderer')
            ->will(self::returnValue($renderer));
        $event->expects(self::any())->method('getModel')
            ->will(self::returnValue($model));
        return $event;
    }

    public function testAttachWithOneEventManagerListensToEvents()
    {
        $em = new EventManager();
        $this->sut->attach($em);

        $this->pendo->expects(self::once())->method('getJavaScript')
            ->will(self::returnValue('Some JavaScript Snippet here.'));

        $event = $this->prepareExpectedEvent();

        /** @var PhpRenderer | PHPUnit_Framework_MockObject_MockObject $renderer */
        $renderer = $event->getRenderer();

        /** @var HeadScript | PHPUnit_Framework_MockObject_MockObject $headScript */
        $headScript = $renderer->headScript();
        $headScript->expects(self::once())->method('__call')
            ->with('prependScript', array('Some JavaScript Snippet here.'));

        $em->trigger(ViewEvent::EVENT_RENDERER_POST, $event);
    }

    public function testAttachAttachesToExpectedEventWithExpectedCallback()
    {
        /** @var EventManagerInterface | PHPUnit_Framework_MockObject_MockObject $em */
        $em = $this->createMock('\Zend\EventManager\EventManagerInterface');
        $em->expects(self::once())->method('attach')
            ->with(ViewEvent::EVENT_RENDERER_POST, array($this->sut, '__invoke'));

        $this->sut->attach($em);
    }

    public function testDetachDetachesFromExpectedEventUsingExpectedListener()
    {
        /** @var \Zend\Stdlib\CallbackHandler $cbHandler */
        $cbHandler = $this->getMock('\Zend\Stdlib\CallbackHandler', array(), array(), '', false);

        /** @var EventManagerInterface | PHPUnit_Framework_MockObject_MockObject $em */
        $em = $this->createMock('\Zend\EventManager\EventManagerInterface');
        $em->expects(self::once())->method('attach')
           ->with(ViewEvent::EVENT_RENDERER_POST, array($this->sut, '__invoke'))
            ->willReturn($cbHandler);

        $this->sut->attach($em);

        $em->expects(self::once())->method('detach')
            ->with($cbHandler)
            ->will(self::returnValue(true));

        $this->sut->detach($em);
    }

    public function testAttachWithOneEventManagerIgnoresWrongEventType()
    {
        $em = new EventManager();
        $this->sut->attach($em);

        $this->pendo->expects(self::never())->method('getJavaScript');

        /** @var PhpRenderer | PHPUnit_Framework_MockObject_MockObject $renderer */
        $renderer = $this->createMock('\Zend\View\Renderer\PhpRenderer');

        $options = array(
            'has_parent' => true,
        );

        /** @var ModelInterface | PHPUnit_Framework_MockObject_MockObject $model */
        $model = $this->createMock('\Zend\View\Model\ModelInterface');
        $model->expects(self::any())->method('getOptions')
              ->will(self::returnValue($options));

        /** @var EventInterface | PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->createMock('\Zend\EventManager\EventInterface');
        $event->expects(self::any())->method('getRenderer')
              ->will(self::returnValue($renderer));
        $event->expects(self::any())->method('getModel')
              ->will(self::returnValue($model));

        $renderer->expects(self::never())->method('__call');
        $renderer->expects(self::never())->method('headScript');

        $em->trigger(ViewEvent::EVENT_RENDERER_POST, $event);
    }

    public function testAttachWithOneEventManagerIgnoresTruthyHasParentValue()
    {
        $em = new EventManager();
        $this->sut->attach($em);

        $this->pendo->expects(self::never())->method('getJavaScript');

        /** @var PhpRenderer | PHPUnit_Framework_MockObject_MockObject $renderer */
        $renderer = $this->createMock('\Zend\View\Renderer\PhpRenderer');

        $options = array(
            'has_parent' => true,
        );

        /** @var ModelInterface | PHPUnit_Framework_MockObject_MockObject $model */
        $model = $this->createMock('\Zend\View\Model\ModelInterface');
        $model->expects(self::any())->method('getOptions')
              ->will(self::returnValue($options));

        /** @var ViewEvent | PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMock('\Zend\View\ViewEvent', array(), array(), '', false);
        $event->expects(self::any())->method('getRenderer')
              ->will(self::returnValue($renderer));
        $event->expects(self::any())->method('getModel')
              ->will(self::returnValue($model));

        $renderer->expects(self::never())->method('__call');
        $renderer->expects(self::never())->method('headScript');

        $em->trigger(ViewEvent::EVENT_RENDERER_POST, $event);
    }

    public function testAttachWithOneEventManagerIgnoresBadModelReturnType()
    {
        $em = new EventManager();
        $this->sut->attach($em);

        $this->pendo->expects(self::never())->method('getJavaScript');

        /** @var PhpRenderer | PHPUnit_Framework_MockObject_MockObject $renderer */
        $renderer = $this->createMock('\Zend\View\Renderer\PhpRenderer');

        $model = new \stdClass();
        $model->getOptions = function(){
            return array(
                'has_parent' => false
            );
        };

        /** @var ViewEvent | PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMock('\Zend\View\ViewEvent', array(), array(), '', false);
        $event->expects(self::any())->method('getRenderer')
              ->will(self::returnValue($renderer));
        $event->expects(self::any())->method('getModel')
              ->will(self::returnValue($model));

        $renderer->expects(self::never())->method('__call');
        $renderer->expects(self::never())->method('headScript');

        $em->trigger(ViewEvent::EVENT_RENDERER_POST, $event);
    }

    public function testAttachWithOneEventManagerIgnoresWrongRenderer()
    {
        $em = new EventManager();
        $this->sut->attach($em);

        $this->pendo->expects(self::never())->method('getJavaScript');

        /** @var RendererInterface | PHPUnit_Framework_MockObject_MockObject $renderer */
        $renderer = $this->createMock('\Zend\View\Renderer\RendererInterface');

        $options = array(
            'has_parent' => false,
        );

        /** @var ModelInterface | PHPUnit_Framework_MockObject_MockObject $model */
        $model = $this->createMock('\Zend\View\Model\ModelInterface');
        $model->expects(self::any())->method('getOptions')
              ->will(self::returnValue($options));

        /** @var ViewEvent | PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMock('\Zend\View\ViewEvent', array(), array(), '', false);
        $event->expects(self::any())->method('getRenderer')
              ->will(self::returnValue($renderer));
        $event->expects(self::any())->method('getModel')
              ->will(self::returnValue($model));

        $renderer->expects(self::never())->method('__call');
        $renderer->expects(self::never())->method('headScript');

        $em->trigger(ViewEvent::EVENT_RENDERER_POST, $event);
    }

    public function testAttachWithThreeEventManagersListensToEvents()
    {
        $em = new EventManager();
        $em2 = new EventManager();
        $em3 = new EventManager();

        $this->sut->attach($em);
        $this->sut->attach($em2);
        $this->sut->attach($em3);

        $this->pendo->expects(self::exactly(3))->method('getJavaScript')
            ->willReturnOnConsecutiveCalls(
                'Some JavaScript Snippet here - 1.',
                'Some JavaScript Snippet here - 2.',
                'Some JavaScript Snippet here - 3.'
            );

        // Trigger the first event manager
        $event = $this->prepareExpectedEvent();
        /** @var PhpRenderer | PHPUnit_Framework_MockObject_MockObject $renderer */
        $renderer = $event->getRenderer();

        /** @var HeadScript | PHPUnit_Framework_MockObject_MockObject $headScript */
        $headScript = $renderer->headScript();
        $headScript->expects(self::once())->method('__call')
                   ->with('prependScript', array('Some JavaScript Snippet here - 1.'));

        $em->trigger(ViewEvent::EVENT_RENDERER_POST, $event);

        // Trigger the third event manager
        $event = $this->prepareExpectedEvent();
        /** @var PhpRenderer | PHPUnit_Framework_MockObject_MockObject $renderer */
        $renderer = $event->getRenderer();

        /** @var HeadScript | PHPUnit_Framework_MockObject_MockObject $headScript */
        $headScript = $renderer->headScript();
        $headScript->expects(self::once())->method('__call')
                   ->with('prependScript', array('Some JavaScript Snippet here - 2.'));

        $em3->trigger(ViewEvent::EVENT_RENDERER_POST, $event);


        // Trigger the second event manager
        $event = $this->prepareExpectedEvent();
        /** @var PhpRenderer | PHPUnit_Framework_MockObject_MockObject $renderer */
        $renderer = $event->getRenderer();

        /** @var HeadScript | PHPUnit_Framework_MockObject_MockObject $headScript */
        $headScript = $renderer->headScript();
        $headScript->expects(self::once())->method('__call')
                   ->with('prependScript', array('Some JavaScript Snippet here - 3.'));

        $em2->trigger(ViewEvent::EVENT_RENDERER_POST, $event);
    }

    public function testDetachWithOneEventManagerDoesNotListenToEvents()
    {
        $em = new EventManager();

        $this->sut->attach($em);
        $this->sut->detach($em);

        $this->pendo->expects(self::never())->method('getJavaScript');

        $event = $this->prepareExpectedEvent();

        /** @var PhpRenderer | PHPUnit_Framework_MockObject_MockObject $renderer */
        $renderer = $event->getRenderer();

        $renderer->expects(self::never())->method('__call');
        $renderer->expects(self::never())->method('headScript');

        $em->trigger(ViewEvent::EVENT_RENDERER_POST, $event);
    }

    public function testAttachWithThreeEventManagersAndTwoDetachedListensToOnlyOneEvent()
    {
        $em = new EventManager();
        $em2 = new EventManager();
        $em3 = new EventManager();

        $this->sut->attach($em);
        $this->sut->attach($em2);
        $this->sut->attach($em3);

        $this->sut->detach($em);
        $this->sut->detach($em3);

        $this->pendo->expects(self::once())->method('getJavaScript')
                    ->willReturn('Some JavaScript Snippet here - 2.');

        // Trigger the first event manager
        $event = $this->prepareExpectedEvent();
        /** @var PhpRenderer | PHPUnit_Framework_MockObject_MockObject $renderer */
        $renderer = $event->getRenderer();

        /** @var HeadScript | PHPUnit_Framework_MockObject_MockObject $headScript */
        $headScript = $renderer->headScript();
        $headScript->expects(self::never())->method('prependScript');

        $em->trigger(ViewEvent::EVENT_RENDERER_POST, $event);

        // Trigger the third event manager
        $event = $this->prepareExpectedEvent();
        /** @var PhpRenderer | PHPUnit_Framework_MockObject_MockObject $renderer */
        $renderer = $event->getRenderer();

        /** @var HeadScript | PHPUnit_Framework_MockObject_MockObject $headScript */
        $headScript = $renderer->headScript();
        $headScript->expects(self::never())->method('prependScript');

        $em3->trigger(ViewEvent::EVENT_RENDERER_POST, $event);


        // Trigger the second event manager
        $event = $this->prepareExpectedEvent();
        /** @var PhpRenderer | PHPUnit_Framework_MockObject_MockObject $renderer */
        $renderer = $event->getRenderer();

        /** @var HeadScript | PHPUnit_Framework_MockObject_MockObject $headScript */
        $headScript = $renderer->headScript();
        $headScript->expects(self::once())->method('__call')
                   ->with('prependScript', array('Some JavaScript Snippet here - 2.'));

        $em2->trigger(ViewEvent::EVENT_RENDERER_POST, $event);
    }

    public function testDetachWithThreeEventManagersDoesNotListenToEvents()
    {
        $em = new EventManager();
        $em2 = new EventManager();
        $em3 = new EventManager();

        $this->sut->attach($em);
        $this->sut->attach($em2);
        $this->sut->attach($em3);

        $this->sut->detach($em);
        $this->sut->detach($em2);
        $this->sut->detach($em3);

        $this->pendo->expects(self::never())->method('getJavaScript');

        // Trigger the first event manager
        $event = $this->prepareExpectedEvent();
        /** @var PhpRenderer | PHPUnit_Framework_MockObject_MockObject $renderer */
        $renderer = $event->getRenderer();

        /** @var HeadScript | PHPUnit_Framework_MockObject_MockObject $headScript */
        $headScript = $renderer->headScript();
        $headScript->expects(self::never())->method('prependScript');

        $em->trigger(ViewEvent::EVENT_RENDERER_POST, $event);

        // Trigger the third event manager
        $event = $this->prepareExpectedEvent();
        /** @var PhpRenderer | PHPUnit_Framework_MockObject_MockObject $renderer */
        $renderer = $event->getRenderer();

        /** @var HeadScript | PHPUnit_Framework_MockObject_MockObject $headScript */
        $headScript = $renderer->headScript();
        $headScript->expects(self::never())->method('prependScript');

        $em3->trigger(ViewEvent::EVENT_RENDERER_POST, $event);

        // Trigger the second event manager
        $event = $this->prepareExpectedEvent();
        /** @var PhpRenderer | PHPUnit_Framework_MockObject_MockObject $renderer */
        $renderer = $event->getRenderer();

        /** @var HeadScript | PHPUnit_Framework_MockObject_MockObject $headScript */
        $headScript = $renderer->headScript();
        $headScript->expects(self::never())->method('prependScript');

        $em2->trigger(ViewEvent::EVENT_RENDERER_POST, $event);
    }
}

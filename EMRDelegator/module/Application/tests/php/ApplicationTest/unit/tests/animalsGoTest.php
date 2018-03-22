<?php

namespace ApplicationTest\unit\tests;
use Application\Controller\SuperUserDelegationController;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Literal as LiteralRoute;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Mvc\Router\SimpleRouteStack;
use Zend\Mvc\Controller\PluginManager;
use EMRCore\Service\Auth\Token\Exception\Authentication;

class cat {

    public function meow()
    {
        return 'meow';
    }
}

class dog {
    public function bark(){
        return 'bark';
    }

}

class animals {

    public function animalsGo()
    {
        /** @var $cat cat */
        $cat = $this->getLocator()->getCat();
        /** @var $dog dog */
        $dog = $this->getLocator()->getDog();
        $dog->bark();
        $cat->meow();

    }

    public function theCatGoes()
    {
        $cat = $this->getLocator()->getCat();
        return $cat->meow();
    }

    /** @var locator */
    protected $locator;

    public function setLocator($locator)
    {
        $this->locator = $locator;
    }

    public function getLocator()
    {
        return $this->locator;
    }

}


class locator {

    public function getCat(){
        return new cat();
    }

    public function getDog(){
        return new dog();
    }

}


class animalsGoTest extends PHPUnit_Framework_TestCase
{


    public function testAnimalsGoCallsCatMeow()
    {

        $catMock = $this->getMock('cat',array('meow'));
        $catMock->expects($this->once())
            ->method('meow')
            ->will($this->returnValue('meow'));


        $dogMock = $this->getMock('dog',array('bark'));
        $dogMock->expects($this->once())
            ->method('bark')
            ->will($this->returnValue('bark'));


        $locatorMock = $this->getMock('locator',array('getCat','getDog'));
        $locatorMock->expects($this->once())
            ->method('getCat')
            ->will($this->returnValue($catMock));
        $locatorMock->expects($this->once())
            ->method('getDog')
            ->will($this->returnValue($dogMock));


        $animals = new animals();
        $animals->setLocator($locatorMock);
        $animals->animalsGo();

    }


    public function testTheCatGoesReturnsMeow()
    {
        $animals = new animals();



        //$sound = $animals->theCatGoes();
        //$this->assertEquals($sound,'meow');





    }



}







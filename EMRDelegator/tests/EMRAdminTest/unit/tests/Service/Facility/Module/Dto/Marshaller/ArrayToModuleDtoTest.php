<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 8/6/13 2:31 PM
 */
use EMRCoreTest\Helper\Reflection as ReflectionHelper;
use EMRAdmin\Service\Facility\Module\Dto\Marshaller\ArrayToModuleDto;

class ArrayToModuleDtoTest extends PHPUnit_Framework_TestCase {
    /**
     * @expectedException \EMRCore\Marshaller\Exception\IncompleteArray
     */
    public function testCheckFieldThrowsIncompleteArray() {
        $marshaller = new ArrayToModuleDto();
        ReflectionHelper::invoke($marshaller, 'checkField', array('foo', array('bar'=>'biz')));
    }

    public function testCheckFieldDoesNotThrowException() {
        $marshaller = new ArrayToModuleDto();
        ReflectionHelper::invoke($marshaller, 'checkField', array('foo', array('foo'=>'biz')));
    }

    public function testCheckFieldsChecksModuleFields() {
        $item = array(
            'id' => 3,
            'name' => 'foo',
            'description' => 'bar'
        );

        $marshaller = $this->getMock('EMRAdmin\Service\Facility\Module\Dto\Marshaller\ArrayToModuleDto', array('checkField'));
        $marshaller->expects($this->exactly(3))
            ->method('checkField')
            ->will($this->returnValueMap(array(
                array('id', $item),
                array('name', $item),
                array('description', $item)
            )));

        ReflectionHelper::invoke($marshaller, 'checkFields', array($item));
    }

    public function testMarshalReturnsHydratedModule() {
        $id = 9;
        $name = 'foo';
        $description = 'bar';

        $item = array(
            'id' => $id,
            'name' => $name,
            'description' => $description
        );

        $marshaller = new ArrayToModuleDto();
        $module = $marshaller->marshall($item);

        $this->assertEquals($id, $module->getId());
        $this->assertEquals($name, $module->getName());
        $this->assertEquals($description, $module->getDescription());
    }
}

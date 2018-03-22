<?php


use EMRAdmin\Service\Facility\Module\Dto\Marshaller\ModulesDtoToModuleCollection;


class ModulesDtoToModuleCollectionTest extends PHPUnit_Framework_TestCase {

    public function testMarshallValidatedItemIteratesPayload() {
        $moduleArray = array('foo');
        $module = 'foo';

        $item = $this->getMock('EMRCore\Service\Esb\Facility\Module\Dto\Modules', array('getPayload'));
        $item->expects($this->once())
            ->method('getPayload')
            ->will($this->returnValue((object)array('modules' => array($moduleArray))));

        $marshalMock = $this->getMock('stdClass', array('marshall'));
        $marshalMock->expects($this->once())
            ->method('marshall')
            ->with($moduleArray)
            ->will($this->returnValue($module));

        $collectionMock = $this->getMock('stdClass', array('push'));
        $collectionMock->expects($this->once())
            ->method('push')
            ->with($module);

        /** @var ModulesDtoToModuleCollection $marshaller */
        $marshaller = $this->getMock('EMRAdmin\Service\Facility\Module\Dto\Marshaller\ModulesDtoToModuleCollection',
            array('getNewCollection', 'getNewMarshaller'));

        $marshaller->expects($this->once())
            ->method('getNewCollection')
            ->will($this->returnValue($collectionMock));
        $marshaller->expects($this->once())
            ->method('getNewMarshaller')
            ->will($this->returnValue($marshalMock));

        $result = $marshaller->marshall($item);
        $this->assertEquals($collectionMock, $result);
    }

}

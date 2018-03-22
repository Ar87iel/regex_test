<?php
namespace EMRAdminTest\unit\tests\Service\Company\Marshaller\Search;

use EMRAdmin\Service\Company\Marshaller\Search\StdClassToTelephone;
use EMRCore\Contact\Address\Dto\Address;
use EMRCore\Contact\Telephone\Dto\Telephone;
use EMRCore\PrototypeFactory;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class StdClassToTelephoneTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var StdClassToTelephone
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new StdClassToTelephone;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidItemType()
    {
        $this->marshaller->marshall(array());
    }

    public function testMarshalsAddress()
    {
        $id = 1;

        $item = new \stdClass();
        $item->id = $id;
        $item->areaCode = null;
        $item->localNumber = null;

        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);
        $prototypeFactory->expects($this->once())
            ->method('createAndInitialize')
            ->with($this->equalTo('EMRCore\Contact\Telephone\Dto\Telephone'))
            ->will($this->returnValue(new Telephone));

        $this->marshaller->setPrototypeFactory($prototypeFactory);

        $address = $this->marshaller->marshall($item);

        $this->assertSame($id, $address->getId());
    }
}
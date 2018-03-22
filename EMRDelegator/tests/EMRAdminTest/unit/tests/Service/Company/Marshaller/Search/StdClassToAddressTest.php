<?php
namespace EMRAdminTest\unit\tests\Service\Company\Marshaller\Search;

use EMRAdmin\Service\Company\Marshaller\Search\StdClassToAddress;
use EMRCore\Contact\Address\Dto\Address;
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
class StdClassToAddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var StdClassToAddress
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new StdClassToAddress;
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
        $item->street = null;
        $item->otherDesignation = null;
        $item->city = null;
        $item->state = null;
        $item->country = null;
        $item->postalCode = null;

        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);
        $prototypeFactory->expects($this->once())
            ->method('createAndInitialize')
            ->with($this->equalTo('EMRCore\Contact\Address\Dto\Address'))
            ->will($this->returnValue(new Address));

        $this->marshaller->setPrototypeFactory($prototypeFactory);

        $address = $this->marshaller->marshall($item);

        $this->assertSame($id, $address->getId());
    }
}
<?php

namespace EMRAdminTest\unit\tests\Service\Facility\Marshaller;

use EMRAdmin\Service\Cluster\Marshaller\SuccessToGetClusterResponse;
use EMRAdmin\Service\Facility\Marshaller\SuccessToGetFacilityByIdResponse;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use stdClass;
use EMRAdmin\Service\Facility\Dto\Facility;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use EMRCore\Contact\Address\Dto\Address;
use EMRCore\Contact\Fax\Dto\Fax;
use EMRCore\Contact\Telephone\Dto\Telephone;
use EMRCore\PrototypeFactory;
use InvalidArgumentException;

class SuccessToGetFacilityByIdResponseTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SuccessToGetFacilityByIdResponse
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new SuccessToGetFacilityByIdResponse();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidItemType()
    {
        $this->marshaller->marshall(new stdClass);
    }

    public function testMarshalsGetFacilityByIdResponse()
    {
        $payload = (object) array();

        /* @var Address $address */
        $address = new Address;

        /** @var Telephone $phone */
        $phone = new Telephone;

        /** @var Fax $fax */
        $fax = new Fax;

        /** @var Success $success */
        $success = new Success;
        $success->setPayload($payload);

        /** @var Facility $facility */
        $facility = new Facility;

        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);
        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
            function($name) use ($address, $phone, $fax, $facility)
            {
                if ($name == 'EMRCore\Contact\Address\Dto\Address')
                {
                    return $address;
                }
                if ($name == 'EMRCore\Contact\Telephone\Dto\Telephone')
                {
                    return $phone;
                }
                if ($name == 'EMRCore\Contact\Fax\Dto\Fax')
                {
                    return $fax;
                }
                if ($name == 'EMRAdmin\Service\Facility\Dto\Facility')
                {
                    return $facility;
                }

                throw new InvalidArgumentException("Mocked prototypeFactory cannot create name [$name].");
            }));

        /** @var SuccessToGetFacilityByIdResponse|PHPUnit_Framework_MockObject_MockObject $marshaller */
        $marshaller = $this->getMock('EMRAdmin\Service\Facility\Marshaller\SuccessToGetFacilityByIdResponse', array(
            'setAddressInformation',
            'setAddress2Information',
            'setPhoneInformation',
            'setPhone2Information',
            'setFaxInformation',
            'setFax2Information',
            'setFacilityInformation',
            'setBillingAddressInformation'
        ));

        $marshaller->expects($this->once())
            ->method('setAddressInformation', 'setAddress2Information', 'setBillingAddressInformation')
            ->with($this->equalTo($address), $this->equalTo($payload));

        $marshaller->expects($this->once())
            ->method('setPhoneInformation', 'setPhone2Information')
            ->with($this->equalTo($phone), $this->equalTo($payload));

        $marshaller->expects($this->once())
            ->method('setFaxInformation', 'setFax2Information')
            ->with($this->equalTo($fax), $this->equalTo($payload));

        $marshaller->expects($this->once())
            ->method('setFacilityInformation')
            ->with($this->equalTo($facility), $this->equalTo($payload), $this->equalTo($address), $this->equalTo($address), $this->equalTo($address), $this->equalTo($phone), $this->equalTo($phone), $this->equalTo($fax), $this->equalTo($fax));

        $marshaller->setPrototypeFactory($prototypeFactory);

        /** @var Facility $response */
        $response = $marshaller->marshall($success);

        $this->assertInstanceOf('EMRAdmin\Service\Facility\Dto\Facility', $response);
    }

}
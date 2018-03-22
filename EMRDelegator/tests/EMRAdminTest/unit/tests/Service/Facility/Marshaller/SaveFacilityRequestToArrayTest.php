<?php
namespace EMRAdminTest\unit\tests\Service\Facility\Marshaller;

use EMRAdmin\Service\Facility\Dto\SaveFacilityRequest;
use EMRAdmin\Service\Facility\Marshaller\SaveFacilityRequestToArray;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class SaveFacilityRequestToArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $fax;
    /**
     * @var SaveFacilityRequest
     */
    private $request;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $address;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $telephone;

    public function setUp()
    {
        $this->request = new SaveFacilityRequest;

        $this->address = $this->createMock('EMRCore\Contact\Address\Dto\Address');
        $this->telephone = $this->createMock('EMRCore\Contact\Telephone\Dto\Telephone');
        $this->fax = $this->createMock('EMRCore\Contact\Fax\Dto\Fax');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidItemType()
    {
        $marshaller = new SaveFacilityRequestToArray;
        $marshaller->marshall(array());
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockSaveFacilityRequestToArray()
    {
        return $this->getMock('EMRAdmin\Service\Facility\Marshaller\SaveFacilityRequestToArray', array(
            'setPrimaryAddressInformation',
            'setSecondaryAddressInformation',
            'setBillingAddressInformation',
            'setPrimaryTelephoneInformation',
            'setSecondaryTelephoneInformation',
            'setPrimaryFaxInformation',
            'setSecondaryFaxInformation',
        ));
    }

    public function testMarshalGetsArray()
    {
        $marshaller = $this->getMockSaveFacilityRequestToArray();

        /** @var SaveFacilityRequestToArray $marshaller */
        $response = $marshaller->marshall($this->request);

        $this->assertInternalType('array', $response);
    }

    public function testMarshalSetsPrimaryAddressDataWhenPrimaryAddressIsPresent()
    {
        $marshaller = $this->getMockSaveFacilityRequestToArray();

        $this->request->setAddress($this->address);
        $marshaller->expects($this->once())->method('setPrimaryAddressInformation')->with($this->equalTo($this->address));

        /** @var SaveFacilityRequestToArray $marshaller */
        $marshaller->marshall($this->request);
    }

    public function testMarshalSetsSecondaryAddressDataWhenSecondaryAddressIsPresent()
    {
        $marshaller = $this->getMockSaveFacilityRequestToArray();

        $this->request->setAddress2($this->address);
        $marshaller->expects($this->once())->method('setSecondaryAddressInformation')->with($this->equalTo($this->address));

        /** @var SaveFacilityRequestToArray $marshaller */
        $marshaller->marshall($this->request);
    }

    public function testMarshalSetsBillingAddressDataWhenBillingAddressIsPresent()
    {
        $marshaller = $this->getMockSaveFacilityRequestToArray();

        $this->request->setBillingAddress($this->address);
        $marshaller->expects($this->once())->method('setBillingAddressInformation')->with($this->equalTo($this->address));

        /** @var SaveFacilityRequestToArray $marshaller */
        $marshaller->marshall($this->request);
    }

    public function testMarshalSetsPrimaryTelephoneDataWhenPrimaryTelephoneIsPresent()
    {
        $marshaller = $this->getMockSaveFacilityRequestToArray();

        $this->request->setTelephone($this->telephone);
        $marshaller->expects($this->once())->method('setPrimaryTelephoneInformation')->with($this->equalTo($this->telephone));

        /** @var SaveFacilityRequestToArray $marshaller */
        $marshaller->marshall($this->request);
    }

    public function testMarshalSetsSecondaryTelephoneDataWhenSecondaryTelephoneIsPresent()
    {
        $marshaller = $this->getMockSaveFacilityRequestToArray();

        $this->request->setTelephone2($this->telephone);
        $marshaller->expects($this->once())->method('setSecondaryTelephoneInformation')->with($this->equalTo($this->telephone));

        /** @var SaveFacilityRequestToArray $marshaller */
        $marshaller->marshall($this->request);
    }

    public function testMarshalSetsPrimaryFaxDataWhenPrimaryFaxIsPresent()
    {
        $marshaller = $this->getMockSaveFacilityRequestToArray();

        $this->request->setFax($this->fax);
        $marshaller->expects($this->once())->method('setPrimaryFaxInformation')->with($this->equalTo($this->fax));

        /** @var SaveFacilityRequestToArray $marshaller */
        $marshaller->marshall($this->request);
    }

    public function testMarshalSetsSecondaryFaxDataWhenSecondaryFaxIsPresent()
    {
        $marshaller = $this->getMockSaveFacilityRequestToArray();

        $this->request->setFax2($this->fax);
        $marshaller->expects($this->once())->method('setSecondaryFaxInformation')->with($this->equalTo($this->fax));

        /** @var SaveFacilityRequestToArray $marshaller */
        $marshaller->marshall($this->request);
    }
}
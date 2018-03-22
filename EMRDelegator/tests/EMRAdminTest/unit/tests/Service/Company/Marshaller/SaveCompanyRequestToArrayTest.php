<?php
namespace EMRAdminTest\unit\tests\Service\Company\Marshaller;

use EMRAdmin\Service\Company\Dto\SaveCompanyRequest;
use EMRAdmin\Service\Company\Marshaller\SaveCompanyRequestToArray;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class SaveCompanyRequestToArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SaveCompanyRequest
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
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $email;

    public function setUp()
    {
        $this->request = new SaveCompanyRequest;

        $this->address = $this->createMock('EMRCore\Contact\Address\Dto\Address');
        $this->telephone = $this->createMock('EMRCore\Contact\Telephone\Dto\Telephone');
        $this->email = $this->createMock('EMRCore\Contact\Email\Dto\Email');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidItemType()
    {
        $marshaller = new SaveCompanyRequestToArray;
        $marshaller->marshall(array());
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockSaveCompanyRequestToArray()
    {
        return $this->getMock('EMRAdmin\Service\Company\Marshaller\SaveCompanyRequestToArray', array(
            'setAddressInformation',
            'setTelephoneInformation',
            'setEmailInformation',
        ));
    }

    public function testMarshalGetsArray()
    {
        $marshaller = $this->getMockSaveCompanyRequestToArray();

        /** @var SaveCompanyRequestToArray $marshaller */
        $response = $marshaller->marshall($this->request);

        $this->assertInternalType('array', $response);
    }

    public function testMarshalGetsAddressDataWhenAddressIsPresent()
    {
        $marshaller = $this->getMockSaveCompanyRequestToArray();

        $this->request->setAddress($this->address);
        $marshaller->expects($this->once())->method('setAddressInformation')->with($this->equalTo($this->address));

        /** @var SaveCompanyRequestToArray $marshaller */
        $marshaller->marshall($this->request);
    }

    public function testMarshallReturnsArrayWithCompanyRequestSettings() {
        $this->request->setId($id = 1);
        $this->request->setName($name = 'foo');
        $this->request->setAccountTypeId($accountTypeId = 2);
        $this->request->setOnlineStatus($onlineStatus = 'bar');
        $this->request->setClusterId($clusterId = 3);

        /** @var SaveCompanyRequestToArray $marshaller */
        $marshaller = $this->getMockSaveCompanyRequestToArray();
        $response = $marshaller->marshall($this->request);

        $this->assertEquals($id, $response['id']);
        $this->assertEquals($name, $response['name']);
        $this->assertEquals($accountTypeId, $response['accountTypeId']);
        $this->assertEquals($onlineStatus, $response['onlineStatus']);
        $this->assertEquals($clusterId, $response['clusterId']);
    }

    public function testMarshalGetsTelephoneDataWhenTelephoneIsPresent()
    {
        $marshaller = $this->getMockSaveCompanyRequestToArray();

        $this->request->setTelephone($this->telephone);
        $marshaller->expects($this->once())->method('setTelephoneInformation')->with($this->equalTo($this->telephone));

        /** @var SaveCompanyRequestToArray $marshaller */
        $marshaller->marshall($this->request);
    }

    public function testMarshalGetsEmailDataWhenEmailIsPresent()
    {
        $marshaller = $this->getMockSaveCompanyRequestToArray();

        $this->request->setEmail($this->email);
        $marshaller->expects($this->once())->method('setEmailInformation')->with($this->equalTo($this->email));

        /** @var SaveCompanyRequestToArray $marshaller */
        $marshaller->marshall($this->request);
    }
}
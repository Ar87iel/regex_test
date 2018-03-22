<?php
namespace EMRAdminTest\unit\tests\Service\Company\Marshaller;

use EMRAdmin\Service\Company\Dto\Company;
use EMRAdmin\Service\Company\Dto\GetCompanyByIdResponse;
use EMRAdmin\Service\Company\Marshaller\SuccessToGetCompanyByIdResponse;
use EMRCore\Contact\Address\Dto\Address;
use EMRCore\Contact\Email\Dto\Email;
use EMRCore\Contact\Telephone\Dto\Telephone;
use EMRCore\PrototypeFactory;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use EMRCoreTest\lib\PhpUnit\SingletonTestCaseHelper;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use stdClass;

class SuccessToGetCompanyByIdResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SingletonTestCaseHelper
     */
    private $singletonHelper;

    /**
     * @var SuccessToGetCompanyByIdResponse
     */
    private $marshaller;

    public function setUp()
    {
        $this->singletonHelper = new SingletonTestCaseHelper($this);

        $this->marshaller = new SuccessToGetCompanyByIdResponse;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidItemType()
    {
        $this->marshaller->marshall(new stdClass);
    }

    public function testMarshalsGetCompanyByIdResponse()
    {
        $payload = (object) array();

        $address = new Address;
        $telephone = new Telephone;
        $email = new Email;
        $company = new Company;
        $responseDto = new GetCompanyByIdResponse;

        $prototypeFactoryClass = 'EMRCore\PrototypeFactory';
        $prototypeFactory = $this->getMock($prototypeFactoryClass, array('create', 'runServiceManagerInitializers'), array(), '', false);
        $this->singletonHelper->mockSingleton($prototypeFactory, $prototypeFactoryClass);

        $prototypeFactory->expects($this->any())->method('runServiceManagerInitializers');

        $prototypeFactory->expects($this->any())->method('create')
            ->will($this->returnCallback(function($name) use ($address, $telephone, $email, $company, $responseDto) {

                if ($name === 'EMRCore\Contact\Address\Dto\Address') {
                    return $address;
                }

                if ($name === 'EMRCore\Contact\Telephone\Dto\Telephone') {
                    return $telephone;
                }

                if ($name === 'EMRCore\Contact\Email\Dto\Email') {
                    return $email;
                }

                if ($name === 'EMRAdmin\Service\Company\Dto\Company') {
                    return $company;
                }

                if ($name === 'EMRAdmin\Service\Company\Dto\GetCompanyByIdResponse') {
                    return $responseDto;
                }

                throw new InvalidArgumentException("Mocked prototypeFactory cannot create name [$name].");
            }));

        $marshaller = $this->getMock('EMRAdmin\Service\Company\Marshaller\SuccessToGetCompanyByIdResponse', array(
            'setCompanyInformation',
            'setAddressInformation',
            'setPhoneInformation',
            'setEmailInformation',
        ));

        $marshaller->expects($this->once())->method('setAddressInformation')
            ->with($this->equalTo($address), $this->equalTo($payload));

        $marshaller->expects($this->once())->method('setPhoneInformation')
            ->with($this->equalTo($telephone), $this->equalTo($payload));

        $marshaller->expects($this->once())->method('setEmailInformation')
            ->with($this->equalTo($email), $this->equalTo($payload));

        $marshaller->expects($this->once())->method('setCompanyInformation')
            ->with($this->equalTo($company), $this->equalTo($payload), $this->equalTo($address), $this->equalTo($telephone), $this->equalTo($email));

        /**
         * @var SuccessToGetCompanyByIdResponse $marshaller
         * @var PrototypeFactory $prototypeFactory
         */
        $marshaller->setPrototypeFactory($prototypeFactory);

        $success = new Success();
        $success->setPayload($payload);

        $response = $marshaller->marshall($success);

        $this->assertInstanceOf('EMRAdmin\Service\Company\Dto\Company', $response);
    }
}
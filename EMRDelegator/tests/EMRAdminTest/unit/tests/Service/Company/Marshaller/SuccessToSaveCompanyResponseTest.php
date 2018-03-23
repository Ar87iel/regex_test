<?php
namespace EMRAdminTest\unit\tests\Service\Company\Marshaller;

use EMRAdmin\Service\Company\Dto\SaveCompanyResponse;
use EMRAdmin\Service\Company\Marshaller\SuccessToSaveCompanyResponse;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use PHPUnit_Framework_TestCase;
use stdClass;

class SuccessToSaveCompanyResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SuccessToSaveCompanyResponse
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new SuccessToSaveCompanyResponse;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidItemType()
    {
        $this->marshaller->marshall(new stdClass);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToMissingCompanyData()
    {
        $success = new Success;
        $success->setPayload((object) array(
            'company' => array(),
        ));

        $this->marshaller->marshall($success);
    }

    public function testMarshalsSaveCompanyResponse()
    {
        $marshaller = $this->getMock('EMRAdmin\Service\Company\Marshaller\SuccessToSaveCompanyResponse', array(
            'setCompanyInformation',
            'setAddressInformation',
            'setTelephoneInformation',
            'setEmailInformation',
        ));

        $marshaller->expects($this->once())->method('setCompanyInformation')->withAnyParameters();
        $marshaller->expects($this->once())->method('setAddressInformation')->withAnyParameters();
        $marshaller->expects($this->once())->method('setTelephoneInformation')->withAnyParameters();
        $marshaller->expects($this->once())->method('setEmailInformation')->withAnyParameters();

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $serviceLocator->expects($this->once())->method('get')
            ->with($this->equalTo('EMRAdmin\Service\Company\Dto\SaveCompanyResponse'))
            ->will($this->returnValue(new SaveCompanyResponse));

        /** @var SuccessToSaveCompanyResponse $marshaller */
        $marshaller->setServiceLocator($serviceLocator);

        $success = new Success();
        $success->setPayload((object) array(
            'company' => (object) array(
                'stuff' => 'things',
            ),
        ));

        $response = $marshaller->marshall($success);

        $this->assertInstanceOf('EMRAdmin\Service\Company\Dto\SaveCompanyResponse', $response);
    }
}
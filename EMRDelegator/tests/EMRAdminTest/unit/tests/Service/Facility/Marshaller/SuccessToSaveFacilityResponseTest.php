<?php
namespace EMRAdminTest\unit\tests\Service\Facility\Marshaller;

use EMRAdmin\Service\Facility\Dto\SaveFacilityResponse;
use EMRAdmin\Service\Facility\Marshaller\SuccessToSaveFacilityResponse;
use EMRCore\PrototypeFactory;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use stdClass;
use InvalidArgumentException;
use EMRCore\Contact\Address\Dto\Address;
use EMRCore\Contact\Telephone\Dto\Telephone;
use EMRCore\Contact\Fax\Dto\Fax;
use Zend\ServiceManager\ServiceLocatorInterface;

class SuccessToSaveFacilityResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SuccessToSaveFacilityResponse
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new SuccessToSaveFacilityResponse;
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
    public function testNotMarshalsDueToResponseNotSuccessful()
    {
        $this->marshaller->marshall(array(
            'success' => false,
        ));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToMissingFacilityData()
    {
        $this->marshaller->marshall(array(
            'success' => true,
        ));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPayloadIsAnInstanceOfstdClass()
    {        
        $success = new Success();
        $success->setPayload( array(
            'success' => true,
            'facility' => (object) array(
                'stuff' => 'things',
            ),
        ));

        $response = $this->marshaller->marshall($success);
    }
    
        /**
     * @expectedException \InvalidArgumentException
     */
    public function testPayloadHaveFacilityProperty()
    {        
        $success = new Success();
        $success->setPayload( array(
            'success' => true,
        ));

        $response = $this->marshaller->marshall($success);
    }
    
     /**
     * @expectedException \InvalidArgumentException
     */
    public function testPayloadFacilityPropertyIsAnInstanceOfstdClass()
    {        
        $success = new Success();
        $success->setPayload((object) array(
            'success' => true,
            'facility' => array(
                'stuff' => 'things',
            ),
        ));

        $response = $this->marshaller->marshall($success);
    }
    
    /*
     *Test returned $response has the apropiate instances and itself is an SaveFacilityResponse instance
     */
    public function testSetBillingAddressInformation()
    {
        /** @var ServiceLocatorInterface|PHPUnit_Framework_MockObject_MockObject $serviceLocator */
        $serviceLocator = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->once())->method('get')
            ->with($this->equalTo('EMRAdmin\Service\Facility\Dto\SaveFacilityResponse'))
            ->will($this->returnValue(new SaveFacilityResponse));

        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);        
        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name)
                {
                    switch ($name)
                    {
                        case 'EMRCore\Contact\Address\Dto\Address':
                            return new Address;
                            break;
                        case 'EMRCore\Contact\Telephone\Dto\Telephone':
                            return new Telephone;
                            break;
                        case 'EMRCore\Contact\Fax\Dto\Fax';
                            return new Fax;
                            break;
                        default:
                            throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                            break;
                    }
                }));

        $this->marshaller->setServiceLocator($serviceLocator);
        $this->marshaller->setprototypeFactory($prototypeFactory);
        
        $success = new Success();
        $success->setPayload((object) array(
            'success' => true,
            'facility' => (object) array(
                'id' => 1,
                'name'=> 'asdf',
                'addressId'=> 1,
                'addressStreet'=> 'dsfg',
                'addressCity'=> 'dfsgfdg',
                'addressStateProvince'=> 'AZ',
                'addressPostalCode'=> 345534444,
                'addressCountryId'=> 'US',
                'phoneLocalNumber'=> 1234567,
                'phoneAreaCode'=> '123',
                'faxLocalNumber'=> 9876541,
                'faxAreaCode'=> 134,
                'contactName'=> 'gfdhgf',
                'employerId'=> 123456789,
                'nationalProviderId'=> 1234567890,
                'status'=> 'A',
                'companyId'=> 1,
                'timeZone'=> 'US/Pacific',
                'pricingId'=> 2,
                'valueAddedPackageId'=> 0,
                'billingType'=> 1,
                'billingDay'=> 23,
                'addressOtherDesignation'=> 'sthdfghfg',
                'address2Id'=> 2,
                'address2Street'=> 'dghhgf',
                'address2OtherDesignation'=> 'fdsgfsdgfderretrew',
                'address2City'=> 'gfhdfh',
                'address2StateProvince'=> 'BC',
                'address2PostalCode'=> 35465345,
                'address2CountryId'=> 'CA',
                'billingAddressId' => 45,
                'billingAddressStreet' => 'sdfgfdsgfdg',
                'billingAddressOtherDesignation' => 'fsdgfsdgfdsg',
                'billingAddressCity' => 'fsdgdfsh',
                'billingAddressStateProvince' => 'AL',
                'billingAddressPostalCode' => 435646365,
                'billingAddressCountryId' => 'US',
                'phoneId'=> 23,
                'phone2Id'=> 12,
                'phone2AreaCode'=> 546,
                'phone2LocalNumber'=> 3465456,
                'faxId'=> 1,
                'fax2Id'=> 1,
                'fax2AreaCode'=> 345,
                'fax2LocalNumber'=> 7654567,
                'nameOtherDesignation'=> 'sfdgsfdg',
                'webSiteAddress'=> 'dfghgdfh',
                'name2'=> 'dfsgfsdg',
                'webSiteAddress2'=> 'fdgfd',
                'contactName2'=> 'regdf',
                'therapistLicenseCount'=> 3443,
                'therapistAssistantLicenseCount'=> 6457,
                'clericalLicenseCount'=> 45364,
                'studentLicenseCount'=> 3456,
                'agentLicenseCount'=> 4356,
                'startDate'=> '2013-05-22T14=>24Z',
                'bfmType'=> 43,
                'bfmApiKey'=> 'dfsg',
                'bfmUserName'=> 'fdsghfd',
                'bfmGroupName'=> 'sdfgd',
                'bfmPassword'=> 'sfdg',
                'bfmEffectiveDateTime'=> '2013-05-22T14=>24Z',
                'bfmTerminateDateTime'=> '2012-05-22T14=>24Z',
                'bfmExternalId'=> 2,
                'bfmTimeToLiveSeconds'=> 2,
            ),
        ));
        
        $response = $this->marshaller->marshall($success);
        
         $this->assertInstanceOf('EMRAdmin\Service\Facility\Dto\SaveFacilityResponse', $response);
         $this->assertInstanceOf('EMRCore\Contact\Address\Dto\Address', $response->getAddress());
         $this->assertInstanceOf('EMRCore\Contact\Telephone\Dto\Telephone', $response->getTelephone());
         $this->assertInstanceOf('EMRCore\Contact\Fax\Dto\Fax', $response->getFax());
    }   
}
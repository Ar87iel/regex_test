<?php

namespace EMRAdminTest\unit\tests\Service\Facility\Marshaller;

use EMRAdmin\Service\Facility\Dto\Facility;
use EMRAdmin\Service\Facility\Marshaller\FacilityToArray;
use EMRCore\Contact\Address\Dto\Address;
use EMRCore\Contact\Fax\Dto\Fax;
use EMRCore\Contact\Telephone\Dto\Telephone;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class FacilityToArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var FacilityToArray
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new FacilityToArray;
    }
    
     /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidItemType()
    {
        $this->marshaller->marshall(new stdClass);
    }
    
    public function testFacilityToArray()
    {
     
        /* @var Address $address */
        $address = new Address;
        
        /* @var Address $address2 */
        $address2 =  new Address;

        /** @var Telephone $phone */
        $phone = new Telephone;

        /** @var Fax $fax */
        $fax = new Fax;

        /** @var Facility $facility */
        $facility = new Facility;
        
        $facility->setId(1);
        $facility->setName('namee');
        $facility->setContactName('asfdgdf');
        $facility->setEmployerID('12');
        $facility->setNationalProviderId('123');
        $facility->setStatus('A');
        $facility->setCompanyId(34);
        $facility->setTimeZone('56');
        $facility->setPricingId(1);
        $facility->setValueAddedPackageId(1);
        $facility->setBillingType(1);
        $facility->setBillingDay(22);
        
        $address->setId(1);
        $address->setCity('CA');
        $address->setStreet('fdsgsfd');
        $address->setStateProvince('dfghdgf');
        $address->setPostalCode(987654321);
        $address->setCountryId('US');
        
        $address2->setId(2);
        $address2->setCity('zxcvbn');
        $address2->setStreet('qwerty');
        $address2->setStateProvince('asdfg');
        $address2->setPostalCode(123456789);
        $address2->setCountryId('US');
        
        
        $facility->setAddress($address);
        
        $facility->setAddress2($address2);
        
        $phone->setId(1);
        $phone->setLocalNumber(1234567);
        $phone->setAreaCode(234);
        
        $facility->setTelephone($phone);
        
        $fax->setId(1);
        $fax->setLocalNumber(2345678);
        $fax->setAreaCode(345);
        
        $facility->setFax($fax);
        
        $response = $this->marshaller->marshall($facility);
        
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('addressStreet', $response);
        $this->assertArrayHasKey('address2Street', $response);
        $this->assertArrayHasKey('telephone', $response);
        $this->assertArrayHasKey('fax', $response);
    }
}

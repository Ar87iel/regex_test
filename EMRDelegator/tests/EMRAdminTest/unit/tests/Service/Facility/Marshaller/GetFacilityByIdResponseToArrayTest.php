<?php

namespace EMRAdminTest\unit\tests\Service\Facility\Marshaller;

use EMRAdmin\Service\Facility\Marshaller\GetFacilityByIdResponseToArray;
use EMRAdmin\Service\Facility\Dto\Facility;
use EMRAdmin\Service\Facility\Marshaller\FacilityToArray;
use EMRCore\Contact\Address\Dto\Address;
use EMRCore\Contact\Email\Dto\Email;
use EMRCore\Contact\Fax\Dto\Fax;
use EMRCore\Contact\Telephone\Dto\Telephone;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use stdClass;

class GetFacilityByIdResponseToArrayTest extends PHPUnit_Framework_TestCase
{
     /**
     * @var FacilityToArray
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new GetFacilityByIdResponseToArray;
    }
    
    public function testGetFacilityByIdResponseToArray()
    {

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
        $this->assertArrayHasKey('phoneLocalNumber', $response);
        $this->assertArrayHasKey('faxLocalNumber', $response);
    }
     
    
}
<?php
namespace EMRAdminTest\unit\tests\Service\Company\Marshaller;

use EMRAdmin\Service\Company\Dto\Company;
use EMRAdmin\Service\Company\Marshaller\CompanyToArray;
use EMRCore\Contact\Address\Dto\Address;
use EMRCore\Contact\Email\Dto\Email;
use EMRCore\Contact\Telephone\Dto\Telephone;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use stdClass;

class CompanyToArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CompanyToArray
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new CompanyToArray;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidItemType()
    {
        $this->marshaller->marshall(new stdClass);
    }

    public function testMarshalsCompany()
    {
        $company = new Company();
        $company->setId(1);
        $company->setName('my company name');
        $company->setAccountTypeId(3);
        $company->setOnlineStatus('on');
        $company->setClusterId(3);
        $company->setMigrationStatus('nope');

        $marshalled = $this->marshaller->marshall($company);

        $this->assertArrayHasKey('id', $marshalled);
        $this->assertSame($company->getId(), $marshalled['id']);
        $this->assertSame($company->getName(), $marshalled['name']);
        $this->assertSame($company->getAccountTypeId(), $marshalled['accountType']);
        $this->assertSame($company->getOnlineStatus(), $marshalled['onlineStatus']);
        $this->assertSame($company->getClusterId(), $marshalled['clusterId']);
        $this->assertSame($company->getMigrationStatus(), $marshalled['migrationStatus']);

        $this->assertArrayHasKey('addressId', $marshalled);
        $this->assertNull($marshalled['addressId']);

        $this->assertArrayHasKey('phoneId', $marshalled);
        $this->assertNull($marshalled['phoneId']);

        $this->assertArrayHasKey('emailId', $marshalled);
        $this->assertNull($marshalled['emailId']);
    }

    public function testMarshalsCompanyAddress()
    {
        $address = new Address;
        $address->setId(1);
        $address->setStreet('1324 street st.');
        $address->setOtherDesignation('other stuff');
        $address->setCity('Who Vil');
        $address->setPostalCode('832934');
        $address->setStateProvince('BO');
        $address->setCountryId('US');
        $address->setComments('my comment');

        $company = new Company;
        $company->setAddress($address);

        $marshalled = $this->marshaller->marshall($company);

        $this->assertSame($address->getId(), $marshalled['addressId']);
        $this->assertSame($address->getStreet(), $marshalled['addressStreet']);
        $this->assertSame($address->getOtherDesignation(), $marshalled['addressOtherDesignation']);
        $this->assertSame($address->getCity(), $marshalled['addressCity']);
        $this->assertSame($address->getPostalCode(), $marshalled['addressPostalCode']);
        $this->assertSame($address->getStateProvince(), $marshalled['addressStateProvince']);
        $this->assertSame($address->getCountryId(), $marshalled['addressCountry']);
        $this->assertSame($address->getComments(), $marshalled['addressAdditionalInfo']);
    }

    public function testMarshalsCompanyPhone()
    {
        $phone = new Telephone;
        $phone->setId(1);
        $phone->setAreaCode('234');
        $phone->setLocalNumber('3423432');
        $phone->setExtension('89');
        $phone->setComments('oirewoi');

        $company = new Company;
        $company->setTelephone($phone);

        $marshalled = $this->marshaller->marshall($company);

        $this->assertSame($phone->getId(), $marshalled['phoneId']);
        $this->assertSame($phone->getAreaCode(), $marshalled['phoneAreaCode']);
        $this->assertSame($phone->getLocalNumber(), $marshalled['phoneLocalNumber']);
        $this->assertSame($phone->getExtension(), $marshalled['phoneExtension']);
        $this->assertSame($phone->getComments(), $marshalled['phoneAdditionalInfo']);
    }

    public function testMarshalsCompanyEmail()
    {
        $email = new Email;
        $email->setId(1);
        $email->setEmail('wer@ieiw.com');
        $email->setComments('werqwer');

        $company = new Company;
        $company->setEmail($email);

        $marshalled = $this->marshaller->marshall($company);

        $this->assertSame($email->getId(), $marshalled['emailId']);
        $this->assertSame($email->getEmail(), $marshalled['email']);
        $this->assertSame($email->getComments(), $marshalled['emailAdditionalInfo']);
    }
}
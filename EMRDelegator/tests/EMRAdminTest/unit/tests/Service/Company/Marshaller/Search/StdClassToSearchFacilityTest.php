<?php

namespace EMRAdminTest\unit\tests\Service\Company\Marshaller\Search;

use EMRAdmin\Service\Company\Dao\Search\SearchFacility;
use EMRAdmin\Service\Company\Dto\Search\LicenseCollection;
use EMRAdmin\Service\Company\Marshaller\Search\StdClassToSearchFacility;
use EMRCore\Contact\Address\Dto\Address;
use EMRCore\Contact\Telephone\Dto\Telephone;
use EMRCore\PrototypeFactory;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class StdClassToSearchFacilityTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var StdClassToSearchFacility
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new StdClassToSearchFacility();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidItemType()
    {
        $this->marshaller->marshall(array());
    }

    public function testMarshalsFacility()
    {
        $id = 1;

        $item = new \stdClass();
        $item->id = $id;
        $item->name = null;
        $item->addressStreet = "asd";
        $item->addressOtherDesignation = "asd";
        $item->addressCity = "asd";
        $item->addressStateProvince = "asd";
        $item->addressCountryId = "asd";
        $item->addressPostalCode = "asd";
        $item->phoneAreaCode = 123;
        $item->phoneLocalNumber = 1234567;
        $item->licenses = array();
		$item->companyId = 1;

        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);
        $this->marshaller->setPrototypeFactory($prototypeFactory);

        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name)
                {
                    if ($name === 'EMRAdmin\Service\Company\Dao\Search\SearchFacility')
                    {
                        return new SearchFacility;
                    }
                    if ($name == 'EMRCore\Contact\Address\Dto\Address')
                    {
                        return new Address();
                    }
                    if ($name == 'EMRCore\Contact\Telephone\Dto\Telephone')
                    {
                        return new Telephone();
                    }

                    throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                }));

        $licenseMarshaller = $this->getMock('EMRAdmin\Service\Company\Marshaller\Search\StdClassToLicenseCollection');
        $licenseMarshaller->expects($this->once())->method('marshall')
                ->withAnyParameters()->will($this->returnValue(new LicenseCollection));

		$facility = $this->getMock('EMRAdmin\Service\Facility\Facility');

        /** @var ServiceLocatorInterface|PHPUnit_Framework_MockObject_MockObject $serviceLocator */
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->marshaller->setServiceLocator($serviceLocator);

        $serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(
                function($name) use ($licenseMarshaller, $facility)
                {

                    if ($name === 'EMRAdmin\Service\Company\Marshaller\Search\StdClassToLicenseCollection')
                    {
                        return $licenseMarshaller;
                    }
                    if ($name === 'EMRAdmin\Service\Facility\Facility')
                    {
                        return $facility;
                    }

                    throw new InvalidArgumentException("Mocked ServiceLocatorInterface cannot provide [$name].");
                }));

        $company = $this->marshaller->marshall($item);

        $this->assertSame($id, $company->getId());
    }

}
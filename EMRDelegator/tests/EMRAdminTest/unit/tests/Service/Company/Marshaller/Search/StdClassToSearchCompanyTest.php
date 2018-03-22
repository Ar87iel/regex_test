<?php

namespace EMRAdminTest\unit\tests\Service\Company\Marshaller\Search;

use EMRAdmin\Service\Company\Dto\Search\SearchCompany;
use EMRAdmin\Service\Company\Dto\Search\SearchFacilityCollection;
use EMRAdmin\Service\Company\Marshaller\Search\StdClassToSearchCompany;
use EMRAdmin\Service\Company\Marshaller\Search\StdClassToSearchFacility;
use EMRCore\Contact\Address\Dto\Address;
use EMRCore\Contact\Telephone\Dto\Telephone;
use EMRCore\PrototypeFactory;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class StdClassToSearchCompanyTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var StdClassToSearchCompany
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new StdClassToSearchCompany;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidItemType()
    {
        $this->marshaller->marshall(array());
    }

    public function testMarshalsCompany()
    {
        $id = 1;

        $item = new \stdClass();
        $item->id = $id;
        $item->name = null;
        $item->addressId = 1;
        $item->addressStreet = "asd";
        $item->addressOtherDesignation = "asd";
        $item->addressCity = "asd";
        $item->addressStateProvince = "asd";
        $item->addressCountry = "asd";
        $item->addressPostalCode = 12345678;
        $item->phoneId = 1;
        $item->phoneAreaCode = 123;
        $item->phoneLocalNumber = 1234567;
        $item->phoneExtension = '123';
        $item->facilities = array();

        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);
        $this->marshaller->setPrototypeFactory($prototypeFactory);

        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name)
                {
                    if ($name === 'EMRAdmin\Service\Company\Dto\Search\SearchCompany')
                    {
                        return new SearchCompany;
                    }

                    if ($name === 'EMRAdmin\Service\Company\Dto\Search\SearchFacilityCollection')
                    {
                        return new SearchFacilityCollection;
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

        /** @var ServiceLocatorInterface|PHPUnit_Framework_MockObject_MockObject $serviceLocator */
        $serviceLocator = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->marshaller->setServiceLocator($serviceLocator);

        $serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(
                function($name)
                {
                    if ($name === 'EMRAdmin\Service\Company\Marshaller\Search\StdClassToSearchFacility')
                    {
                        return new StdClassToSearchFacility;
                    }

                    throw new InvalidArgumentException("Mocked ServiceLocatorInterface cannot provide [$name].");
                }));

        $company = $this->marshaller->marshall($item);

        $this->assertSame($id, $company->getId());
    }

}
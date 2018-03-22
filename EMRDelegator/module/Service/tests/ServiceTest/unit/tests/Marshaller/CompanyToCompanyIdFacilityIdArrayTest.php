<?php
namespace ServiceTest\unit\tests\Marshaller;

use EMRCoreTest\Helper\Reflection;
use EMRDelegator\Model\Company;
use EMRDelegator\Model\Facility;
use PHPUnit_Framework_TestCase;
use Service\Controller\Marshaller\CompanyToCompanyIdFacilityIdArray;
use Service\Controller\Marshaller\FacilityToFacilityIdArray;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class CompanyToCompanyIdFacilityIdArrayTest extends PHPUnit_Framework_TestCase
{
    private function getMarshaller()
    {
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->once())->method('get')
            ->with('Service\Controller\Marshaller\FacilityToFacilityIdArray')
            ->will($this->returnValue(new FacilityToFacilityIdArray));

        $marshaller = new CompanyToCompanyIdFacilityIdArray;
        $marshaller->setServiceLocator($serviceLocator);

        return $marshaller;
    }

    public function testMarshalsCompanyId()
    {
        $company = new Company;
        $company->setCompanyId(1);

        $marshaller = $this->getMarshaller();

        $actual = Reflection::invoke($marshaller, 'marshalCompanyId', array($company));

        $expected = array(
            'id' => $company->getCompanyId(),
        );

        $this->assertSame($expected, $actual);
    }

    public function testMarshalsCompanyIds()
    {
        $company = new Company;
        $company->setCompanyId(1);

        $companies = array(
            $company,
        );

        $marshaller = $this->getMarshaller();

        $actual = Reflection::invoke($marshaller, 'marshalCompanyIds', array($companies));

        $expected = array(
            array(
                'id' => $company->getCompanyId()
            ),
        );

        $this->assertSame($expected, $actual);
    }

    public function testMarshalsCompanyIdsWithFacilityIds()
    {
        $facility = new Facility;
        $facility->setFacilityId(1);

        $company = new Company;
        $company->setCompanyId(1);

        $company->addFacility($facility);

        $companies = array(
            $company,
        );

        $marshaller = $this->getMarshaller();

        $actual = Reflection::invoke($marshaller, 'marshalCompanyIds', array($companies));

        $expected = array(
            array(
                'id' => $company->getCompanyId(),
                'facilities' => array(
                    array(
                        'id' => $facility->getFacilityId(),
                    ),
                ),
            ),
        );

        $this->assertSame($expected, $actual);
    }

    public function testMarshalCallsMarshalCompanyIds()
    {
        $company = new Company;
        $company->setCompanyId(1);

        $companies = array(
            $company,
        );

        $marshaller = $this->getMock('Service\Controller\Marshaller\CompanyToCompanyIdFacilityIdArray', array('marshalCompanyIds'));

        $marshaller->expects($this->once())->method('marshalCompanyIds')->with($companies);

        /** @var CompanyToCompanyIdFacilityIdArray $marshaller */
        $marshaller->marshall($companies);
    }
}
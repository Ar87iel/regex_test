<?php
namespace ServiceTest\unit\tests\Marshaller;

use EMRCoreTest\Helper\Reflection;
use EMRDelegator\Model\Company;
use PHPUnit_Framework_TestCase;
use Service\Controller\Marshaller\CompanyToCompanyIdArray;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class CompanyToCompanyIdArrayTest extends PHPUnit_Framework_TestCase
{
    public function testMarshalsCompanyId()
    {
        $company = new Company;
        $company->setCompanyId(1);

        $marshaller = new CompanyToCompanyIdArray;

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

        $marshaller = new CompanyToCompanyIdArray;

        $actual = Reflection::invoke($marshaller, 'marshalCompanyIds', array($companies));

        $expected = array(
            array(
                'id' => $company->getCompanyId()
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

        $marshaller = $this->getMock('Service\Controller\Marshaller\CompanyToCompanyIdArray', array('marshalCompanyIds'));

        $marshaller->expects($this->once())->method('marshalCompanyIds')->with($companies);

        /** @var CompanyToCompanyIdArray $marshaller */
        $marshaller->marshall($companies);
    }
}
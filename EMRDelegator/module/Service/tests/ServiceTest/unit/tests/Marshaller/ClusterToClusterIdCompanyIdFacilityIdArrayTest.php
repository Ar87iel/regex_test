<?php
namespace ServiceTest\unit\tests\Marshaller;

use EMRCoreTest\Helper\Reflection;
use EMRDelegator\Model\Cluster;
use EMRDelegator\Model\Company;
use PHPUnit_Framework_TestCase;
use Service\Controller\Marshaller\ClusterToClusterIdCompanyIdFacilityIdArray;
use Service\Controller\Marshaller\CompanyToCompanyIdFacilityIdArray;
use Service\Controller\Marshaller\FacilityToFacilityIdArray;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class ClusterToClusterIdCompanyIdFacilityIdArrayTest extends PHPUnit_Framework_TestCase
{
    private function getMarshaller()
    {
        $companyMarshaller = new CompanyToCompanyIdFacilityIdArray;

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->any())->method('get')
            ->will($this->returnValueMap(array(
                array('Service\Controller\Marshaller\CompanyToCompanyIdFacilityIdArray', $companyMarshaller),
                array('Service\Controller\Marshaller\FacilityToFacilityIdArray', new FacilityToFacilityIdArray),
            )));

        $companyMarshaller->setServiceLocator($serviceLocator);

        $marshaller = new ClusterToClusterIdCompanyIdFacilityIdArray;
        $marshaller->setServiceLocator($serviceLocator);

        return $marshaller;
    }

    public function testMarshalsClusterId()
    {
        $cluster = new Cluster;
        $cluster->setClusterId(1);

        $marshaller = $this->getMarshaller();

        $actual = Reflection::invoke($marshaller, 'marshalClusterId', array($cluster));

        $expected = array(
            'id' => $cluster->getClusterId(),
        );

        $this->assertSame($expected, $actual);
    }

    public function testMarshalsClusterIds()
    {
        $cluster = new Cluster;
        $cluster->setClusterId(1);

        $clusters = array(
            $cluster,
        );

        $marshaller = $this->getMarshaller();

        $actual = Reflection::invoke($marshaller, 'marshalClusterIds', array($clusters));

        $expected = array(
            array(
                'id' => $cluster->getClusterId()
            ),
        );

        $this->assertSame($expected, $actual);
    }

    public function testMarshalsClusterIdsWithCompanyIds()
    {
        $company = new Company;
        $company->setCompanyId(1);

        $cluster = new Cluster;
        $cluster->setClusterId(1);

        $cluster->addCompany($company);

        $clusters = array(
            $cluster,
        );

        $marshaller = $this->getMarshaller();

        $actual = Reflection::invoke($marshaller, 'marshalClusterIds', array($clusters));

        $expected = array(
            array(
                'id' => $cluster->getClusterId(),
                'companies' => array(
                    array(
                        'id' => $company->getCompanyId(),
                    ),
                ),
            ),
        );

        $this->assertSame($expected, $actual);
    }

    public function testMarshalCallsMarshalClusterIds()
    {
        $cluster = new Cluster;
        $cluster->setClusterId(1);

        $clusters = array(
            $cluster,
        );

        $marshaller = $this->getMock('Service\Controller\Marshaller\ClusterToClusterIdCompanyIdFacilityIdArray', array('marshalClusterIds'));

        $marshaller->expects($this->once())->method('marshalClusterIds')->with($clusters);

        /** @var ClusterToClusterIdCompanyIdFacilityIdArray $marshaller */
        $marshaller->marshall($clusters);
    }
}
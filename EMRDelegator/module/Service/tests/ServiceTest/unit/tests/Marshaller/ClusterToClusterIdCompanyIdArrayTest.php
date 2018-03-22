<?php
namespace ServiceTest\unit\tests\Marshaller;

use EMRCoreTest\Helper\Reflection;
use EMRDelegator\Model\Cluster;
use EMRDelegator\Model\Company;
use PHPUnit_Framework_TestCase;
use Service\Controller\Marshaller\ClusterToClusterIdCompanyIdArray;
use Service\Controller\Marshaller\CompanyToCompanyIdArray;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class ClusterToClusterIdCompanyIdArrayTest extends PHPUnit_Framework_TestCase
{
    private function getMarshaller()
    {
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->once())->method('get')
            ->with('Service\Controller\Marshaller\CompanyToCompanyIdArray')
            ->will($this->returnValue(new CompanyToCompanyIdArray));

        $marshaller = new ClusterToClusterIdCompanyIdArray;
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

        $marshaller = $this->getMock('Service\Controller\Marshaller\ClusterToClusterIdCompanyIdArray', array('marshalClusterIds'));

        $marshaller->expects($this->once())->method('marshalClusterIds')->with($clusters);

        /** @var ClusterToClusterIdCompanyIdArray $marshaller */
        $marshaller->marshall($clusters);
    }
}
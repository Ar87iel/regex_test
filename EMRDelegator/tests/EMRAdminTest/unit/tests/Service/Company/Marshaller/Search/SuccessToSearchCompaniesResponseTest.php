<?php

namespace EMRAdminTest\unit\tests\Service\Company\Marshaller\Search;

use EMRAdmin\Service\Company\Dto\Search\SearchCompany;
use EMRAdmin\Service\Company\Dto\Search\SearchCompanyCollection;
use EMRAdmin\Service\Company\Marshaller\Search\SuccessToSearchCompaniesResponse;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
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
class SuccessToSearchCompaniesResponseTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SuccessToSearchCompaniesResponse
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new SuccessToSearchCompaniesResponse;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidItemType()
    {
        $this->marshaller->marshall(array());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidPayloadType()
    {
        $success = new Success;
        $success->setPayload(array());

        $this->marshaller->marshall($success);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidPayloadContents()
    {
        $success = new Success;
        $success->setPayload((object) array());

        $this->marshaller->marshall($success);
    }

    public function testMarshalsCompanies()
    {
        $id = 1;

        $company = (object) array(
                    'id' => $id,
        );


        $item = (object) array(
                    'companies' => array(
                        (object) array('company' => $company),
                    ),
        );

        $success = new Success;
        $success->setPayload($item);

        $companyMarshaller = $this->getMock('EMRAdmin\Service\Company\Marshaller\Search\StdClassToSearchCompany');
        $companyMarshaller->expects($this->once())
            ->method('marshall')
            ->withAnyParameters()
            ->will($this->returnValue(new SearchCompany));

        /** @var ServiceLocatorInterface|PHPUnit_Framework_MockObject_MockObject $serviceLocator */
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->marshaller->setServiceLocator($serviceLocator);

        $serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(
                function($name) use ($companyMarshaller)
                {
                    if ($name === 'EMRAdmin\Service\Company\Marshaller\Search\StdClassToSearchCompany')
                    {
                        return $companyMarshaller;
                    }

                    throw new InvalidArgumentException("Mocked ServiceLocatorInterface cannot provide [$name].");
                }));

        $collection = $this->marshaller->marshall($success);

        $this->assertCount(1, $collection);
    }

}
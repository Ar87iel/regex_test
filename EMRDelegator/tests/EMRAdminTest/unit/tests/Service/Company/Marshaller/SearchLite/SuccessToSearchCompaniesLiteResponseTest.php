<?php

namespace EMRAdminTest\unit\tests\Service\Company\Marshaller\SearchLite;

use EMRCore\PrototypeFactory;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\Company\Marshaller\Search\SuccessToSearchCompaniesLiteResponse;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use InvalidArgumentException;
use EMRAdmin\Service\Company\Dto\SearchLite\SearchCompanyLiteCollection;
use EMRAdmin\Service\Company\Dto\SearchLite\SearchFacilityLiteCollection;
use EMRAdmin\Service\Company\Dto\Search\SearchCompanyLite;
use EMRAdmin\Service\Company\Dto\SearchLite\SearchFacilityLite;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class SuccessToSearchCompaniesLiteResponseTest extends PHPUnit_Framework_TestCase
{

    public function testMarshallMethod()
    {
        $payload = (object) array(
                    'companies' => array(
                        (object) array(
                            'company' => (object) array(
                                'companyId' => '123',
                                'companyName' => 'asd',
                            ),
                            'facilities' => array(
                                (object) array(
                                    'facilityId' => '1',
                                    'name' => 'asd',
                                )
                            )
                        )
                    )
        );

        $success = new Success;
        $success->setPayload($payload);

        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);
        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name)
                {
                    if ($name == 'EMRAdmin\Service\Company\Dto\SearchLite\SearchCompanyLiteCollection')
                    {
                        return new SearchCompanyLiteCollection;
                    }
                    if ($name == 'EMRAdmin\Service\Company\Dto\SearchLite\SearchFacilityLiteCollection')
                    {
                        return new SearchFacilityLiteCollection;
                    }
                    if ($name == 'EMRAdmin\Service\Company\Dto\Search\SearchCompanyLite')
                    {
                        return new SearchCompanyLite;
                    }
                    if ($name == 'EMRAdmin\Service\Company\Dto\SearchLite\SearchFacilityLite')
                    {
                        return new SearchFacilityLite;
                    }


                    throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                }));

        $marshaller = new SuccessToSearchCompaniesLiteResponse;
        $marshaller->setPrototypeFactory($prototypeFactory);
        $response = $marshaller->marshall($success);

        $this->assertInstanceOf('EMRAdmin\Service\Company\Dto\SearchLite\SearchCompanyLiteCollection', $response);
        $companies = $response->getElements();

        $this->assertInstanceOf('EMRAdmin\Service\Company\Dto\Search\SearchCompanyLite', $companies[0]);
        $this->assertInstanceOf('EMRAdmin\Service\Company\Dto\SearchLite\SearchFacilityLiteCollection', $companies[0]->getFacilities());

        $facilities = $companies[0]->getFacilities()->getElements();
        $this->assertInstanceOf('EMRAdmin\Service\Company\Dto\SearchLite\SearchFacilityLite', $facilities[0]);
    }

}
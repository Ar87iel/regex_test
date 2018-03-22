<?php

namespace EMRAdminTest\unit\tests\Service\Company\Marshaller;

use EMRAdmin\Service\Company\Marshaller\GetCompaniesResponseToArray;
use EMRAdmin\Service\Company\Dto\GetCompaniesResponse;
use EMRAdmin\Service\Company\Dto\CompanyFromDelegator;
use PHPUnit_Framework_TestCase;
use InvalidArgumentException;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class GetCompaniesResponseToArrayTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var GetCompaniesResponseToArray 
     */
    private $marshaller;

    public function setup()
    {
        $this->marshaller = new GetCompaniesResponseToArray();
    }

    /**
     * Test that the marshalling will not occur due to an invalid type as an argument to the marshaller.
     * 
     * @expectedException InvalidArgumentException
     */
    public function testNoMarshallingBecauseInvalidType()
    {
        $this->marshaller->marshall(array());
    }

    /**
     * Test that marshalling occurs apropriately when the correct data is privided
     */
    public function testCorrectMarshalling()
    {

        $companyId = 50;
        $companyName = 'My company';

        $companyCollection = new GetCompaniesResponse();

        $companyFromDelegator = new CompanyFromDelegator();
        $companyFromDelegator->setClusterId(1);
        $companyFromDelegator->setCompanyId($companyId);
        $companyFromDelegator->setName($companyName);
        $companyFromDelegator->setOnlineStatus('Active');

        $companyCollection->push($companyFromDelegator);

        $marshalledCompany = $this->marshaller->marshall($companyCollection);

        $this->assertTrue(is_array($marshalledCompany), 'Asserting that the marshall result is an array');

        $this->assertArrayHasKey($companyId, $marshalledCompany, 'Asserting that the company Id exists in the marshalled
            Array');

        $this->assertSame($companyName, $marshalledCompany[$companyId], 'Asserting that the company name exists in the
            marshalled array');
    }

}
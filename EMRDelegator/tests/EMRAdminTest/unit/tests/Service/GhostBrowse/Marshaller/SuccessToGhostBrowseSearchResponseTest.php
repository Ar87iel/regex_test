<?php

namespace EMRAdminTest\unit\tests\Service\GhostBrowse\Marshaller;

use EMRAdmin\Service\GhostBrowse\Marshaller\Search\SuccessToGhostBrowseResponse;
use EMRAdmin\Service\GhostBrowse\Marshaller\Search\SuccessToGhostBrowseSearchResponse;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use \stdClass;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\Company\Dto\SearchLite\SearchCompanyLiteCollection;
use EMRAdmin\Service\Company\Dto\Search\SearchCompanyLite;
use EMRAdmin\Service\Company\Dto\SearchLite\SearchFacilityLiteCollection;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowserResponseFacility;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseUserCollection;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseUser;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class SuccessToGhostBrowseSearchResponseTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SuccessToGhostBrowseResponse
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new SuccessToGhostBrowseSearchResponse();
    }

    /**
     * Test that the marshaller will throw an exception when invoked with an unexpected data type parameter
     * 
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshallsDueToInvalidItemType()
    {
        $this->marshaller->marshall(array());
    }

    /**
     * Test that a success response is marshalled correctly to a ghost browse response object.
     */
    public function testMarshallSuccessToGhostBrowseSearchResponse()
    {
        //A fake User
        $user = array(
            (object) array(
                'id' => 1,
                'fullName' => "asd",
                'username' => 'asd',
                'emailAddress' => 'asdf@webpt.com',
                'relevance' => 1
        ));

        //A fake facility
        $facility = array(
            (object) array(
                'name' => "asd",
                'facilityId' => 1,
                'users' => $user,
            )
        );

        //A fake content
        $companies = array(
            'companies' => (object) array(
                'companyName' => 'asd',
                'companyId' => 1,
                'facilities' => $facility,
            )
        );

        //Create the success object to be marshalled
        $success = new Success();

        // Create the payload
        $payload = new stdClass();
        $payload->companies = $companies;

        // Add payload to the success object
        $success->setPayload($payload);

        $response = $this->marshaller->marshall($success);

        $this->assertInstanceOf('EMRAdmin\Service\Company\Dto\SearchLite\SearchCompanyLiteCollection', $response);
    }

}


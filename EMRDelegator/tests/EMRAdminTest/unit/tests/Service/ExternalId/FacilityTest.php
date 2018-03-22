<?php


namespace EMRAdminTest\unit\tests\Service\ExternalId;


use EMRAdmin\Service\ExternalId\Facility;
use Zend\Http\Client;
use Zend\Http\Response;

class FacilityTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Facility */
    private $sut;
    /** @var  string */
    private $baseUrl;
    /** @var  Client | \PHPUnit_Framework_MockObject_MockObject */
    private $client;

    public function setup(){
        parent::setup();
        $this->baseUrl = 'https:\\something.blah\extId';
        $this->client = $this->getMockBuilder('Zend\Http\Client')
            ->disableOriginalConstructor()
            ->setMethods(array('dispatch'))
            ->getMock();
        $this->sut = new Facility($this->client, $this->baseUrl);
    }

    public function testSetIntegrationToken(){
        $facilityId = 34;
        $intToken = 'someToken';
        $extId = 'someExtId';

        $response = new Response();
        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->setContent(json_encode(array($key = 'resultFormat'=> $val = 'stdClass')));

        // Test client calls dispatch
        $this->client->expects(self::once())->method('dispatch')->willReturn($response);

        $result = $this->sut->setIntegrationToken($facilityId, $intToken, $extId);

        // Test json response is translated into object
        self::assertEquals($result->$key, $val);
    }

    public function testGetIntegrationToken(){
        $facilityId = 78687;

        $response = new Response();
        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->setContent(json_encode(array('integration_token'=> $intToken = 'abcd12345')));

        // Test client calls dispatch
        $this->client->expects(self::once())->method('dispatch')->willReturn($response);

        $result = $this->sut->getIntegrationToken($facilityId);

        // Test integration token is extracted from response
        self::assertEquals($intToken, $result);
    }
}

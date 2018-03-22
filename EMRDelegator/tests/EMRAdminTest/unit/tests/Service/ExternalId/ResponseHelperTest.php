<?php

namespace EmrUnitOfWorkMuncherTest\EmrPersistenceApiServices\ResponseHelper;

use EMRAdmin\Service\ExternalId\ResponseHelper;
use Zend\Http\Header\Accept;
use Zend\Http\Request;
use Zend\Http\Response as HttpResponse;
use Zend\Stdlib\Response;

class ResponseHelperTest extends \PHPUnit_Framework_TestCase
{
    /** @var ResponseHelperTest */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->sut = new ResponseHelperTest();
    }

    public function testDecodeJson()
    {
        $response = new HttpResponse();
        $response->setContent(json_encode(array('stuff' => 'things')));
        $actual = ResponseHelper::decodeJson($response);

        self::assertInstanceOf('\stdClass', $actual);
        self::assertTrue(property_exists($actual, 'stuff'));
        self::assertEquals('things', $actual->stuff);
    }

    /**
     * @expectedException \EMRAdmin\Service\ExternalId\Exception\RuntimeException
     */
    public function testDecodeInvalidJson()
    {
        $response = new HttpResponse();
        $response->setContent('stuff and things');
        ResponseHelper::decodeJson($response);
    }

    public function testGetStatusCode()
    {
        $response = new HttpResponse();

        $actual = ResponseHelper::getStatusCode($response);

        self::assertEquals(HttpResponse::STATUS_CODE_200, $actual);
    }

    public function testParseHttpJsonResponse()
    {
        $response = new HttpResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setStatusCode(HttpResponse::STATUS_CODE_200);
        $response->setContent(json_encode(array(
            'stuff' => 'things',
        )));

        $actual = ResponseHelper::parseJson($response);

        self::assertInstanceOf('\stdClass', $actual);
        self::assertTrue(property_exists($actual, 'stuff'));
        self::assertEquals('things', $actual->stuff);
    }

    public function testAcceptHeader()
    {
        $request = new Request();
        self::assertFalse($request->getHeaders()->has('Accept'));

        /** @var Accept $acceptHeader */
        $acceptHeader = $request->getHeader('Accept', null);
        if (!$acceptHeader) {
            $request->getHeaders()->addHeader($acceptHeader = new Accept());
        }
        self::assertTrue($request->getHeaders()->has('Accept'));

        $acceptHeader->addMediaType('application/json');
        self::assertContains('application/json', $acceptHeader->toString());

        $acceptHeader->addMediaType('application/hal+json');
        self::assertContains('application/json', $acceptHeader->toString());
        self::assertContains('application/hal+json', $acceptHeader->toString());
    }

    public function testGetStatusCodeFromNonHttpResponse()
    {
        $response = new Response();

        $actual = ResponseHelper::getStatusCode($response);

        self::assertNull($actual);
    }

    /**
     * @expectedException \EMRAdmin\Service\ExternalId\Exception\UnexpectedValueException
     */
    public function testParseJsonFromNonHttpResponse()
    {
        $response = new Response();

        ResponseHelper::parseJson($response);
    }
}

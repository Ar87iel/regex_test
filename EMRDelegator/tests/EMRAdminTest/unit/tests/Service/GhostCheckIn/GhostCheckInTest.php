<?php

namespace EMRAdminTest\unit\tests\Service\GhostBrowse\Dao;

use EMRAdmin\Service\GhostBrowse\Dao\Esb;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseRequest;
use EMRAdmin\Service\GhostBrowse\Marshaller\Search\SuccessToGhostBrowseResponse;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseCollection;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponse;
use EMRAdmin\Service\GhostBrowse\Marshaller\StdClassToSearchGhostBrowse;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseUserCollection;
use EMRAdmin\Service\GhostBrowse\Marshaller\Search\UserPayloadToUser;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseUser;
use EMRAdmin\Service\GhostBrowse\Dto\Search\UsersByCompanyId as UsersByCompanyIdDto;
use EMRAdmin\Service\GhostCheckIn\GhostCheckIn;
use EMRAdmin\Service\User\Ghost\Ghost;
use EMRCore\Config\Service\PrivateService\Esb\Dto\Route;
use EMRCore\Zend\Http\ClientWrapper;
use EMRCore\Zend\module\Service\src\Response\Parser\Json;
use EMRCoreTest\lib\PhpUnit\SingletonTestCaseHelper;
use EMRCore\EsbFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Uri;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Webmozart\PathUtil\Path;
use Zend\Http\Response;
use EMRAdmin\Service\Company\Dto\SearchLite\SearchCompanyLiteCollection;
use Zend\ServiceManager\ServiceLocatorInterface;

class GhostCheckInTest extends PHPUnit_Framework_TestCase
{
    const TESTING = 'testing';

    /** @var GhostCheckIn */
    public $sut;

    /** @var Client | PHPUnit_Framework_MockObject_MockObject */
    public $guzzleMock;

    /** @var LoggerInterface | PHPUnit_Framework_MockObject_MockObject */
    public $loggerMock;

    /** @var RequestException | PHPUnit_Framework_MockObject_MockObject */
    public $requestExceptionMock;

    /** @var ResponseInterface | PHPUnit_Framework_MockObject_MockObject */
    public $responseInterfaceMock;

    /** @var Uri | PHPUnit_Framework_MockObject_MockObject */
    public $uriMock;

    public $configMock = array();

    /**
     * set up the esb
     */
    public function setUp()
    {
        $this->guzzleMock            = self::getMockBuilder(\GuzzleHttp\Client::class)->disableOriginalConstructor()->createMock();
        $this->loggerMock            = self::getMockBuilder(\Psr\Log\LoggerInterface::class)->disableOriginalConstructor()->createMock();
        $this->uriMock               = self::getMockBuilder(Uri::class)->disableOriginalConstructor()->createMock();
        $this->requestExceptionMock  = self::getMockBuilder(RequestException::class)->disableOriginalConstructor()->createMock();
        $this->responseInterfaceMock = self::getMockBuilder(ResponseInterface::class)->disableOriginalConstructor()->createMock();
    }

    public function tearDown()
    {
        unset($this->sut);
        unset($this->configMock);
        unset($this->guzzleMock);
        unset($this->loggerMock);
        unset($this->uriMock);
        unset($this->requestExceptionMock);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRecordCheckInMissingBothTickeNumberAndJustificationThrowsInvalidArguementException()
    {
        $this->sut = new GhostCheckIn($this->guzzleMock, $this->loggerMock, $this->uriMock, []);
        $this->sut->recordCheckIn(123, 234, 345, 'test');
    }

    public function testRecordCheckInSuccessful()
    {
        $ghostUserIdentityId = 123;
        $ghostAsIdentityId   = 234;
        $facilityId          = 345;
        $productName         = 'WEBPT_EMR';
        $ticketNumber        = 456;
        $justification       = "i'm justice!";

        $query = [
            'ticket_number'             => $ticketNumber,
            'justification'             => $justification,
            'ghosting_user_identity_id' => $ghostUserIdentityId,
            'ghosting_as_identity_id'   => $ghostAsIdentityId,
            'facility_id'               => $facilityId,
            'product_name'              => $productName,
        ];
        $this->responseInterfaceMock->expects(self::once())->method('getStatusCode');
        $this->guzzleMock->expects(self::once())
                         ->method('request')
                         ->with(
                             'POST',
                             $this->uriMock,
                             [
                                 'json'    => $query
                             ]
                         )
                         ->will(self::returnValue($this->responseInterfaceMock));
        $this->sut = new GhostCheckIn($this->guzzleMock, $this->loggerMock, $this->uriMock);
        $this->sut->recordCheckIn($ghostUserIdentityId, $ghostAsIdentityId, $facilityId, $productName, $ticketNumber, $justification);
    }

    public function testRecordCheckInSuccessfulWithHeaders()
    {
        $ghostUserIdentityId = 123;
        $ghostAsIdentityId   = 234;
        $facilityId          = 345;
        $productName         = 'WEBPT_EMR';
        $ticketNumber        = 456;
        $justification       = "i'm justice!";

        $query = [
            'ticket_number'             => $ticketNumber,
            'justification'             => $justification,
            'ghosting_user_identity_id' => $ghostUserIdentityId,
            'ghosting_as_identity_id'   => $ghostAsIdentityId,
            'facility_id'               => $facilityId,
            'product_name'              => $productName,
        ];
        $this->responseInterfaceMock->expects(self::once())->method('getStatusCode');
        $this->guzzleMock->expects(self::once())
                         ->method('request')
                         ->with(
                             'POST',
                             $this->uriMock,
                             ['json' => $query]
                         )
                         ->will(self::returnValue($this->responseInterfaceMock));
        $this->sut = new GhostCheckIn($this->guzzleMock, $this->loggerMock, $this->uriMock);
        $this->sut->recordCheckIn($ghostUserIdentityId, $ghostAsIdentityId, $facilityId, $productName, $ticketNumber, $justification);
    }
}

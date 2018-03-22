<?php

namespace EMRAdminTest\unit\tests\Service\GhostBrowse\Dao;

use EMRAdmin\Service\GhostCheckIn\GhostCheckIn;
use EMRAdmin\Service\GhostCheckIn\GhostCheckInFactory;
use GuzzleHttp\Client as GuzzleClient;
use PHPUnit_Framework_TestCase;
use Psr\Log\LoggerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GhostCheckInFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var ServiceLocatorInterface | \PHPUnit_Framework_MockObject_MockObject */
    public $serviceLocatorInterfaceMock;

    public function setUp() {
        $this->serviceLocatorInterfaceMock = self::getMockBuilder(ServiceLocatorInterface::class)
                                                    ->disableOriginalConstructor()
                                                    ->createMock();
        $this->loggerInterfaceMock = self::getMockBuilder(LoggerInterface::class)
                                                    ->disableOriginalConstructor()
                                                    ->createMock();
    }

    public function testCreateService()
    {
        $sut = new GhostCheckInFactory();
        $this->serviceLocatorInterfaceMock->expects(self::any())
                                          ->method('get')
                                          ->will(self::returnValueMap([
                                              ['config', [
                                                  'GHOST_GRANT_API' => [
                                                      'host' => 'https://localhost',
                                                      'endpoints' => [
                                                          'check-in' => '/authorize'
                                                      ],
                                                      'headers' => []
                                                  ]
                                              ]],
                                              ['SimpleLogger', $this->loggerInterfaceMock]
                                          ]));
        $result = $sut->createService($this->serviceLocatorInterfaceMock);
        self::assertInstanceOf(GhostCheckIn::class, $result);
    }

    public function testCreateServiceReturnsFalseNoGhostGrantApiParam()
    {
        $sut = new GhostCheckInFactory();
        $this->serviceLocatorInterfaceMock->expects(self::any())
                                          ->method('get')
                                          ->will(self::returnValueMap([
                                              ['config', ['GHOST_GRANT_API' => []]],
                                              ['SimpleLogger', $this->loggerInterfaceMock]
                                          ]));
        $result = $sut->createService($this->serviceLocatorInterfaceMock);
        self::assertFalse($result);
    }

    public function testCreateServiceReturnsFalseNoHostParam()
    {
        $sut = new GhostCheckInFactory();
        $this->serviceLocatorInterfaceMock->expects(self::any())
                                          ->method('get')
                                          ->will(self::returnValueMap([
                                              ['config', []],
                                              ['SimpleLogger', $this->loggerInterfaceMock]
                                          ]));
        $result = $sut->createService($this->serviceLocatorInterfaceMock);
        self::assertFalse($result);
    }
}

<?php

namespace EMRAdminTest\unit\tests\Service\Session;

use EMRCore\PrototypeFactory;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\Session\RegistryKeepAlive;
use EMRCoreTest\Helper\Reflection;
use EMRAdmin\Service\Session\Dto\KeepAlive as KeepAliveDto;
use InvalidArgumentException;
use EMRAdmin\Model\SessionRegistry as SessionRegistryModel;
use EMRAdmin\Service\Session\Dto\KeepAliveSession;
use EMRAdmin\Service\Session\Dao\Registry;
use EMRCore\Session\Exception\Session as SessionException;
use Zend\Config\Config;

class RegistryKeepAliveTest extends PHPUnit_Framework_TestCase
{

    /**
     * Proves that the url returned is the same from the one provided by the config file
     */
    public function testGetUrlWithKeys()
    {
        $url = 'foo';

        $config = array(
            'slices' => array(
                'sso' => array(
                    'keepAlive' => $url
                )
            )
        );

        /** @var RegistryKeepAlive|PHPUnit_Framework_MockObject_MockObject $registryService */
        $registryService = $this->getMock('EMRAdmin\Service\Session\RegistryKeepAlive', array('getConfiguration'));

        $registryService->expects($this->once())->method('getConfiguration')->will($this->returnValue($config));

        $result = Reflection::invoke($registryService, 'getSsoKeepAliveUrl');

        $this->assertSame($result, $url, 'Asserting that the returned url is the same as the one declared in config');
    }

    /**
     * Proves that if the configuration lacks the required keys, an exception is raised
     * 
     * @expectedException \RuntimeException
     */
    public function testGetUrlWithoutKeys()
    {
        $config = array();

        /** @var RegistryKeepAlive|PHPUnit_Framework_MockObject_MockObject $registryService */
        $registryService = $this->getMock('EMRAdmin\Service\Session\RegistryKeepAlive', array('getConfiguration'));

        $registryService->expects($this->once())->method('getConfiguration')->will($this->returnValue($config));

        Reflection::invoke($registryService, 'getSsoKeepAliveUrl');
    }

    /**
     * Proves that the send to keep alive service method changes the DTO appropriately
     */
    public function testSendToKeepAliveService()
    {
        $response = 'foo';
        
        $clientMock = $this->getMock('EMRCore\Zend\Http\ClientWrapper', array('execute'));

        $clientMock->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($response));

        /** @var RegistryKeepAlive|PHPUnit_Framework_MockObject_MockObject $registryService */
        $registryService = $this->getMock('EMRAdmin\Service\Session\RegistryKeepAlive', array('getClientWrapper'));

        $registryService->expects($this->once())
            ->method('getClientWrapper')
            ->will($this->returnValue($clientMock));

        $registryService->setKeepAliveDto(new KeepAliveDto());

        Reflection::invoke($registryService, 'sendToKeepAliveService', array(array()));
        
        $dto = $registryService->getKeepAliveDto();
        
        $this->assertSame($dto->getResponse(), $response, 
                'Asserting that the response inside the DTO matches the expected one: '.$response);
    }
    
    /**
     * proves that the default result from keepalive sessions created from session registry models is 'skipped'
     */
    public function testGetKeepAliveSession()
    {
        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $mockedPrototypeFactory */
        $mockedPrototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);
        
        $mockedPrototypeFactory->expects($this->once())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name)
                {
                    switch ($name)
                    {
                        case 'EMRAdmin\Service\Session\Dto\KeepAliveSession':
                            return new KeepAliveSession();
                            break;
                        default:
                            throw new InvalidArgumentException("Mocked prototype factory cannot provide [{$name}]");
                            break;
                    }
                }));
                
        $registryService = new RegistryKeepAlive();
        
        $registryService->setPrototypeFactory($mockedPrototypeFactory);
        
        $registryModel = new SessionRegistryModel();
        
        $registryModel->setIdentityId(1)
                ->setSessionId(1)
                ->setSessionRegistryId(1)
                ->setSsoToken(1);
        
        $rs = Reflection::invoke($registryService, 'getKeepAliveSession', array($registryModel));
        
        $this->assertSame($rs->getResult(), KeepAliveSession::RESULT_SKIPPED, 
                'Assert that the session default result returned is "skipped"');
 
    }
    
    /**
     * proves that the supplied session to the garbage collector gets marked as such 
     * in the keepalive session dto collection.
     */
    public function testGarbageCollect()
    {
        $registryDao = $this->getMock('EMRAdmin\Service\Session\Dao\Registry');
        
        $registryDao->expects($this->once())->method('deleteBySessionRegistryId');
        
        $registryService = new RegistryKeepAlive();
        
        $registryService->setRegistryDao($registryDao);
        
        $registryService->setKeepAliveDto(new KeepAliveDto());
        
        $keepAliveSession = new KeepAliveSession();
        
        $sessionRegistryModel = new SessionRegistryModel();
        
        Reflection::invoke($registryService, 'garbageCollect', array(
            $keepAliveSession, 
            $sessionRegistryModel
        ));
        
        
        $this->assertSame($registryService->getKeepAliveDto()->current()->getResult(), KeepAliveSession::RESULT_GARBAGE_COLLECTED, 
                'Assert that the supplied session to the garbage collector gets added to the keepalive session dto as
                    garbage');
        
    }
    
    /**
     * Proves that an empty resultset from session registry will return an empty DTO
     */
    public function testKeepAliveEmptyResultset()
    {
        $mockedServiceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        
        $mockedServiceLocator->expects($this->any())->method('get')->will($this->returnCallback(function($name)
                {
                            switch ($name)
                            {
                                case 'EMRAdmin\Service\Session\Dto\KeepAlive':
                                    return new KeepAliveDto();
                                    break;
                                default:
                                    throw new InvalidArgumentException("Mocked service locator cannot provide [{$name}]");
                                    break;
                            }
            
                }
                ));
        
        
        
        $registryDao = $this->getMock('EMRAdmin\Service\Session\Dao\Registry');
        
        $registryDao->expects($this->once())->method('getAll')->will($this->returnValue(null));
        
        $keepAliveService = new RegistryKeepAlive();
        
        $keepAliveService->setRegistryDao($registryDao);
        $keepAliveService->setServiceLocator($mockedServiceLocator);
        
        $rs = $keepAliveService->keepAlive();
        
        $this->assertSame($rs->count(), 0, 
                'Asserting that an empty resultset from session registry will return an empty DTO');
    }
    
    /**
     * Proves that a session registry record that cannot be found in memcached will throw an exception and be handled
     * by the garbage collector
     */
    public function testKeepAliveWithoutSessionRegistry()
    {
        $mockedServiceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        
        $mockedServiceLocator->expects($this->any())->method('get')->will($this->returnCallback(function($name)
                {
                            switch ($name)
                            {
                                case 'EMRAdmin\Service\Session\Dto\KeepAlive':
                                    return new KeepAliveDto();
                                    break;
                                default:
                                    throw new InvalidArgumentException("Mocked service locator cannot provide [{$name}]");
                                    break;
                            }
            
                }
                ));
        
        $model = $this->getMock('EMRAdmin\Model\SessionRegistry');
        
        $model->setIdentityId(1);
        $model->setSessionId(1);
        $model->setSessionRegistryId(1);
        $model->setSsoToken(1);
        
        $registryDao = $this->getMock('EMRAdmin\Service\Session\Dao\Registry');
        
        $registryDao->expects($this->once())->method('getAll')->will($this->returnValue(array($model)));
        
        $keepAliveSession = new KeepAliveSession();
        $keepAliveSession->setApplicationSessionId(1)
                ->setLastUpdatedAt(1)
                ->setResult(KeepAliveSession::RESULT_SKIPPED)
                ->setSsoSessionId(1);
        
        $keepAliveService = $this->getMock('EMRAdmin\Service\Session\RegistryKeepAlive', array(
            'getKeepAliveSession',
            'getSessionFromFactory',
            'garbageCollect',
        ));
        
        $keepAliveService->expects($this->once())->method('getKeepAliveSession')
                ->will($this->returnValue($keepAliveSession));
        
        $keepAliveService->expects($this->once())->method('getSessionFromFactory')
                ->will($this->throwException(new SessionException));
        
        $keepAliveService->expects($this->once())->method('garbageCollect')->with($keepAliveSession, $model);

        $keepAliveService->setRegistryDao($registryDao);
        $keepAliveService->setServiceLocator($mockedServiceLocator);
        
        $keepAliveService->keepAlive();
        
    }
    
    /**
     * Proves that a timed out session will be handled by the garbage collector
     */
    public function testKeepAliveWithRegistryTimedOut()
    {
        $mockedServiceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        
        $mockedServiceLocator->expects($this->any())->method('get')->will($this->returnCallback(function($name)
                {
                            switch ($name)
                            {
                                case 'EMRAdmin\Service\Session\Dto\KeepAlive':
                                    return new KeepAliveDto();
                                    break;
                                default:
                                    throw new InvalidArgumentException("Mocked service locator cannot provide [{$name}]");
                                    break;
                            }
            
                }
                ));
        
        $model = $this->getMock('EMRAdmin\Model\SessionRegistry');
        
        $model->setIdentityId(1);
        $model->setSessionId(1);
        $model->setSessionRegistryId(1);
        $model->setSsoToken(1);
        
        $registryDao = $this->getMock('EMRAdmin\Service\Session\Dao\Registry');
        
        $registryDao->expects($this->once())->method('getAll')->will($this->returnValue(array($model)));
        
        $keepAliveSession = new KeepAliveSession();
        $keepAliveSession->setApplicationSessionId(1)
                ->setLastUpdatedAt(1)
                ->setResult(KeepAliveSession::RESULT_SKIPPED)
                ->setSsoSessionId(1);
        
        $keepAliveService = $this->getMock('EMRAdmin\Service\Session\RegistryKeepAlive', array(
            'getKeepAliveSession',
            'getSessionFromFactory',
            'garbageCollect',
        ));
        
        $config = new Config(array(
            'session_timeout' => '2 hours'
        ));
        
        $sessionRegistry = $this->getMock('EMRCore\Session\Memcache\Adapter');
        
        $sessionRegistry->expects($this->any())->method('get')->with('LastUpdatedTimestamp')
                ->will($this->returnValue(strtotime('-3 hours')));
        
        $sessionRegistry->expects($this->once())->method('getConfiguration')->will($this->returnValue($config));
        $sessionRegistry->expects($this->once())->method('destroy');
        
        $keepAliveService->expects($this->once())->method('getKeepAliveSession')
                ->will($this->returnValue($keepAliveSession));
        
        $keepAliveService->expects($this->once())->method('getSessionFromFactory')
                ->will($this->returnValue($sessionRegistry));
        
        $keepAliveService->expects($this->once())->method('garbageCollect')->with($keepAliveSession, $model);

        $keepAliveService->setRegistryDao($registryDao);
        $keepAliveService->setServiceLocator($mockedServiceLocator);
        
        $keepAliveService->keepAlive();
    }
    
    /**
     * Proves that a session registry older no more than 5 minutes will be sent to the keep alive service
     */
    public function testKeepAliveWithRegistryLessThanFiveMinutes()
    {
        $mockedServiceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        
        $mockedServiceLocator->expects($this->any())->method('get')->will($this->returnCallback(function($name)
                {
                            switch ($name)
                            {
                                case 'EMRAdmin\Service\Session\Dto\KeepAlive':
                                    return new KeepAliveDto();
                                    break;
                                default:
                                    throw new InvalidArgumentException("Mocked service locator cannot provide [{$name}]");
                                    break;
                            }
            
                }
                ));
        
        $model = $this->getMock('EMRAdmin\Model\SessionRegistry');
        
        $model->setIdentityId(1);
        $model->setSessionId(1);
        $model->setSessionRegistryId(1);
        $model->setSsoToken(1);
        
        $registryDao = $this->getMock('EMRAdmin\Service\Session\Dao\Registry');
        
        $registryDao->expects($this->once())->method('getAll')->will($this->returnValue(array($model)));
        
        $keepAliveSession = new KeepAliveSession();
        $keepAliveSession->setApplicationSessionId(1)
                ->setLastUpdatedAt(1)
                ->setResult(KeepAliveSession::RESULT_SKIPPED)
                ->setSsoSessionId(1);
        
        $keepAliveService = $this->getMock('EMRAdmin\Service\Session\RegistryKeepAlive', array(
            'getKeepAliveSession',
            'getSessionFromFactory',
            'sendToKeepAliveService',
        ));
        
        $config = new Config(array(
            'session_timeout' => '2 hours'
        ));
        
        $sessionRegistry = $this->getMock('EMRCore\Session\Memcache\Adapter');
        
        $sessionRegistry->expects($this->any())->method('get')->with('LastUpdatedTimestamp')
                ->will($this->returnValue(strtotime('-2 minutes')));
        
        $sessionRegistry->expects($this->once())->method('getConfiguration')->will($this->returnValue($config));
        
        $keepAliveService->expects($this->once())->method('getKeepAliveSession')
                ->will($this->returnValue($keepAliveSession));
        
        $keepAliveService->expects($this->once())->method('getSessionFromFactory')
                ->will($this->returnValue($sessionRegistry));
        
        $keepAliveService->expects($this->once())->method('sendToKeepAliveService');

        $keepAliveService->setRegistryDao($registryDao);
        $keepAliveService->setServiceLocator($mockedServiceLocator);
        
        $keepAliveService->keepAlive();
    }
    
    /**
     * Proves that a session older that 5 minutes will remain marked as skipped
     */
    public function testKeepAliveWithRegistryMoreThanFiveMinutes()
    {
        $mockedServiceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        
        $mockedServiceLocator->expects($this->any())->method('get')->will($this->returnCallback(function($name)
                {
                            switch ($name)
                            {
                                case 'EMRAdmin\Service\Session\Dto\KeepAlive':
                                    return new KeepAliveDto();
                                    break;
                                default:
                                    throw new InvalidArgumentException("Mocked service locator cannot provide [{$name}]");
                                    break;
                            }
            
                }
                ));
        
        $model = $this->getMock('EMRAdmin\Model\SessionRegistry');
        
        $model->setIdentityId(1);
        $model->setSessionId(1);
        $model->setSessionRegistryId(1);
        $model->setSsoToken(1);
        
        $registryDao = $this->getMock('EMRAdmin\Service\Session\Dao\Registry');
        
        $registryDao->expects($this->once())->method('getAll')->will($this->returnValue(array($model)));
        
        $keepAliveSession = new KeepAliveSession();
        $keepAliveSession->setApplicationSessionId(1)
                ->setLastUpdatedAt(1)
                ->setResult(KeepAliveSession::RESULT_SKIPPED)
                ->setSsoSessionId(1);
        
        $keepAliveService = $this->getMock('EMRAdmin\Service\Session\RegistryKeepAlive', array(
            'getKeepAliveSession',
            'getSessionFromFactory',
        ));
        
        $config = new Config(array(
            'session_timeout' => '2 hours'
        ));
        
        $sessionRegistry = $this->getMock('EMRCore\Session\Memcache\Adapter');
        
        $sessionRegistry->expects($this->any())->method('get')->with('LastUpdatedTimestamp')
                ->will($this->returnValue(strtotime('-6 minutes')));
        
        $sessionRegistry->expects($this->once())->method('getConfiguration')->will($this->returnValue($config));
        
        $keepAliveService->expects($this->once())->method('getKeepAliveSession')
                ->will($this->returnValue($keepAliveSession));
        
        $keepAliveService->expects($this->once())->method('getSessionFromFactory')
                ->will($this->returnValue($sessionRegistry));

        $keepAliveService->setRegistryDao($registryDao);
        $keepAliveService->setServiceLocator($mockedServiceLocator);
        
        $rs = $keepAliveService->keepAlive();
        
        $this->assertTrue($rs instanceof KeepAliveDto, 'Asserting that returned object is of KeepAlive DTO type');
        
        $this->assertSame($rs->current()->getResult(), KeepAliveSession::RESULT_SKIPPED, 
                'Asserting that session inside the collection is marked as skipped');
    }

}
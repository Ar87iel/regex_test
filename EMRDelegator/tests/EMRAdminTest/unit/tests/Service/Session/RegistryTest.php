<?php

namespace EMRAdminTest\unit\tests\Service\Session;

use DateTime;
use Doctrine\DBAL\DBALException;
use PHPUnit_Framework_TestCase;
use EMRAdmin\Model\SessionRegistry as SessionRegistryModel;
use EMRAdmin\Service\Session\Registry as RegistryService;
use EMRAdmin\Service\Session\Exception;

class RegistryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var RegistryService $registryService
     */
    private $registryService;

    /**
     * @var SessionRegistryModel $sessionRegistryModel
     */
    private $sessionRegistryModel;

    /**
     * 
     */
    protected function setUp()
    {
        $this->registryService = new RegistryService();
        $this->sessionRegistryModel = new SessionRegistryModel();
    }

    /**
     * Proves create registry
     */
    public function testCreateRegistryFromDaoCrud()
    {
        $identityId = 123;
        $ssoToken = 'foobar';
        $sessionId = 'barfoo';

        $sessionRegistryModel = new SessionRegistryModel();
        $sessionRegistryModel->setIdentityId($identityId);
        $sessionRegistryModel->setSsoToken($ssoToken);
        $sessionRegistryModel->setSessionId($sessionId);

        $registryDaoMock = $this->createMock('\EMRAdmin\Service\Session\Dao\Registry');

        $this->registryService->setRegistryDao($registryDaoMock);

        $beforeTime = new DateTime();
        $that = $this;
        $registryDaoMock->expects($this->once())
                ->method('create')
                ->will($this->returnCallback(function($srm) use($that, $sessionRegistryModel, &$beforeTime) {
                    /** @var SessionRegistryModel $srm */

                    $afterTime = new DateTime();

                    $that->assertInstanceOf(get_class($sessionRegistryModel), $srm);

                    $that->assertGreaterThanOrEqual($beforeTime, $srm->getCreated());
                    $that->assertLessThanOrEqual($afterTime, $srm->getCreated());
                    $that->assertGreaterThanOrEqual($beforeTime, $srm->getLastModified());
                    $that->assertLessThanOrEqual($afterTime, $srm->getLastModified());

                    $sessionRegistryModel->setCreated($srm->getCreated());
                    $sessionRegistryModel->setLastModified($srm->getLastModified());

                    $that->assertEquals($sessionRegistryModel, $srm);
                }));

        $this->registryService->setRegistryDao($registryDaoMock);

        /** @var  SessionRegistryModel $result */
        $this->registryService->create(
            $sessionRegistryModel->getIdentityId(),
            $sessionRegistryModel->getSsoToken(),
            $sessionRegistryModel->getSessionId()
        );
    }

    public function testGetRegistryByIdentityId()
    {

        $identityId = 123;
        $sessionRegistryModel = new SessionRegistryModel();
        $sessionRegistryModel->setIdentityId($identityId);


        $registryDaoMock = $this->createMock('\EMRAdmin\Service\Session\Dao\Registry');

        $this->registryService->setRegistryDao($registryDaoMock);
        $registryDaoMock->expects($this->once())
                ->method('getByIdentityId')
                ->with($identityId)
                ->will($this->returnValue($sessionRegistryModel));

        $returnedRegistry = $this->registryService->getByIdentityId($identityId);

        $this->assertNotNull($returnedRegistry);
        $this->assertEquals($identityId, $returnedRegistry->getIdentityId());
    }

    public function testGetRegistryBySsoToken()
    {

        $ssoToken = 'thisbeatoken';
        $sessionRegistryModel = new SessionRegistryModel();
        $sessionRegistryModel->setSsoToken($ssoToken);


        $registryDaoMock = $this->createMock('\EMRAdmin\Service\Session\Dao\Registry');

        $this->registryService->setRegistryDao($registryDaoMock);
        $registryDaoMock->expects($this->once())
                ->method('getBySsoToken')
                ->with($ssoToken)
                ->will($this->returnValue($sessionRegistryModel));

        $returnedRegistry = $this->registryService->getBySsoToken($ssoToken);

        $this->assertNotNull($returnedRegistry);
        $this->assertEquals($ssoToken, $returnedRegistry->getSsoToken());
    }

    /**
     * @expectedException \EMRAdmin\Service\Session\Exception\SessionRegistryNotFound
     */
    public function testGetRegistryBySsoTokenThrowsExceptionForEmptyModel()
    {

        $ssoToken = 'thisbethetoken';

        $registryDaoMock = $this->createMock('\EMRAdmin\Service\Session\Dao\Registry');

        $this->registryService->setRegistryDao($registryDaoMock);
        $registryDaoMock->expects($this->once())
                ->method('getBySsoToken')
                ->with($ssoToken)
                ->will($this->returnValue(null));

        $this->registryService->getBySsoToken($ssoToken);
    }

    /**
     * @expectedException \EMRAdmin\Service\Session\Exception\SessionRegistryNotFound
     */
    public function testGetRegistryByIdentityIdThrowsExceptionForEmptyModel()
    {

        $identityId = '123';

        $registryDaoMock = $this->createMock('\EMRAdmin\Service\Session\Dao\Registry');

        $this->registryService->setRegistryDao($registryDaoMock);
        $registryDaoMock->expects($this->once())
                ->method('getByIdentityId')
                ->with($identityId)
                ->will($this->returnValue(null));

        $this->registryService->getByIdentityId($identityId);
    }

    public function testDeleteRegistryBySsoToken()
    {

        $ssoToken = 'thisbeatoken';
        $sessionRegistryModel = new SessionRegistryModel();
        $sessionRegistryModel->setSsoToken($ssoToken);


        $registryDaoMock = $this->createMock('\EMRAdmin\Service\Session\Dao\Registry');

        $this->registryService->setRegistryDao($registryDaoMock);
        $registryDaoMock->expects($this->once())
                ->method('getBySsoToken')
                ->with($ssoToken)
                ->will($this->returnValue($sessionRegistryModel));

        $registryDaoMock->expects(($this->once()))
                ->method('delete')
                ->with($sessionRegistryModel);

        $returnedRegistry = $this->registryService->deleteBySsoToken($ssoToken);

        $this->assertEmpty($returnedRegistry);
    }

    public function testDeleteRegistryByIdentityId()
    {

        $identityId = 123;
        $sessionRegistryModel = new SessionRegistryModel();
        $sessionRegistryModel->setIdentityId($identityId);

        $registryDaoMock = $this->createMock('\EMRAdmin\Service\Session\Dao\Registry');

        $this->registryService->setRegistryDao($registryDaoMock);
        $registryDaoMock->expects($this->once())
                ->method('getByIdentityId')
                ->with($identityId)
                ->will($this->returnValue($sessionRegistryModel));

        $registryDaoMock->expects(($this->once()))
                ->method('delete')
                ->with($sessionRegistryModel);

        $returnedRegistry = $this->registryService->deleteByIdentityId($identityId);

        $this->assertEmpty($returnedRegistry);
    }

    /**
     * Proves handles DBALException 23000
     */
    public function testCreateRegistryHandlesDbalException23000()
    {
        $identityId = 123;
        $ssoToken = 'foobar';
        $sessionId = 'barfoo';

        $sessionRegistryModel = new SessionRegistryModel();
        $sessionRegistryModel->setIdentityId($identityId);
        $sessionRegistryModel->setSsoToken($ssoToken);
        $sessionRegistryModel->setSessionId($sessionId);

        $registryDaoMock = $this->createMock('\EMRAdmin\Service\Session\Dao\Registry');

        $pdoException = new \PDOException('test', 23000);

        $this->registryService->setRegistryDao($registryDaoMock);
        $registryDaoMock->expects($this->once())
            ->method('create')
            ->will($this->throwException(new DBALException('',0,$pdoException)));

        $this->registryService->setRegistryDao($registryDaoMock);

        $logger = $this->getMock('\Logger', array(), array(), '', false);
        $logger->expects($this->atLeastOnce())->method('warn');

        $this->registryService->setLogger($logger);

        /** @var  SessionRegistryModel $result */
        $this->registryService->create(
            $sessionRegistryModel->getIdentityId(),
            $sessionRegistryModel->getSsoToken(),
            $sessionRegistryModel->getSessionId()
        );

    }

    /**
     * Proves handles DBALException
     * @expectedException \Doctrine\DBAL\DBALException
     */
    public function testCreateRegistryThrowsDbalException()
    {
        $identityId = 123;
        $ssoToken = 'foobar';
        $sessionId = 'barfoo';

        $sessionRegistryModel = new SessionRegistryModel();
        $sessionRegistryModel->setIdentityId($identityId);
        $sessionRegistryModel->setSsoToken($ssoToken);
        $sessionRegistryModel->setSessionId($sessionId);

        $registryDaoMock = $this->createMock('\EMRAdmin\Service\Session\Dao\Registry');

        $pdoException = new \PDOException('test', 1);

        $this->registryService->setRegistryDao($registryDaoMock);
        $registryDaoMock->expects($this->once())
            ->method('create')
            ->will($this->throwException(new DBALException('',0,$pdoException)));

        $this->registryService->setRegistryDao($registryDaoMock);

        /** @var  SessionRegistryModel $result */
        $this->registryService->create(
            $sessionRegistryModel->getIdentityId(),
            $sessionRegistryModel->getSsoToken(),
            $sessionRegistryModel->getSessionId()
        );

    }

    /**
     * Proves handles PDOException 23000
     */
    public function testCreateRegistryHandlesPDOException23000()
    {
        $identityId = 123;
        $ssoToken = 'foobar';
        $sessionId = 'barfoo';

        $sessionRegistryModel = new SessionRegistryModel();
        $sessionRegistryModel->setIdentityId($identityId);
        $sessionRegistryModel->setSsoToken($ssoToken);
        $sessionRegistryModel->setSessionId($sessionId);

        $registryDaoMock = $this->createMock('\EMRAdmin\Service\Session\Dao\Registry');

        $pdoException = new \PDOException('test', 23000);

        $this->registryService->setRegistryDao($registryDaoMock);
        $registryDaoMock->expects($this->once())
            ->method('create')
            ->will($this->throwException($pdoException));

        $this->registryService->setRegistryDao($registryDaoMock);

        $logger = $this->getMock('\Logger', array(), array(), '', false);
        $logger->expects($this->atLeastOnce())->method('warn');

        $this->registryService->setLogger($logger);

        /** @var  SessionRegistryModel $result */
        $this->registryService->create(
            $sessionRegistryModel->getIdentityId(),
            $sessionRegistryModel->getSsoToken(),
            $sessionRegistryModel->getSessionId()
        );

    }

    /**
     * Proves throws PDOException
     * @expectedException \PDOException
     */
    public function testCreateRegistryThrowsPDOException()
    {
        $identityId = 123;
        $ssoToken = 'foobar';
        $sessionId = 'barfoo';

        $sessionRegistryModel = new SessionRegistryModel();
        $sessionRegistryModel->setIdentityId($identityId);
        $sessionRegistryModel->setSsoToken($ssoToken);
        $sessionRegistryModel->setSessionId($sessionId);

        $registryDaoMock = $this->createMock('\EMRAdmin\Service\Session\Dao\Registry');

        $pdoException = new \PDOException('test', 1);

        $this->registryService->setRegistryDao($registryDaoMock);
        $registryDaoMock->expects($this->once())
            ->method('create')
            ->will($this->throwException($pdoException));

        $this->registryService->setRegistryDao($registryDaoMock);

        /** @var  SessionRegistryModel $result */
        $this->registryService->create(
            $sessionRegistryModel->getIdentityId(),
            $sessionRegistryModel->getSsoToken(),
            $sessionRegistryModel->getSessionId()
        );

    }

}

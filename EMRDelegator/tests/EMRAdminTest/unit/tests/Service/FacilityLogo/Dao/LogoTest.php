<?php
namespace EMRAdminTest\unit\tests\Service\FacilityLogo\Dao;

use EMRAdmin\Service\FacilityLogo\Dao\Logo;
use EMRAdmin\Service\FacilityLogo\Dto\SaveFacilityLogoRequest;
use EMRCore\PrototypeFactory;
use EMRCore\Service\Assets\Dto\FacilityPathRequest;
use EMRCoreTest\Helper\Reflection;
use InvalidArgumentException;
use Logger;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class LogoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $serviceLocator;

    /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
    private $prototypeFactory;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $fileService;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $assetsService;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $logger;

    /**
     * @var Logo
     */
    private $dao;

    public function setUp()
    {
        /** @var Logger|PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->getMock('Logger', array(), array(), '', false);
        $this->logger = $logger;

        $assetsService = $this->getMock('EMRCore\Service\Assets\Facility');
        $this->assetsService = $assetsService;

        $fileService = $this->getMock('EMRCore\File\File');
        $this->fileService = $fileService;

        /** @var ServiceLocatorInterface|PHPUnit_Framework_MockObject_MockObject $serviceLocator */
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->serviceLocator = $serviceLocator;
        $this->serviceLocator->expects($this->any())->method('get')
            ->will($this->returnCallback(function($name) use ($assetsService, $fileService) {

                if ($name === 'EMRCore\Service\Assets\Facility')
                {
                    return $assetsService;
                }

                if ($name === 'EMRCore\File\File')
                {
                    return $fileService;
                }

                throw new InvalidArgumentException("Mocked ServiceLocatorInterface cannot provide [$name].");
            }));

        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);
        $this->prototypeFactory = $prototypeFactory;
        $this->prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(function($name) {

                if ($name === 'EMRCore\Service\Assets\Dto\FacilityPathRequest')
                {
                    return new FacilityPathRequest;
                }

                throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
            }));

        $this->dao = new Logo;

        $this->dao->setServiceLocator($this->serviceLocator);
        $this->dao->setPrototypeFactory($this->prototypeFactory);

        $this->dao->setLogger($this->logger);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNotCreatesAssetDirectory()
    {
        $dirName = sys_get_temp_dir() . '/' . md5(__METHOD__);
        $facilityId = 1;

        $this->fileService->expects($this->once())->method('mkdir')
            ->with($this->equalTo($dirName), $this->equalTo(0775), $this->equalTo(true))
            ->will($this->returnValue(false));

        Reflection::invoke($this->dao, 'createAssetsDirectory', array($dirName, $facilityId));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNotCreatesAssetDirectory2()
    {
        $dirName = sys_get_temp_dir() . '/' . md5(__METHOD__);
        $facilityId = 1;

        $this->fileService->expects($this->once())->method('mkdir')
            ->with($this->equalTo($dirName), $this->equalTo(0775), $this->equalTo(true))
            ->will($this->returnValue(true));

        $this->fileService->expects($this->once())->method('is_dir')
            ->with($this->equalTo($dirName))->will($this->returnValue(false));

        Reflection::invoke($this->dao, 'createAssetsDirectory', array($dirName, $facilityId));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNotCreatesWritableAssetDirectory()
    {
        $dirName = sys_get_temp_dir() . '/' . md5(__METHOD__);
        $facilityId = 1;

        $this->fileService->expects($this->once())->method('mkdir')
            ->with($this->equalTo($dirName), $this->equalTo(0775), $this->equalTo(true))
            ->will($this->returnValue(true));

        $this->fileService->expects($this->once())->method('is_dir')
            ->with($this->equalTo($dirName))->will($this->returnValue(true));

        $this->fileService->expects($this->once())->method('is_writable')
            ->with($this->equalTo($dirName))->will($this->returnValue(false));

        Reflection::invoke($this->dao, 'createAssetsDirectory', array($dirName, $facilityId));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNotMovesFile()
    {
        $oldName = sys_get_temp_dir() . '/' . md5(__METHOD__ . 1);
        $newName = sys_get_temp_dir() . '/' . md5(__METHOD__ . 2);
        $facilityId = 1;

        $this->fileService->expects($this->once())->method('rename')
            ->with($this->equalTo($oldName), $this->equalTo($newName));

        $this->fileService->expects($this->once())->method('is_file')
            ->with($this->equalTo($newName))->will($this->returnValue(false));

        Reflection::invoke($this->dao, 'moveFile', array($oldName, $newName, $facilityId));
    }

    public function testCreatesAssetDirectoryAndMovesFile()
    {
        $dao = $this->getMock('EMRAdmin\Service\FacilityLogo\Dao\Logo', array('createAssetsDirectory', 'moveFile'));

        $dirName = sys_get_temp_dir();
        $fileName = $dirName . '/' . md5(__METHOD__);

        $companyId = 1;
        $facilityId = 1;

        $request = new SaveFacilityLogoRequest;
        $request->setCompanyId($companyId);
        $request->setFacilityId($facilityId);
        $request->setFilename($fileName);

        $this->assetsService->expects($this->once())->method('getTemporaryLogoFilename')
            ->withAnyParameters()->will($this->returnValue($fileName));

        $this->fileService->expects($this->once())->method('is_dir')
            ->with($this->equalTo($dirName))->will($this->returnValue(false));

        $dao->expects($this->once())->method('createAssetsDirectory')
            ->withAnyParameters($this->equalTo($dirName), $this->equalTo($facilityId));

        $dao->expects($this->once())->method('moveFile')
            ->with($this->equalTo($fileName), $this->equalTo($fileName), $this->equalTo($facilityId));

        /** @var Logo $dao */
        $dao->setServiceLocator($this->serviceLocator);
        $dao->setPrototypeFactory($this->prototypeFactory);
        $dao->setLogger($this->logger);

        $dao->storeTemporarily($request);
    }
}
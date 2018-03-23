<?php

namespace EMRAdminTest\unit\tests\Service\User\Dao;

use EMRAdmin\Service\User\Dto\User as UserDto;
use EMRAdmin\Service\User\Dao\Asset as AssetDao;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\User\User as UserService;

/**
 * Test User Service
 */
class AssetTest extends PHPUnit_Framework_TestCase
{
    private $serviceLocator;
    
    const LOGO_FILENAME = "path/to/logo";
    const LOGO_KEY = "key123456";
    const LOGO_FILE = "logo.jpg";
    
    const SIGNATURE_FILENAME = "path/to/signature";
    const SIGNATURE_KEY = "key654321";
    const SIGNATURE_FILE = "signature.jpg";
    
    function setUp()
    {
        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
    }
    
    public function testStoreTemporaryLogo()
    {
        $fileService = $this->getMock("EMRCore\File\File");
        $assetService = $this->getMock("EMRCore\Service\Assets\User");
        
        $assetService
            ->expects($this->once())
            ->method("getKey")
            ->with(self::LOGO_FILENAME . DIRECTORY_SEPARATOR .  self::LOGO_KEY)
            ->will($this->returnValue(self::LOGO_KEY));
        
        $assetService
            ->expects($this->once())
            ->method("getTemporaryLogoFilename")
            ->with(self::LOGO_KEY)
            ->will($this->returnValue(self::LOGO_FILENAME . DIRECTORY_SEPARATOR .  self::LOGO_FILE));
        
        $fileService
            ->expects($this->any())
            ->method("is_dir")
            ->will($this->returnValue(self::LOGO_FILENAME));                   
        
        $fileService
            ->expects($this->once())
            ->method("rename")
            ->with(self::LOGO_FILENAME . DIRECTORY_SEPARATOR .  self::LOGO_KEY, self::LOGO_FILENAME . DIRECTORY_SEPARATOR .  self::LOGO_FILE)
            ->will($this->returnValue(true));
        
        $fileService
            ->expects($this->once())
            ->method("chmod")
            ->with(self::LOGO_FILENAME . DIRECTORY_SEPARATOR . self::LOGO_FILE, 0775)
            ->will($this->returnValue(true));
        
        $this->serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($name) use ($assetService, $fileService){
                switch($name)
                {
                    case "EMRCore\File\File":
                        return $fileService;
                        break;
                    case "EMRCore\Service\Assets\User":
                        return $assetService;
                        break;
                    default:
                        throw new InvalidArgumentException("Mocked ServiceManager cannot create name [$name].");
                }
        }));
        
        $assetDao = new AssetDao();
        $assetDao->setServiceLocator($this->serviceLocator);
        $assetDao->storeTemporaryLogo(self::LOGO_FILENAME . DIRECTORY_SEPARATOR .  self::LOGO_KEY);
    }
    
    public function testStoreTemporarySignature()
    {
        $fileService = $this->getMock("EMRCore\File\File");
        $assetService = $this->getMock("EMRCore\Service\Assets\User");
        
        $assetService
            ->expects($this->once())
            ->method("getKey")
            ->with(self::SIGNATURE_FILENAME . DIRECTORY_SEPARATOR .  self::SIGNATURE_KEY)
            ->will($this->returnValue(self::SIGNATURE_KEY));
        
        $assetService
            ->expects($this->once())
            ->method("getTemporaryLogoFilename")
            ->with(self::SIGNATURE_KEY)
            ->will($this->returnValue(self::SIGNATURE_FILENAME . DIRECTORY_SEPARATOR .  self::SIGNATURE_FILE));
        
        $fileService
            ->expects($this->any())
            ->method("is_dir")
            ->will($this->returnValue(self::SIGNATURE_FILENAME));                   
        
        $fileService
            ->expects($this->once())
            ->method("rename")
            ->with(self::SIGNATURE_FILENAME . DIRECTORY_SEPARATOR .  self::SIGNATURE_KEY, self::SIGNATURE_FILENAME . DIRECTORY_SEPARATOR .  self::SIGNATURE_FILE)
            ->will($this->returnValue(true));
        
        $fileService
            ->expects($this->once())
            ->method("chmod")
            ->with(self::SIGNATURE_FILENAME . DIRECTORY_SEPARATOR . self::SIGNATURE_FILE, 0775)
            ->will($this->returnValue(true));
        
        $this->serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($name) use ($assetService, $fileService){
                switch($name)
                {
                    case "EMRCore\File\File":
                        return $fileService;
                        break;
                    case "EMRCore\Service\Assets\User":
                        return $assetService;
                        break;
                    default:
                        throw new InvalidArgumentException("Mocked ServiceManager cannot create name [$name].");
                }
        }));
        
        $assetDao = new AssetDao();
        $assetDao->setServiceLocator($this->serviceLocator);
        $assetDao->storeTemporaryLogo(self::SIGNATURE_FILENAME . DIRECTORY_SEPARATOR .  self::SIGNATURE_KEY);
    }
    
    public function testStorePermanentLogo()
    {
        $fileService = $this->getMock("EMRCore\File\File");
        $assetService = $this->getMock("EMRCore\Service\Assets\User");
        
        $this->serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($name) use ($assetService, $fileService){
                switch($name)
                {
                    case "EMRCore\File\File":
                        return $fileService;
                        break;
                    case "EMRCore\Service\Assets\User":
                        return $assetService;
                        break;
                    default:
                        throw new InvalidArgumentException("Mocked ServiceManager cannot create name [$name].");
                }
        }));
        
        $dto = new UserDto();
        
        $dto->setIsSuperUser(true);
        $dto->setId(5);
        $dto->setLogoFullFileName(self::LOGO_FILENAME . DIRECTORY_SEPARATOR .  self::LOGO_KEY);
        
        $assetService->expects($this->once())
            ->method('getLogoFilename')
            ->with($dto->getId())
            ->will($this->returnValue(self::LOGO_FILENAME . DIRECTORY_SEPARATOR .  self::LOGO_FILE));
        
                $fileService
            ->expects($this->any())
            ->method("is_dir")
            ->will($this->returnValue(self::LOGO_FILENAME));                   
        
        $fileService
            ->expects($this->once())
            ->method("rename")
            ->with(self::LOGO_FILENAME . DIRECTORY_SEPARATOR .  self::LOGO_KEY, self::LOGO_FILENAME . DIRECTORY_SEPARATOR .  self::LOGO_FILE)
            ->will($this->returnValue(true));
        
        $fileService
            ->expects($this->once())
            ->method("chmod")
            ->with(self::LOGO_FILENAME . DIRECTORY_SEPARATOR . self::LOGO_FILE, 0775)
            ->will($this->returnValue(true));
        
        $assetDao = new AssetDao();
        $assetDao->setServiceLocator($this->serviceLocator);
        $assetDao->storePermanentLogo($dto);
        
    }
    
    public function testStorePermanentSignature()
    {
        $fileService = $this->getMock("EMRCore\File\File");
        $assetService = $this->getMock("EMRCore\Service\Assets\User");
        
        $this->serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($name) use ($assetService, $fileService){
                switch($name)
                {
                    case "EMRCore\File\File":
                        return $fileService;
                        break;
                    case "EMRCore\Service\Assets\User":
                        return $assetService;
                        break;
                    default:
                        throw new InvalidArgumentException("Mocked ServiceManager cannot create name [$name].");
                }
        }));
        
        $dto = new UserDto();
        
        $dto->setIsSuperUser(true);
        $dto->setId(5);
        $dto->setSignatureFullFileName(self::SIGNATURE_FILENAME . DIRECTORY_SEPARATOR .  self::SIGNATURE_KEY);
        
        $assetService->expects($this->once())
            ->method('getSignatureFilename')
            ->with($dto->getId())
            ->will($this->returnValue(self::SIGNATURE_FILENAME . DIRECTORY_SEPARATOR .  self::SIGNATURE_FILE));
        
        $fileService
            ->expects($this->any())
            ->method("is_dir")
            ->will($this->returnValue(self::SIGNATURE_FILENAME));                   
        
        $fileService
            ->expects($this->once())
            ->method("rename")
            ->with(self::SIGNATURE_FILENAME . DIRECTORY_SEPARATOR .  self::SIGNATURE_KEY, self::SIGNATURE_FILENAME . DIRECTORY_SEPARATOR .  self::SIGNATURE_FILE)
            ->will($this->returnValue(true));
        
        $fileService
            ->expects($this->once())
            ->method("chmod")
            ->with(self::SIGNATURE_FILENAME . DIRECTORY_SEPARATOR . self::SIGNATURE_FILE, 0775)
            ->will($this->returnValue(true));
        
        
        $assetDao = new AssetDao();
        $assetDao->setServiceLocator($this->serviceLocator);
        $assetDao->storePermanentSignature($dto);
    }   
    
    public function testDeletePermanentLogoFile()
    {
        $fileService = $this->getMock("EMRCore\File\File");
        $assetService = $this->getMock("EMRCore\Service\Assets\User");
        
        $this->serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($name) use ($assetService, $fileService){
                switch($name)
                {
                    case "EMRCore\File\File":
                        return $fileService;
                        break;
                    case "EMRCore\Service\Assets\User":
                        return $assetService;
                        break;
                    default:
                        throw new InvalidArgumentException("Mocked ServiceManager cannot create name [$name].");
                }
        }));
        
        $dto = new UserDto();
        
        $dto->setIsSuperUser(true);
        $dto->setId(5);
        $dto->setSignatureFullFileName(self::SIGNATURE_FILENAME . DIRECTORY_SEPARATOR .  self::SIGNATURE_KEY);
        
        $assetService->expects($this->once())
            ->method('getLogoFilename')
            ->with($dto->getId())
            ->will($this->returnValue(self::SIGNATURE_FILENAME . DIRECTORY_SEPARATOR .  self::SIGNATURE_FILE));
        
        $fileService
            ->expects($this->any())
            ->method("is_file")
            ->will($this->returnValue(self::SIGNATURE_FILE));
        
        $fileService
            ->expects($this->once())
            ->method("unlink")
            //->with(self::SIGNATURE_FILENAME . DIRECTORY_SEPARATOR . self::SIGNATURE_FILE, 0775)
            ->will($this->returnValue(true));
        
        $assetDao = new AssetDao();
        $assetDao->setServiceLocator($this->serviceLocator);
        $assetDao->deletePermanentProfileFile($dto->getId(5));
    }
    
      public function testDeletePermanentSignatureFile()
    {
        $fileService = $this->getMock("EMRCore\File\File");
        $assetService = $this->getMock("EMRCore\Service\Assets\User");
        
        $this->serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($name) use ($assetService, $fileService){
                switch($name)
                {
                    case "EMRCore\File\File":
                        return $fileService;
                        break;
                    case "EMRCore\Service\Assets\User":
                        return $assetService;
                        break;
                    default:
                        throw new InvalidArgumentException("Mocked ServiceManager cannot create name [$name].");
                }
        }));
        
        $dto = new UserDto();
        
        $dto->setIsSuperUser(true);
        $dto->setId(5);
        $dto->setSignatureFullFileName(self::SIGNATURE_FILENAME . DIRECTORY_SEPARATOR .  self::SIGNATURE_KEY);
        
        $assetService->expects($this->once())
            ->method('getSignatureFilename')
            ->with($dto->getId())
            ->will($this->returnValue(self::SIGNATURE_FILENAME . DIRECTORY_SEPARATOR .  self::SIGNATURE_FILE));
        
        $fileService
            ->expects($this->any())
            ->method("is_file")
            ->will($this->returnValue(self::SIGNATURE_FILE));
        
        $fileService
            ->expects($this->once())
            ->method("unlink")
            //->with(self::SIGNATURE_FILENAME . DIRECTORY_SEPARATOR . self::SIGNATURE_FILE, 0775)
            ->will($this->returnValue(true));
        
        $assetDao = new AssetDao();
        $assetDao->setServiceLocator($this->serviceLocator);
        $assetDao->deletePermanentSignatureFile($dto->getId(5));
    }
    
}
<?php

namespace EMRAdminTest\unit\tests\Service\User;

use EMRAdmin\Service\User\Dao\Asset;
use EMRAdmin\Service\User\Dao\Esb;
use EMRAdmin\Service\User\Dto\User as UserDto;
use EMRCore\PrototypeFactory;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\User\User as UserService;
use InvalidArgumentException;
use EMRAdmin\Service\User\Dto\SearchCriteria;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Test User Service
 */
class UserTest extends PHPUnit_Framework_TestCase
{

    /** @var UserService */
    private $userService;

    /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject */
    private $prototypeFactory;

    /** @var ServiceLocatorInterface|PHPUnit_Framework_MockObject_MockObject */
    private $serviceLocator;

    /** @var Asset|PHPUnit_Framework_MockObject_MockObject */
    private $assetDao;

    /** @var Esb|PHPUnit_Framework_MockObject_MockObject */
    private $esbDao;

    /** @var  UserDto */
    private $userRequest;

    /** @var  UserDto */
    private $userResponse;
    
    const LOGO_FILENAME = "path/to/logo/key";

    const SIGNATURE_FILENAME = "path/to/signature/key";
    
    function setUp()
    {
        $this->userService = new UserService();
        $this->userRequest = new UserDto();
        $this->userResponse = new UserDto();
        $this->serviceLocator = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);
        $this->assetDao = $this->createMock('EMRAdmin\Service\User\Dao\Asset');
        $this->esbDao = $this->createMock('EMRAdmin\Service\User\Dao\Esb');
    }
    
    public function testSaveNormalUserWithAssets()
    {
        $this->userRequest->setIsSuperUser(false);
        $this->userResponse->setId(5);
        $this->userRequest->setLogoFullFileName(self::LOGO_FILENAME);
        $this->userRequest->setSignatureFullFileName(self::SIGNATURE_FILENAME);
        $this->userRequest->setId(5);
        $this->userResponse->setIsSuperUser(false);
        
        $this->esbDao->expects($this->once())
            ->method("saveUser")
            ->with($this->equalTo($this->userRequest))
            ->will($this->returnValue($this->userResponse));
        
        $this->assetDao->expects($this->once())
            ->method("storeTemporaryLogo")
            ->with($this->equalTo($this->userRequest->getLogoFullFileName()));
        
        $this->assetDao->expects($this->once())
            ->method("storeTemporarySignature")
            ->with($this->equalTo($this->userRequest->getSignatureFullFileName()));
        
        
        $this->setUserService();
        $this->userService->saveUser($this->userRequest);
    }
    
    
    public function testSaveSuperUserWithAssets()
    {
        $this->userRequest->setIsSuperUser(true);
        $this->userResponse->setIsSuperUser(true);
        $this->userResponse->setId(5);
        $this->userRequest->setId(5);
        $this->userRequest->setLogoFullFileName(self::LOGO_FILENAME);
        $this->userRequest->setSignatureFullFileName(self::SIGNATURE_FILENAME);
        
        $this->esbDao->expects($this->once())
            ->method("saveUser")
            ->with($this->equalTo($this->userRequest))
            ->will($this->returnValue($this->userResponse));
        
        $this->assetDao->expects($this->once())
            ->method("storePermanentLogo")
            ->with($this->equalTo($this->userRequest));
        
        $this->assetDao->expects($this->once())
            ->method("storePermanentSignature")
            ->with($this->equalTo($this->userRequest));
        
        
        $this->setUserService();
        $this->userService->saveUser($this->userRequest);
    }
    
    private function setUserService()
    {
        $this->userService->setPrototypeFactory($this->prototypeFactory);
        $this->userService->setServiceLocator($this->serviceLocator);
        $this->userService->setAssetDao($this->assetDao);
        $this->userService->setEsbDao($this->esbDao);
        
    }

    /**
     * Proves that the getSuperUsers will call the prototype factory to get a SearchCriteria object and then call
     * the Dao's searchUserByCriteria() method
     */
    public function testGetSuperUsers()
    {
        $this->prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name)
                {
                    switch ($name)
                    {
                        case 'EMRAdmin\Service\User\Dto\SearchCriteria':
                            return new SearchCriteria();
                            break;
                        default:
                            throw new InvalidArgumentException("Mocked prototype factory cannot provide '[{$name}]'");
                            break;
                    }
                }));

        $this->esbDao->expects($this->once())
            ->method('searchUserByCriteria');

        $this->setUserService();
        $this->userService->getSuperUsers();
    }
}

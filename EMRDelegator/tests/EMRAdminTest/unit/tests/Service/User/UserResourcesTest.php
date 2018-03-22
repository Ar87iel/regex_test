<?php
namespace EMRAdminTest\unit\tests\Service\User;

use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\User\UserResources as UserResourcesService;
use EMRAdmin\Service\User\Dao\UserResources as UserResourcesDao;
use EMRAdmin\Service\User\Marshaller\ArrayUserResourcesToSimpleArray;
use EMRModel\Resource\Resource as ResourceModel;
use InvalidArgumentException;


class UserResourcesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;
    
    /**
     *
     * @var UserResourcesDao 
     */
    private $dao;
    
    public function setUp()
    {
        $this->serviceLocator = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->dao = $this->createMock('EMRAdmin\Service\User\Dao\UserResources');
    }
    
    /**
     * Tests the service execution and its marshaller
     */
    public function testGetUserResources()
    {
        
        $resource = new ResourceModel();
        $resource->setId('foo');
        
        $arrAllowedResources = array(
            $resource 
        );
        
        $this->dao->expects($this->once())->method('getResourcesByUserId')
                ->will($this->returnValue($arrAllowedResources));
        
        $this->serviceLocator->expects($this->any())->method('get')->will($this->returnCallback(
                function($name)
                {
                    switch ($name)
                    {
                        case 'EMRAdmin\Service\User\Marshaller\ArrayUserResourcesToSimpleArray':
                            return new ArrayUserResourcesToSimpleArray();
                            break;
                        default:
                            throw new InvalidArgumentException("Mocked service locator cannot provide ['$name']");
                            break;
                    }
                }
                ));
        
        $service = new UserResourcesService();
        
        $service->setDao($this->dao);
        $service->setServiceLocator($this->serviceLocator);
        
        $rs = $service->getResourcesByUserId(1);
        
        $key = ArrayUserResourcesToSimpleArray::KEY_NAME;
        
        $this->assertTrue(is_array($rs), 'Asserting that returned value from service is array');
        
        $this->assertTrue(array_key_exists($key, $rs), 
                'Asserting that the returned array has the appropriate key');
        
        $this->assertSame(count($rs[$key]), count($arrAllowedResources), 
                'Asserting that the returned array has the same number of elements as the returned resultset from the DAO');
    }
}


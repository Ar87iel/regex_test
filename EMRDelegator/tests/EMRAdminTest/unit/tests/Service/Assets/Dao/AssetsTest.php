<?php

namespace EMRAdminTest\unit\tests\Service\Asset\Dao;

use PHPUnit_Framework_TestCase;
use InvalidArgumentException;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;
use Application\Controller\Form\Assets\GetFacilityAsset;
use EMRAdmin\Service\Assets\Dao\Assets;
use EMRAdmin\Service\Assets\Dto\GetFacilityAssetRequest;
use EMRCore\Service\Assets\Facility;
use EMRCore\Service\Assets\Dto\FacilityPathRequest;
use EMRCoreTest\Helper\Reflection;
use EMRAdmin\Service\Assets\Dto\GetUserAssetRequest;
use EMRCore\File\File;

/**
 *
 *
 * @category WebPT
 * @package EMRAdmin
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class AssetsTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var ServiceLocatorInterface 
     */
    private $serviceLocator;

    public function setUp()
    {
        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
    }

    /**
     * 
     * test that getFacilityAssetByCriteria follows the correct workflow calling its dependencies
     */
    public function testFacilityAssetDao()
    {
        $facilityAssetDto = new GetFacilityAssetRequest;

        $facilityAssetDto->setCompanyId(1);
        $facilityAssetDto->setFacilityId(1);

        //mock EMRCore Assets service
        $assetService = $this->getMock('EMRCore\Service\Assets\Facility', array('getPath'));

        $assetService->expects($this->once())->method('getPath')
                ->will($this->returnValue('data/assets/company/0/0/1/facility/0/0/1/logo.jpg'));

        //mock Calling for getExistingAsset since it has its own test
        $assetDao = $this->getMock('EMRAdmin\Service\Assets\Dao\Assets', array('getExistingAsset'));

        $assetDao->expects($this->once())->method('getExistingAsset')->withAnyParameters()
                ->will($this->returnValue('logo.jpg'));

        // Create a mock serviceLocator for providing new instances of objects.
        $this->serviceLocator->expects($this->any())->method('get')
                ->will($this->returnCallback(function($name)use($assetService)
                                {
                                    if ($name == 'EMRCore\Service\Assets\Dto\FacilityPathRequest')
                                    {
                                        return new FacilityPathRequest;
                                    }
                                    if ($name == 'EMRCore\Service\Assets\Facility')
                                    {
                                        return $assetService;
                                    }
                                    throw new InvalidArgumentException("Mocked ServiceManager cannot create name [$name].");
                                }));

        $assetDao->setServiceLocator($this->serviceLocator);

        $assetDao->getFacilityAssetByCriteria($facilityAssetDto);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetExistingAsset()
    {
        $assetDao = new Assets();

        //fake route
        $route = 'data/assets/company/0/0/1/facility/0/0/1/logo.jpg';

        $assetDao->setServiceLocator($this->serviceLocator);

        Reflection::invoke($assetDao, 'getExistingAsset', array($route));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testUserAssetDaoForSignature()
    {
        $userAsserRequest = new GetUserAssetRequest();
        $userAsserRequest->setUserid(1);
        $userAsserRequest->setAssetType("signature");

        $fakePath = "data/assets/company/0/0/1/facility/0/0/1/logo.jpg";

        $userAssetService = $this->getMock("EMRCore\Service\Assets\User");
        $userAssetService->expects($this->once())
                ->method("getSignatureFilename")
                ->with($this->equalTo($userAsserRequest->getUserid()))
                ->will($this->returnValue($fakePath));

        // Create a mock serviceLocator for providing new instances of objects.
        $this->serviceLocator->expects($this->any())->method('get')
                ->will($this->returnCallback(function($name) use($userAssetService)
                                {
                                    if ($name == "EMRCore\Service\Assets\User")
                                    {
                                        return $userAssetService;
                                    }

                                    throw new InvalidArgumentException("Mocked ServiceManager cannot create name [$name].");
                                }));

        $assetDao = new Assets();
        $assetDao->setServiceLocator($this->serviceLocator);
        $assetDao->getUserAssetByCriteria($userAsserRequest);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testUserAssetDaoForLogo()
    {
        $userAsserRequest = new GetUserAssetRequest();
        $userAsserRequest->setUserid(1);
        $userAsserRequest->setAssetType("logo");

        $fakePath = "data/assets/company/0/0/1/facility/0/0/1/logo.jpg";

        $userAssetService = $this->getMock("EMRCore\Service\Assets\User");
        $userAssetService->expects($this->once())
                ->method("getLogoFilename")
                ->with($this->equalTo($userAsserRequest->getUserid()))
                ->will($this->returnValue($fakePath));

        // Create a mock serviceLocator for providing new instances of objects.
        $this->serviceLocator->expects($this->any())->method('get')
                ->will($this->returnCallback(function($name) use($userAssetService)
                                {
                                    if ($name == "EMRCore\Service\Assets\User")
                                    {
                                        return $userAssetService;
                                    }

                                    throw new InvalidArgumentException("Mocked ServiceManager cannot create name [$name].");
                                }));

        $assetDao = new Assets();
        $assetDao->setServiceLocator($this->serviceLocator);
        $assetDao->getUserAssetByCriteria($userAsserRequest);
    }
}
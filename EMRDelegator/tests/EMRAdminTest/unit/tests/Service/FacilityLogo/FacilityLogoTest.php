<?php
namespace EMRAdminTest\unit\tests\Service\FacilityLogo;

use EMRAdmin\Service\FacilityLogo\Dto\SaveFacilityLogoRequest;
use EMRAdmin\Service\FacilityLogo\FacilityLogo;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class FacilityLogoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $esbDao;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $fileSystemDao;

    /**
     * @var FacilityLogo
     */
    private $service;

    public function setUp()
    {
        $this->fileSystemDao = $this->getMock('EMRAdmin\Service\FacilityLogo\Dao\Logo');

        $this->esbDao = $this->getMock('EMRAdmin\Service\FacilityLogo\Dao\Esb');

        $this->service = new FacilityLogo;
        $this->service->setEsbDao($this->esbDao);
        $this->service->setLogoDao($this->fileSystemDao);
    }

    public function testStoresTemporarilyAndSendsEsbRequest()
    {
        $request = new SaveFacilityLogoRequest;

        $this->fileSystemDao->expects($this->once())->method('storeTemporarily')
            ->with($this->equalTo($request));

        $this->esbDao->expects($this->once())->method('saveFacilityLogo')
            ->with($this->equalTo($request));

        $this->service->saveFacilityLogo($request);
    }
}
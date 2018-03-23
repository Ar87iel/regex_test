<?php

namespace EMRAdminTest\unit\tests\Service;

use EMRAdmin\Service\Facility\FacilityWithIntegrationTokenFactory;
use Zend\ServiceManager\ServiceManager;

class FacilityWithIntegrationTokenFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var FacilityWithIntegrationTokenFactory */
    private $sut;
    /** @var  ServiceManager */
    private $sm;

    protected function setUp()
    {
        parent::setUp();
        $this->sut = new FacilityWithIntegrationTokenFactory();
        $this->sm = new ServiceManager();
    }

    public function testBuildWithoutExtIdFacilityService()
    {
        $this->sm->setService(
            'EMRAdmin\Service\Facility\Dao\Esb',
            $this->getMock('\EMRAdmin\Service\Facility\Dao\Esb')
        );

        self::assertInstanceOf('\EMRAdmin\Service\Facility\FacilityInterface', $this->sut->createService($this->sm));
    }

    public function testBuildWithExtIdFacilityService()
    {
        $this->sm->setService(
            'EMRAdmin\Service\Facility\Dao\Esb',
            $this->getMock('\EMRAdmin\Service\Facility\Dao\Esb')
        );

        $this->sm->setService(
            'EMRAdmin\Service\ExternalId\Facility',
            $this->getMock('\EMRAdmin\Service\ExternalId\FacilityIntegrationTokenInterface')
        );

        self::assertInstanceOf('\EMRAdmin\Service\Facility\FacilityInterface', $this->sut->createService($this->sm));
    }
}


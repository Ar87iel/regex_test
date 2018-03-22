<?php


namespace EMRAdminTest\unit\tests\Service\ExternalId;


use EMRAdmin\Service\ExternalId\FacilityFactory;
use Zend\ServiceManager\ServiceManager;

class FacilityFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ServiceManager */
    private $sl;
    /** @var  FacilityFactory */
    private $sut;

    public function setup(){
        parent::setup();
        $this->sl = new ServiceManager();
        $this->sut = new FacilityFactory();
    }

    public function testBuildFacilityServiceWithoutConfig(){
        $result = $this->sut->createService($this->sl);
        self::assertInstanceOf('EMRAdmin\Service\ExternalId\Facility', $result);
    }

    public function testBuildFacilityServiceWithUrlConfig(){
        $config = array(
            'ExternalIdService' => array(
                'url' => $url = 'https://blah.blah',
                'client_options' => array(),
            ),
        );

        $this->sl->setService('config', $config);

        $result = $this->sut->createService($this->sl);
        self::assertInstanceOf('EMRAdmin\Service\ExternalId\Facility', $result);
    }
}

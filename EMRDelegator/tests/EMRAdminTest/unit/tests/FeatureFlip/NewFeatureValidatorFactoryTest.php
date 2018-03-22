<?php

namespace EMRAdminTest\unit\tests\src\FeatureFlip;

use EMRAdmin\FeatureFlip\NewFeatureValidatorFactory;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\Validator\ValidatorInterface;

/**
 * Checks feature validator factory output
 */
class NewFeatureValidatorFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var FactoryInterface
     */
    private $sut;

    /**
     * @var ServiceLocatorInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $serviceLocator;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->sut = new NewFeatureValidatorFactory();
        $this->serviceLocator = $this->createMock(ServiceLocatorInterface::class);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();

        unset($this->sut);
    }

    /**
     * Checks create service complete path
     */
    public function testCreateService()
    {
        $whitelist = ['feature-whitelist' => []];

        $this->serviceLocator->expects(static::any())
            ->method('get')
            ->will(static::returnValueMap([
                ['Config', $whitelist],
            ]));

        $result = $this->sut->createService($this->serviceLocator);

        static::assertInstanceOf(ValidatorInterface::class, $result);
    }

    /**
     * Checks exception functionality when there is missing config entries
     */
    public function testCreateServiceMissingConfig()
    {
        $this->setExpectedException(ServiceNotCreatedException::class);

        $this->serviceLocator->expects(static::any())
            ->method('get')
            ->will(static::returnValueMap([
                ['Config', []],
            ]));

        $this->sut->createService($this->serviceLocator);
    }

    /**
     * Checks exception functionality when there is no config
     */
    public function testCreateServiceNoConfig()
    {
        $this->setExpectedException(ServiceNotCreatedException::class);
        $this->sut->createService($this->serviceLocator);
    }
}

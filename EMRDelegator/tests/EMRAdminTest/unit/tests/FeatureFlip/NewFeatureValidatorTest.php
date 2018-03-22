<?php

namespace EMRAdminTest\unit\tests\src\FeatureFlip;

use EMRAdmin\FeatureFlip\NewFeatureValidator;
use PHPUnit_Framework_TestCase;
use Zend\Validator\ValidatorInterface;

/**
 * Check new feature validator behavior
 */
class NewFeatureValidatorTest extends PHPUnit_Framework_TestCase
{
    const EXISTING_FF = 'existing-ff';

    /**
     * @var ValidatorInterface
     */
    private $sut;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->sut = new NewFeatureValidator([static::EXISTING_FF]);
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
     * @return array
     */
    public function provideIsValidData()
    {
        $existingFeatures = [static::EXISTING_FF => true];
        $newFeatures = ['x' => false];
        $mixedExistingAndNewFeatures = array_merge($existingFeatures, $newFeatures);
        $mixedExistingAndNewFeaturesReverse = array_reverse($mixedExistingAndNewFeatures);

        return [
            'valid scenario' => [$existingFeatures, true],
            'valid scenario with empty array' => [[], true],
            'fail by new FF' => [$newFeatures, false],
            'fail by new FF #2' => [$mixedExistingAndNewFeatures, false],
            'fail by new FF #3' => [$mixedExistingAndNewFeaturesReverse, false],
            'fail by bad data' => [false, false],
            'fail by bad data #2' => [123, false],
        ];
    }

    /**
     * @param mixed $data
     * @param bool  $expectedResult
     *
     * @dataProvider provideIsValidData
     */
    public function testIsValid($data, $expectedResult)
    {
        $result = $this->sut->isValid($data);

        static::assertEquals($expectedResult, $result);
    }

    /**
     * return array
     */
    public function provideGetMessagesData()
    {
        $data = $this->provideIsValidData();

        // remove the first 2 "valid" scenarios
        return array_splice($data, 2);
    }

    /**
     * @param mixed $data
     *
     * @dataProvider provideGetMessagesData
     */
    public function testGetMessages($data)
    {
        $expectedCount = 1;
        $validator = $this->sut;
        $validator->isValid($data);
        $messages = $validator->getMessages();

        static::assertInternalType('array', $messages);
        static::assertCount($expectedCount, $messages);
    }

    /**
     * Checks default messages value count
     */
    public function testGetMessagesDefault()
    {
        static::assertCount(0, $this->sut->getMessages());
    }
}

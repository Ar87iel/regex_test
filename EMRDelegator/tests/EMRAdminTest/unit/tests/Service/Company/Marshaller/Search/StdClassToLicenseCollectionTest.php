<?php

namespace EMRAdminTest\unit\tests\Service\Company\Marshaller\Search;

use EMRAdmin\Service\Company\Dto\Search\License;
use EMRAdmin\Service\Company\Dto\Search\LicenseCollection;
use EMRAdmin\Service\Company\Marshaller\Search\StdClassToLicenseCollection;
use EMRCore\PrototypeFactory;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class StdClassToLicenseCollectionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var StdClassToLicenseCollection
     */
    private $marshaller;

    public function setUp()
    {
        $this->marshaller = new StdClassToLicenseCollection;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToInvalidItemType()
    {
        $this->marshaller->marshall(array());
    }

    public function testMarshalsLicenses()
    {
        $item = new \stdClass();
        $item->therapistLicenseCount = 1;
        $item->therapistAssistantLicenseCount = 2;
        $item->studentLicenseCount = 0;
        $item->clericalLicenseCount = 10;
        $item->agentLicenseCount = 0;

        $expect = (object) array(
                    'therapist' => 1,
                    'assistant' => 2,
                    'student' => 0,
                    'clerical' => 10,
                    'agent' => 0,
        );

        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);
        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name)
                {
                    if ($name === 'EMRAdmin\Service\Company\Dto\Search\LicenseCollection')
                    {
                        return new LicenseCollection;
                    }

                    if ($name === 'EMRAdmin\Service\Company\Dto\Search\License')
                    {
                        return new License;
                    }

                    throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                }));

        $this->marshaller->setPrototypeFactory($prototypeFactory);

        $collection = $this->marshaller->marshall($item);

        $expected = array_keys((array) $expect);

        $this->assertSame($expected, $collection->pluck('type')->toArray());
    }

}
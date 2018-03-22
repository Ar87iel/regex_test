<?php
namespace ServiceTest\unit\tests\Marshaller;

use EMRCoreTest\Helper\Reflection;
use EMRDelegator\Model\Facility;
use PHPUnit_Framework_TestCase;
use Service\Controller\Marshaller\FacilityToFacilityIdArray;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class FacilityToFacilityIdArrayTest extends PHPUnit_Framework_TestCase
{
    public function testMarshalsFacilityId()
    {
        $facility = new Facility;
        $facility->setFacilityId(1);

        $marshaller = new FacilityToFacilityIdArray;

        $actual = Reflection::invoke($marshaller, 'marshalFacilityId', array($facility));

        $expected = array(
            'id' => $facility->getFacilityId(),
        );

        $this->assertSame($expected, $actual);
    }

    public function testMarshalsFacilityIds()
    {
        $facility = new Facility;
        $facility->setFacilityId(1);

        $facilities = array(
            $facility,
        );

        $marshaller = new FacilityToFacilityIdArray;

        $actual = Reflection::invoke($marshaller, 'marshalFacilityIds', array($facilities));

        $expected = array(
            array(
                'id' => $facility->getFacilityId()
            ),
        );

        $this->assertSame($expected, $actual);
    }

    public function testMarshalCallsMarshalFacilityIds()
    {
        $facility = new Facility;
        $facility->setFacilityId(1);

        $facilities = array(
            $facility,
        );

        $marshaller = $this->getMock('Service\Controller\Marshaller\FacilityToFacilityIdArray', array('marshalFacilityIds'));

        $marshaller->expects($this->once())->method('marshalFacilityIds')->with($facilities);

        /** @var FacilityToFacilityIdArray $marshaller */
        $marshaller->marshall($facilities);
    }
}
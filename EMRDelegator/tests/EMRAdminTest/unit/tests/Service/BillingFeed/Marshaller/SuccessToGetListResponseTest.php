<?php
namespace EMRAdminTest\unit\tests\Service\BillingFeed\Marshaller;

use EMRAdmin\Service\BillingFeed\Dto\BillingFeed;
use EMRAdmin\Service\BillingFeed\Dto\BillingFeedCollection;
use EMRAdmin\Service\BillingFeed\Marshaller\SuccessToGetListResponse;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use EMRCore\PrototypeFactory;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use stdClass;

class SuccessToGetListResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject
     */
    public $prototypeFactory;

    /**
     * @var SuccessToGetListResponse
     */
    private $marshaller;

    public function setUp()
    {
        /** @var PrototypeFactory|PHPUnit_Framework_MockObject_MockObject $prototypeFactory */
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);
        $this->prototypeFactory = $prototypeFactory;
        $this->prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(function($name)
            {
                if ($name === 'EMRAdmin\Service\BillingFeed\Dto\BillingFeedCollection')
                {
                    return new BillingFeedCollection;
                }

                if ($name === 'EMRAdmin\Service\BillingFeed\Dto\BillingFeed')
                {
                    return new BillingFeed;
                }

                throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
            }));

        $this->marshaller = new SuccessToGetListResponse;
        $this->marshaller->setPrototypeFactory($this->prototypeFactory);
    }

    public function testMarshals()
    {
        $id = 1;
        $name = 'asdf';

        $success = new Success;
        $success->setPayload(array(
            (object) array(
                'Fd_FeedID' => $id,
                'Fd_FeedTitle' => $name,
            ),
        ));

        $billingFeed = new BillingFeed();
        $billingFeed->setId($id);
        $billingFeed->setName($name);

        $billingFeedCollection = new BillingFeedCollection();
        $billingFeedCollection->push($billingFeed);

        $result = $this->marshaller->marshall($success);

        $this->assertInstanceOf('EMRAdmin\Service\BillingFeed\Dto\BillingFeedCollection', $result);
        $this->assertCount(1, $result);

        //Assert the same
        $this->assertEquals($billingFeedCollection, $result);

        /** @var BillingFeed $billingFeedResult */
        $billingFeedResult = $result->shift();
        $this->assertSame($id, $billingFeedResult->getId());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToUnexpectedInputType()
    {
        $this->marshaller->marshall(new stdClass);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotMarshalsDueToUnexpectedPayloadType()
    {
        $success = new Success;
        $success->setPayload(new stdClass);

        $this->marshaller->marshall($success);
    }
}
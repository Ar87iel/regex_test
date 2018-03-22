<?php

namespace EMRAdminTest\unit\tests\Service\BillingFeed\Marshaller;

use EMRAdmin\Service\BillingFeed\Marshaller\BillingFeedCollectionToArray;
use EMRAdmin\Service\BillingFeed\Dto\BillingFeedCollection;
use EMRAdmin\Service\BillingFeed\Dto\BillingFeed;
use PHPUnit_Framework_TestCase;
use InvalidArgumentException;

class BillingFeedCollectionToArrayTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var BillingFeedCollectionToArray 
     */
    private $marshaller;

    public function setup()
    {
        $this->marshaller = new BillingFeedCollectionToArray();
    }

    /**
     * Test that the marshalling will not occur due to an invalid type as an argument to the marshaller.
     * 
     * @expectedException InvalidArgumentException
     */
    public function testNoMarshallingBecauseInvalidType()
    {
        $this->marshaller->marshall(array());
    }

    /**
     * 
     */
    public function testCorrectMatshalling()
    {
        $feedId = 20;
        $feedName = 'Feed name';

        /** @var BillingFeedCollection $collection */
        $collection = new BillingFeedCollection();

        /** @var BillingFeed $feed */
        $feed = new BillingFeed();

        $feed->setId($feedId);
        $feed->setName($feedName);

        $collection->push($feed);

        $marshalledFeed = $this->marshaller->marshall($collection);

        $this->assertTrue(is_array($marshalledFeed), 'Assert that the marshalled data is an array');
        $this->assertArrayHasKey($feedId, $marshalledFeed, 'Assert that the feed id is a key from the returned
            marshalled array');
        $this->assertSame($feedName, $marshalledFeed[$feedId], 'Assert that the feed name remains the same as the
            original one and is contained within the correct array key');
    }

}
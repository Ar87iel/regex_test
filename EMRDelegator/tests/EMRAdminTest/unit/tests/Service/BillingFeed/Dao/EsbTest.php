<?php
namespace EMRAdminTest\unit\tests\Service\BillingFeed\Dao;

use EMRAdmin\Service\BillingFeed\Dao\Esb;
use EMRAdmin\Service\BillingFeed\Dto\BillingFeed;
use EMRAdmin\Service\BillingFeed\Dto\BillingFeedCollection;
use EMRAdmin\Service\BillingFeed\Marshaller\SuccessToGetListResponse;
use EMRCore\Config\Service\PrivateService\Esb\Dto\Route;
use EMRCore\Zend\Http\ClientWrapper;
use EMRCore\Zend\module\Service\src\Response\Parser\Json;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Zend\Http\Response;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class EsbTest extends PHPUnit_Framework_TestCase
{
    /**
     * Parses the response from a mocked ESB Request. This actually executes the parser
     * and marshaller so there should be no surprises regarding the Success payload data type.
     */
    public function testGetListProvidesBillingFeedCollection()
    {
        $id1 = 1;
        $name1 = 'asdf';

        $id2 = 2;
        $name2 = 'qwer';

        // Create a route to use instead of bootstrapping the application config.
        $route = new Route;
        $route->setUri('asdf');
        $route->setMethod('GET');

        // Create a value to be returned by a mock Client.
        $routeReturnValue = json_encode(array(
            'content' => array(
                'response' => array(
                    array(
                        'Fd_FeedID' => $id1,
                        'Fd_FeedTitle' => $name1,
                    ),
                    array(
                        'Fd_FeedID' => $id2,
                        'Fd_FeedTitle' => $name2,
                    ),
                ),
            ),
        ));

        // Create a response container for the content value.
        $response = new Response;
        $response->setStatusCode(200);
        $response->setContent($routeReturnValue);

        // Create the DAO. It is ok that it is a mock for this test.
        $dao = $this->getMock('EMRAdmin\Service\BillingFeed\Dao\Esb', array(
            'getRoute',
        ));

        // Always return the custom route.
        $dao->expects($this->any())->method('getRoute')->withAnyParameters()->will($this->returnValue($route));

        // Create a mock Client. This will be executed by our client wrapper.
        $client = $this->createMock('Zend\Http\Client');
        $client->expects($this->once())->method('send')->will($this->returnValue($response));

        // Create a ClientWrapper to execute our mock Client.
        $clientWrapper = new ClientWrapper;
        $clientWrapper->setClient($client);
        $clientWrapper->setLogger($this->getMock('Logger', array(), array(), '', false));

        // Create a mock EsbFactory to return our ClientWrapper.
        $esbFactory = $this->getMock('EMRCore\EsbFactory', array(), array(), '', false);
        $esbFactory->expects($this->once())
            ->method('getClient')
            ->with($this->equalTo($route->getUri()), $this->equalTo($route->getMethod()))
            ->will($this->returnValue($clientWrapper));

        // Create a prototype factory mock to produce instances of containers for the marshaller.
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $prototypeFactory->expects($this->any())
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

        // Create a service locator mock to produce instances of our parser and marshaller.
        $serviceLocator = $this->createMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->any())->method('get')
            ->will($this->returnCallback(function($name) use ($prototypeFactory)
            {
                if ($name === 'EMRCore\Zend\module\Service\src\Response\Parser\Json')
                {
                    return new Json;
                }

                if ($name === 'EMRAdmin\Service\BillingFeed\Marshaller\SuccessToGetListResponse')
                {
                    $marshaller = new SuccessToGetListResponse;
                    $marshaller->setPrototypeFactory($prototypeFactory);
                    return $marshaller;
                }

                throw new InvalidArgumentException("Mocked ServiceLocatorInterface cannot provide [$name].");
            }));

        /**
         * Set dependencies into the DAO.
         * @var Esb $dao
         */
        $dao->setServiceLocator($serviceLocator);
        $dao->setEsbFactory($esbFactory);

        // Get the list.
        $collection = $dao->getList();

        $expected = array(
            $id1,
            $id2,
        );

        $this->assertSame($expected, $collection->pluck('id')->toArray());
    }
}
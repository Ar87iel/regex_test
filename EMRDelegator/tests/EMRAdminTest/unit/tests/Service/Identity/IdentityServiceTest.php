<?php
/**
 * @category WebPT 
 * @package EMRAdmin
 * @author: kevinkucera
 * 9/30/13 4:02 PM
 */
namespace EMRAdminTest\unit\tests\Service\Identity;

use EMRAdmin\Service\Identity\IdentityService;
use EMRCore\Service\Identity\Dto\SearchCriteria;
use PHPUnit_Framework_TestCase;

class IdentityServiceTest extends PHPUnit_Framework_TestCase
{

    public function testSearchIdentity()
    {
        $searchCriteria = new SearchCriteria();
        $expected = 'OK';

        $esbDao = $this->createMock('\EMRAdmin\Service\Identity\Dao\Esb');
        $esbDao->expects($this->once())
            ->method('searchIdentity')
            ->with($searchCriteria)
        ->will($this->returnValue($expected));

        $service = new IdentityService();
        $service->setEsbDao($esbDao);
        $result = $service->searchIdentity($searchCriteria);
        $this->assertEquals($expected, $result);
    }

}
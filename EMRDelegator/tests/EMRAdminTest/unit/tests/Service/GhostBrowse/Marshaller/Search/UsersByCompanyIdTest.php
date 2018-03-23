<?php

namespace EMRAdminTest\unit\tests\Service\GhostBrowse\Marshaller\Search;

use EMRAdmin\Service\GhostBrowse\Marshaller\Search\SuccessToGhostBrowseResponse;
use EMRAdmin\Service\GhostBrowse\Marshaller\Search\UsersByCompanyId;
use EMRCore\Zend\module\Service\src\Response\Dto\Success;
use EMRAdmin\Service\GhostBrowse\Dto\SearchGhostBrowseResponseCollection;
use EMRAdmin\Service\GhostBrowse\Dto\Search\UsersByCompanyId as UsersByCompanyIdDto;
use PHPUnit_Framework_MockObject_MockObject;
use \stdClass;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class UsersByCompanyIdTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * @var SuccessToGhostBrowseResponse
     */
    private $marshaller;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $mockServiceLocator;
    
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $mockPrototypeFactory;
    
    public function setUp()
    {
        $this->marshaller = new UsersByCompanyId();
        $this->mockPrototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false );
        $this->mockServiceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
    
    }
    
    public function testUsersByCompanyId()
    {
        //A fake User
        $user = array(
            'id' => 1,
            'fullName' => "asd",
            'userName' => 'asd',
            'userType' => 2,
            'userStatus' => 'I',
            'isAdmin' => 0
        );

        //A fake facility
        $facility = array(
            'name' => "asd",
            'facilityId' => 1,
            'users' => $user,
        );
        
        $std = new stdClass();
        
        $std->searchByCompanyId = array(
            (object)$facility
        );
        
        $success =  new Success();
        
        $success->setPayload($std);
                
        $this->marshaller->marshall($success);
    }
    
}
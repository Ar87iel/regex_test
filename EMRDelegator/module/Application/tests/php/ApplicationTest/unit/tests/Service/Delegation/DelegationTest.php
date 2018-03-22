<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 4/9/13 11:37 AM
 */

use Application\Service\Delegation\Delegation;
use Application\Service\Delegation\Dto\Delegate as DelegateDto;

class DelegationTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        parent::setUp();
    }

    /**
     * @expectedException RuntimeException
     */
    public function testDelegateCallsGetCompanyWithIdentityAndFacilityParams() {
        $identityId = 3;
        $facilityId = 4;
        $token = 'token';
        $delegateDto = new DelegateDto();
        $delegateDto->setFacilityId($facilityId);
        $delegateDto->setUserId($identityId);
        $delegateDto->setToken($token);

        $delegateMock = $this->getMock('Application\Service\Delegation\Delegation',
            array('getCompanyModel'));
        $delegateMock->expects($this->once())
            ->method('getCompanyModel')
            ->with($identityId, $facilityId)
            // throw an exception when called to escape execution of method so we dont have to mock a bunch
            // of unnecessary stuff
            ->will($this->throwException(new RuntimeException()));
        $delegateMock->delegate($delegateDto);
    }

    public function testDelegateCallsGetCookiePassingCompanyIdAsParameter() {
        // Delegation to the appropriate cluster.
        $clusterId = 1;
        $cookie = 'foo';
        $companyId = 7;
        $userId = 3;
        $token = 'asdf';
        $testUrl = 'http://foo';

        $delegateDto = new DelegateDto();
        $delegateDto->setUserId($userId);
        $delegateDto->setToken($token);

        $clusterMock = $this->getMock('stdClass', array('getClusterId'));
        $clusterMock->expects($this->once())
            ->method('getClusterId')
            ->will($this->returnValue($clusterId));

        $companyModelMock = $this->getMock('stdClass', array('getCluster', 'getCompanyId'));
        $companyModelMock->expects($this->once())
            ->method('getCluster')
            ->will($this->returnValue($clusterMock));
        $companyModelMock->expects($this->once())
            ->method('getCompanyId')
            ->will($this->returnValue($companyId));


        $delegateMock = $this->getMock('Application\Service\Delegation\Delegation',
            array('getCompanyModel', 'getCookie', 'getInterceptorRedirectUrl'));
        $delegateMock->expects($this->once())
            ->method('getCompanyModel')
            ->will($this->returnValue($companyModelMock));
        $delegateMock->expects($this->once())
            ->method('getInterceptorRedirectUrl')
            ->will($this->returnValue($testUrl));
        $delegateMock->expects($this->once())
            ->method('getCookie')
            ->with($clusterId)
            ->will($this->returnValue($cookie));

        /** @var $delegateMock Delegation */
        $result = $delegateMock->delegate($delegateDto);
        $this->assertEquals($cookie, $result->getCookie());
    }

    public function testDtoUrlIsSetToInterceptorUrlConcatedWithTokenAndCompanyId() {
        // Delegation to the appropriate cluster.
        $clusterId = 1;
        $cookie = 'foo';
        $companyId = 7;
        $userId = 3;
        $token = 'asdf';
        $baseUrl = 'http://foo';

        $delegateDto = new DelegateDto();
        $delegateDto->setUserId($userId);
        $delegateDto->setToken($token);

        $testUrl = $baseUrl
            . "?companyId=" . $companyId
            . "&wpt_sso_token=" . $token;

        $clusterMock = $this->getMock('stdClass', array('getClusterId'));
        $clusterMock->expects($this->once())
            ->method('getClusterId')
            ->will($this->returnValue($clusterId));

        $companyModelMock = $this->getMock('stdClass', array('getCluster', 'getCompanyId'));
        $companyModelMock->expects($this->once())
            ->method('getCluster')
            ->will($this->returnValue($clusterMock));
        $companyModelMock->expects($this->once())
            ->method('getCompanyId')
            ->will($this->returnValue($companyId));


        $delegateMock = $this->getMock('Application\Service\Delegation\Delegation',
            array('getCompanyModel', 'getInterceptorBaseUrl', 'getCookie'));
        $delegateMock->expects($this->once())
            ->method('getCompanyModel')
            ->will($this->returnValue($companyModelMock));
        $delegateMock->expects($this->once())
            ->method('getInterceptorBaseUrl')
            ->will($this->returnValue($baseUrl));

        /** @var $delegateMock Delegation */
        $result = $delegateMock->delegate($delegateDto);
        $this->assertEquals($testUrl, $result->getUrl());
    }

    public function testDtoUrlIsSetToInterceptorUrlConcatedWithTokenAndCompanyIdAndFacilityId() {
        // Delegation to the appropriate cluster.
        $clusterId = 1;
        $cookie = 'foo';
        $companyId = 7;
        $userId = 3;
        $token = 'asdf';
        $facilityId = 11;
        $baseUrl = 'http://foo';

        $delegateDto = new DelegateDto();
        $delegateDto->setFacilityId($facilityId);
        $delegateDto->setUserId($userId);
        $delegateDto->setToken($token);

        $testUrl = $baseUrl
            . "?companyId=" . $companyId
            . "&wpt_sso_token=" . $token
            . "&facilityId=" . $facilityId;

        $clusterMock = $this->getMock('stdClass', array('getClusterId'));
        $clusterMock->expects($this->once())
            ->method('getClusterId')
            ->will($this->returnValue($clusterId));

        $companyModelMock = $this->getMock('stdClass', array('getCluster', 'getCompanyId'));
        $companyModelMock->expects($this->once())
            ->method('getCluster')
            ->will($this->returnValue($clusterMock));
        $companyModelMock->expects($this->once())
            ->method('getCompanyId')
            ->will($this->returnValue($companyId));


        $delegateMock = $this->getMock('Application\Service\Delegation\Delegation',
            array('getCompanyModel', 'getInterceptorBaseUrl', 'getCookie'));
        $delegateMock->expects($this->once())
            ->method('getCompanyModel')
            ->will($this->returnValue($companyModelMock));
        $delegateMock->expects($this->once())
            ->method('getInterceptorBaseUrl')
            ->will($this->returnValue($baseUrl));

        /** @var $delegateMock Delegation */
        $result = $delegateMock->delegate($delegateDto);
        $this->assertEquals($testUrl, $result->getUrl());
    }
}

<?php
/**
 * @category WebPT 
 * @package EMRDelegator
 * @author: kevinkucera
 * 10/16/13 4:19 PM
 */

namespace ServiceTest\Unit;

use EMRDelegator\Service\UserHasAgreement\Exception\UserHasAgreementNotFound;
use Zend\Http\Request;
use EMRDelegator\Service\UserHasAgreement\UserHasAgreement;

class UserHasAgreementControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject|\Service\Controller\UserHasAgreementController
     */
    private function getControllerMock($methods = array())
    {
        return $this->getMock('Service\Controller\UserHasAgreementController', $methods);
    }

    public function testGetUserAgreementAction()
    {
        $userId = 4;
        $agreementId = 9;
        $marshaled = 'uha marshalled';
        $content = array('userHasAgreement'=>$marshaled);
        $expected = 'OK';

        $query = $this->getMock('stdClass', array('get'));
        $query->expects($this->at(0))
            ->method('get')
            ->with('userId')
            ->will($this->returnValue($userId));
        $query->expects($this->at(1))
            ->method('get')
            ->with('agreementId')
            ->will($this->returnValue($agreementId));

        $request = $this->getMock('Zend\Http\Request');
        $request->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($query));

        $userHasAgreement = 'userHasAgreement';

        $agreementService = $this->getMock('EMRDelegator\Service\UserHasAgreement\UserHasAgreement');
        $agreementService->expects($this->once())
            ->method('getUserHasAgreement')
            ->with($userId, $agreementId)
            ->will($this->returnValue($userHasAgreement));

        $controller = $this->getControllerMock(array(
            'getRequest',
            'getQuery',
            'getUserHasAgreementService',
            'getContentResponse',
            'getMarshaledUserHasAgreement'
        ));
        $controller->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));
        $controller->expects($this->once())
            ->method('getUserHasAgreementService')
            ->will($this->returnValue($agreementService));
        $controller->expects($this->once())
            ->method('getMarshaledUserHasAgreement')
            ->with($userHasAgreement)
            ->will($this->returnValue($marshaled));
        $controller->expects($this->once())
            ->method('getContentResponse')
            ->with($content)
            ->will($this->returnValue($expected));
        $result = $controller->getUserAgreementAction();
        $this->assertEquals($expected, $result);
    }

    /**
     * In charge to get test values for check if values returned are correct.
     *
     * @return stdClass[]|null
     */
    public function provideGetBulkAgreementAction()
    {
        $userId = '[1,37,38]';
        $agreementId = 8;

        $response = [
            (object) [
                'userId' => 38,
                'agreementId' => 12,
                'version' => 20160428.3,
                'agreementTypeId' => 4,
                'type' => 'BAA',
            ],
            (object) [
                'userId' => 38,
                'agreementId' => 12,
                'version' => 20160428.3,
                'agreementTypeId' => 4,
                'type' => 'BAA',
            ],
        ];

        return [
            'Retrieve result' => [$response, $userId, $agreementId, $response],
            'Without result' => [null, $userId, $agreementId, null],
        ];
    }

    /**
     * Tests to request with POST method and bulk data.
     *
     * @param string[]|null $response
     * @param string $userId
     * @param int $agreementId
     * @param string $expected
     *
     * @dataProvider provideGetBulkAgreementAction
     */
    public function testGetBulkAgreementAction($response, $userId, $agreementId, $expected)
    {
        $query = $this->getMock('stdClass', ['get']);
        $query->expects(self::any())
            ->method('get')
            ->with('userId')
            ->will(self::returnValue($userId));
        $query->expects(self::any())
            ->method('get')
            ->with('agreementId')
            ->will(self::returnValue($agreementId));

        $request = $this->getMock(Request::class);
        $request->expects(self::any())
            ->method('getQuery')
            ->will(self::returnValue($query));

        $request->expects(self::any())
            ->method('getMethod')
            ->will($this->returnValue('POST'));

        $request->expects(self::any())
            ->method('getPost')
            ->will(self::returnValue($userId));

        $agreementService = $this->getMock(UserHasAgreement::class);
        $agreementService->expects(self::any())
            ->method('getBulkUserHasAgreement')
            ->with(json_decode($userId))
            ->will(self::returnValue([]));

        $controller = $this->getControllerMock([
            'getRequest',
            'getQuery',
            'getUserHasAgreementService',
            'getContentResponse'
        ]);

        $controller->expects(self::any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $controller->expects(self::any())
            ->method('getContentResponse')
            ->will(self::returnValue($response));

        $controller->expects(self::any())
            ->method('getUserHasAgreementService')
            ->will($this->returnValue($agreementService));

        $result = $controller->getUserAgreementAction();

        self::assertEquals(
            $expected,
            $result,
            'The test goes wrong.'
        );
    }

    /**
     * Tests to check if has an exception and how is the output to this case.
     *
     * @expectedException UserHasAgreementNotFound
     */
    public function testGetBulkAgreementActionException()
    {
        $response = null;
        $userId = '[]';
        $agreementId = 8;
        $expected = null;

        $query = $this->getMock('stdClass', ['get']);
        $query->expects(self::any())
            ->method('get')
            ->with('userId')
            ->will(self::returnValue($userId));
        $query->expects(self::any())
            ->method('get')
            ->with('agreementId')
            ->will(self::returnValue($agreementId));

        $request = $this->getMock(Request::class);
        $request->expects(self::any())
            ->method('getQuery')
            ->will(self::returnValue($query));

        $request->expects(self::any())
            ->method('getMethod')
            ->will($this->returnValue('POST'));

        $request->expects(self::any())
            ->method('getPost')
            ->will(self::returnValue($userId));

        $agreementService = $this->getMock(UserHasAgreement::class);
        $agreementService->expects(self::any())
            ->method('getBulkUserHasAgreement')
            ->with(json_decode($userId))
            ->will(self::returnValue([]));

        $controller = $this->getControllerMock([
            'getRequest',
            'getQuery',
            'getUserHasAgreementService',
            'getContentResponse'
        ]);

        $controller->expects(self::any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $controller->expects(self::any())
            ->method('getContentResponse')
            ->will(self::returnValue($response));

        $controller->expects(self::any())
            ->method('getUserHasAgreementService')
            ->will($this->returnValue($agreementService));

        $this->setExpectedException(UserHasAgreementNotFound::class);
        $controller->getUserAgreementAction();
    }
}
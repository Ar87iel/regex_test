<?php
/**
 * @category WebPT 
 * @package EMRAdmin
 * @author: kevinkucera
 * 10/4/13 9:27 AM
 */
namespace EMRAdminTest\unit\tests\Service\Reminder;

use PHPUnit_Framework_TestCase;
use EMRAdmin\Service\Reminder\QuotaService;
use \EMRModel\Reminder\Quota as QuotaModel;

class QuotaServiceTest extends PHPUnit_Framework_TestCase
{

    /**
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject|\EMRAdmin\Service\Reminder\Dao\Esb
     */
    private function getDaoMock($methods = array())
    {
        return $this->createMock('EMRAdmin\Service\Reminder\Dao\Esb');
    }

    public function testGetQuotas()
    {
        $facilityId = 9;
        $expected = 'OK';

        $dao = $this->getDaoMock(array('getFacilityQuotas'));
        $dao->expects($this->once())
            ->method('getFacilityQuotas')
            ->with($facilityId)
            ->will($this->returnValue($expected));

        $service = new QuotaService();
        $service->setEsbDao($dao);

        $result = $service->getQuotas($facilityId);
        $this->assertEquals($expected, $result);
    }

    public function testSetQuotas()
    {
        $quota = new QuotaModel();
        $expected = 'OK';

        $dao = $this->getDaoMock(array('setFacilityQuota'));
        $dao->expects($this->once())
            ->method('setFacilityQuota')
            ->with($quota)
            ->will($this->returnValue($expected));

        $service = new QuotaService();
        $service->setEsbDao($dao);

        $result = $service->setQuota($quota);
        $this->assertEquals($expected, $result);
    }

}
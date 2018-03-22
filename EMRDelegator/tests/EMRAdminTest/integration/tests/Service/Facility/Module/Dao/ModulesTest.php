<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kkucera
 * 10/01/13 11:41 AM
 */

use EMRAdmin\Service\Facility\Module\Dao\Modules as ModulesDao;
use EMRModel\Facility\Module;

class ModulesTest extends \EMRCoreTest\lib\PhpUnit\Framework\DatabaseTestCase
{
    /** @var  ModulesDao */
    protected $dao;

    /** @var  ModulesDao[] */
    protected $modules = array();

    /**
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDaoMock($methods = array()){
        return $this->getMock('EMRAdmin\Service\Facility\Modules\Dao\Modules', $methods);
    }

    public function setUp() {
        parent::setUp();
        $this->dropTables();
        $this->createTables();
        $this->dao = new ModulesDao();
        $this->dao->setDefaultMasterSlave($this->getMasterSlaveDoctrineAdapter());
    }

    protected function createTables() {
        $this->executeSql(require __DIR__ . '/sql/create_tables.sql.php');
    }

    protected function dropTables() {
        $this->executeSql(require __DIR__ . '/sql/drop_tables.sql.php');
    }

    protected function insertData() {
        $this->executeSql(require __DIR__ .'/sql/insert_data.sql.php');
    }

    public function testGetListReturnsEmpty()
    {
        $r = $this->dao->getList();
        $this->assertEmpty($r);
    }

    public function testGetListReturnsSortedResults()
    {
        $this->insertData();
        $result = $this->dao->getList();
        $this->assertCount(3, $result);

        for($i=1; $i<4; $i++)
        {
            $module = $result[$i-1];
            $this->assertInstanceOf('EMRModel\Facility\Module', $module);
            $this->assertEquals($i, $module->getId());
            $this->assertEquals('Module'.$i, $module->getName());
            $this->assertEquals('Module '.$i.' Desc', $module->getDescription());
        }
    }

}

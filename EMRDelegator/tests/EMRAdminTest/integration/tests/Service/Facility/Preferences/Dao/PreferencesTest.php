<?php
use EMRAdmin\Service\Facility\Preferences\Dao\Preferences;
use EMRModel\Facility\Preference;
use EMRModel\Facility\PreferenceGroup;

/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 9/30/13 11:41 AM
 */

class PreferencesTest extends \EMRCoreTest\lib\PhpUnit\Framework\DatabaseTestCase {
    /** @var  Preferences */
    protected $dao;

    /** @var  Preference[] */
    protected $preferences = array();
    /** @var  PreferenceGroup[] */
    protected $preferenceGroups = array();

    /**
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDaoMock($methods = array()){
        return $this->getMock('EMRAdmin\Service\Facility\Preferences\Dao\Preferences', $methods);
    }

    public function setUp() {
        parent::setUp();
        $this->dropTables();
        $this->createTables();
        $this->dao = new Preferences();
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
        // these models represent the inserted data, used to simplify testing
        $this->preferenceGroups[1] = new PreferenceGroup();
        $this->preferenceGroups[1]->setId(1);
        $this->preferenceGroups[1]->setName('One');
        $this->preferenceGroups[1]->setDescription('Group One');
        $this->preferenceGroups[1]->setOrder(2);

        $this->preferenceGroups[2] = new PreferenceGroup();
        $this->preferenceGroups[2]->setId(2);
        $this->preferenceGroups[2]->setName('Two');
        $this->preferenceGroups[2]->setDescription('Group Two');
        $this->preferenceGroups[2]->setOrder(1);

        $this->preferences[1] = new Preference();
        $this->preferences[1]->setId(1);
        $this->preferences[1]->setName('Foo');
        $this->preferences[1]->setDescription('Foo Desc');
        $this->preferences[1]->setGroupId(1);
        $this->preferences[1]->setOrder(2);

        $this->preferences[2] = new Preference();
        $this->preferences[2]->setId(2);
        $this->preferences[2]->setName('Bar');
        $this->preferences[2]->setDescription('Bar Cust');
        $this->preferences[2]->setGroupId(1);
        $this->preferences[2]->setOrder(1);

        $this->preferences[3] = new Preference();
        $this->preferences[3]->setId(3);
        $this->preferences[3]->setName('Biz');
        $this->preferences[3]->setDescription('Biz Desc');
        $this->preferences[3]->setGroupId(2);
        $this->preferences[3]->setOrder(3);
    }

    public function testGetListReturnsEmpty()
    {
        $r = $this->dao->getList();
        $this->assertEmpty($r);
    }

    public function testGetListReturnsSortedResults()
    {
        $this->insertData();
        $r = $this->dao->getList();
        $this->assertCount(2, $r);

        $groupTwo = $r[0];
        $this->assertPreferenceGroupMatch($this->preferenceGroups[2], $groupTwo);
        /** @var Preference[] $preferences */
        $preferences = $groupTwo->getPreferences();
        // order matters
        $this->assertPreferenceMatch($this->preferences[3], $preferences[0]);

        $groupOne = $r[1];
        $this->assertPreferenceGroupMatch($this->preferenceGroups[1], $groupOne);
        /** @var Preference[] $preferences */
        $preferences = $groupOne->getPreferences();
        // order matters
        $this->assertPreferenceMatch($this->preferences[2], $preferences[0]);
        $this->assertPreferenceMatch($this->preferences[1], $preferences[1]);
    }

    protected function assertPreferenceMatch(Preference $expected, Preference $test) {
        $this->assertEquals($expected->getId(), $test->getId());
        $this->assertEquals($expected->getName(), $test->getName());
        $this->assertEquals($expected->getDescription(), $test->getDescription());
        $this->assertEquals($expected->getOrder(), $test->getOrder());
        $this->assertEquals($expected->getGroupId(), $test->getGroupId());
    }

    protected function assertPreferenceGroupMatch(PreferenceGroup $expected, PreferenceGroup $test) {
        $this->assertEquals($expected->getId(), $test->getId());
        $this->assertEquals($expected->getName(), $test->getName());
        $this->assertEquals($expected->getDescription(), $test->getDescription());
        $this->assertEquals($expected->getOrder(), $test->getOrder());
    }


}

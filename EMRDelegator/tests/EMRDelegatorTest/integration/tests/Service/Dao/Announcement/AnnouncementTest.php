<?php

/**
 * @category WebPT
 * @package EMRDelegatorTest
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace EMRDelegatorTest\integration\tests\Service\Dao\Announcement;

use DateTime;
use Doctrine\ORM\Query;
use EMRCore\DoctrineConnector\Adapter\Adapter;
use EMRCore\DoctrineConnector\DoctrineConnectorFactory;
use EMRCore\PrototypeFactory;
use EMRCore\SqlConnector\SqlConnectorFactory;
use EMRCoreTest\lib\PhpUnit\Framework\DatabaseTestCase;
use EMRDelegator\Model\Announcement as AnnouncementModel;
use EMRDelegator\Service\Announcement\Dao\Announcement as AnnouncementDao;

class AnnouncementTest extends DatabaseTestCase
{
    /**
     * The adapter we are using.
     * @var Adapter
     */
    private static $defaultReaderWriter;

    /**
     * @var AnnouncementDao
     */
    private $dao;

    /**
     * Creates test tables.
     */
    private static function createTables()
    {
        self::executeSql(include __DIR__ . '/../../../../sql/common/create_table_announcement.sql.php');
    }
    
    /**
     * Drops test tables.
     */
    private static function dropTables()
    {
        self::executeSql(include __DIR__ . '/../../../../sql/common/drop_table_announcement.sql.php');
    }
    
    /**
     * Performs before class set up.
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

//        print_r(self::getWriterDoctrineAdapter()->getEntityManager()->getConnection()->fetchAll('select database()'));die;

        self::$defaultReaderWriter = self::getWriterDoctrineAdapter();
    }
    
    /**
     * Performs after class tear down.
     */
    public static function tearDownAfterClass()
    {
        self::$defaultReaderWriter->getCacheDriver()->deleteAll();
        self::dropTables();

        parent::tearDownAfterClass();
    }
    
    /**
     * Performs PhpUnit set up.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->dao = new AnnouncementDao;
        
        $this->dao->setDefaultReaderWriter(self::$defaultReaderWriter);

        self::dropTables();
        self::createTables();

        self::executeSql(include __DIR__ . '/../../../../sql/common/insert_announcements.sql.php');
    }

    /**
     * Tests announcement creation.
     */
    public function testAnnouncementCreateAndLoad()
    {
        $title = 'Announcement Title';
        $timeBegin = new DateTime('now');
        $timeEnd = new DateTime('+1 day');
        $description = 'Announcement Description';

        /**
         * @var AnnouncementModel $newAnnouncementCriteria
         */
        $model = new AnnouncementModel();
        $model->setTitle($title);
        $model->setDateTimeBegin($timeBegin);
        $model->setDateTimeEnd($timeEnd);
        $model->setDescription($description);
        $model->setPrivate(0);

        /**
         * @var AnnouncementModel $newModel
         */
        $newModel = $this->dao->create($model);

        $this->assertInstanceOf('\EMRDelegator\Model\Announcement', $newModel);
        $this->assertGreaterThan(0, $newModel->getAnnouncementId());

        $loadedModel = $this->dao->load($newModel->getAnnouncementId());
        $this->assertInstanceOf('\EMRDelegator\Model\Announcement', $newModel);
        $this->assertEquals($loadedModel->getAnnouncementId(), $newModel->getAnnouncementId());
    }

    /**
     * Tests announcement creation.
     */
    public function testAnnouncementDelete()
    {
        $id = 1;
        $announcementModelToDelete = $this->dao->load($id);

        $this->dao->delete($announcementModelToDelete);

        $announcementDeleted = $this->dao->load($id);
        $this->assertNull($announcementDeleted);
    }

    /**
     * Tests announcement load.
     */
    public function testAnnouncementLoad()
    {
        /**
         * @var AnnouncementModel $announcementModel
         */
        $announcementModel = $this->dao->load(2);
        $this->assertEquals($announcementModel->getAnnouncementId(), 2);
        $this->assertEquals($announcementModel->getTitle(), 'Announcement 2');
        $this->assertEquals($announcementModel->getDescription(), 'Announcement 2 Description');
        $this->assertEquals($announcementModel->getDateTimeEnd()->getTimestamp(), strtotime('today'));
    }

    /**
     * Tests announcement load all.
     */
    public function testAnnouncementGetList()
    {
        /**
         * @var AnnouncementModel[] $announcementModels
         */
        $list = $this->dao->getList();

        $this->assertEquals(count($list), 4);
    }

    /**
     * Tests announcement update.
     */
    public function testAnnouncementUpdate()
    {
        $id = 2;
        /**
         * @var AnnouncementModel $announcementModel
         */
        $announcementModel = $this->dao->load($id);
        $updatedTitle = "New Title";
        $announcementModel->setTitle($updatedTitle);
        $updatedModel = $this->dao->update($announcementModel);

        $loadedModel = $this->dao->load($id);

        $this->assertEquals($loadedModel->getTitle(), $updatedTitle);
    }
}

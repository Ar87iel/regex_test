<?php
namespace EMRDelegatorTest\Integration\Lib;

use Doctrine\ORM\Query;
use EMRCore\DoctrineConnector\Adapter\Adapter;
use EMRCore\DoctrineConnector\DoctrineConnectorFactory;
use PHPUnit_Framework_TestCase;

class DelegatorBaseTestCase extends PHPUnit_Framework_TestCase {
    /** @var Adapter */
    protected static $defaultReaderWriter;

    /**
     * setup tables for test
     */
    protected static function createTables() {
        $stmt = self::$defaultReaderWriter->getEntityManager()
            ->getConnection()
            ->prepare('SET foreign_key_checks = 0;');
        $stmt->execute();

        $stmt = self::$defaultReaderWriter->getEntityManager()
            ->getConnection()
            ->prepare(include __DIR__ . '/../sql/common/create_table_cluster.sql.php', true);
        $stmt->execute(array());

        $stmt = self::$defaultReaderWriter->getEntityManager()
            ->getConnection()
            ->prepare(include __DIR__ . '/../sql/common/create_table_company.sql.php', true);
        $stmt->execute();

        $stmt = self::$defaultReaderWriter->getEntityManager()
            ->getConnection()
            ->prepare(include __DIR__ . '/../sql/common/create_table_facility.sql.php', true);
        $stmt->execute();

        $stmt = self::$defaultReaderWriter->getEntityManager()
            ->getConnection()
            ->prepare(file_get_contents(dirname(__DIR__) . '/sql/common/create_sample.sql', true));
        $stmt->execute();

        $stmt = self::$defaultReaderWriter->getEntityManager()
            ->getConnection()
            ->prepare('SET foreign_key_checks = 1;');
        $stmt->execute();
    }

    /**
     * tear down tables after test
     */
    protected static function dropTables() {
        $stmt = self::$defaultReaderWriter->getEntityManager()
            ->getConnection()
            ->prepare('SET foreign_key_checks = 0;');
        $stmt->execute();

        $stmt = self::$defaultReaderWriter->getEntityManager()
            ->getConnection()
            ->prepare(include __DIR__ . '/../sql/common/drop_table_cluster.sql.php', true);
        $stmt->execute();

        $stmt = self::$defaultReaderWriter->getEntityManager()
            ->getConnection()
            ->prepare(include __DIR__ . '/../sql/common/drop_table_company.sql.php', true);
        $stmt->execute();

        $stmt = self::$defaultReaderWriter->getEntityManager()
            ->getConnection()
            ->prepare(include __DIR__ . '/../sql/common/drop_table_facility.sql.php', true);
        $stmt->execute();

        $stmt = self::$defaultReaderWriter->getEntityManager()
            ->getConnection()
            ->prepare(file_get_contents(dirname(__DIR__) . '/sql/common/drop_sample.sql', true));
        $stmt->execute();

        $stmt = self::$defaultReaderWriter->getEntityManager()
            ->getConnection()
            ->prepare('SET foreign_key_checks = 1;');
        $stmt->execute();
    }

    /**
     * setup
     */
    public static function setUpBeforeClass()
    {
        self::$defaultReaderWriter = DoctrineConnectorFactory::get('default_reader_writer');

        self::dropTables();
        self::createTables();
    }

    /**
     * tear down
     */
    public static function tearDownAfterClass()
    {
        self::dropTables();
    }
}
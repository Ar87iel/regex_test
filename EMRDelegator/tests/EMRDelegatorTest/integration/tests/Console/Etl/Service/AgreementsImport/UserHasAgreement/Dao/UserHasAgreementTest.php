<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 5/16/13 3:41 PM
 */

use Console\Etl\Service\AgreementsImport\UserHasAgreement\Dao\UserHasAgreement;
use Console\Etl\Service\Dao\SelectAndInsertInterface;

class UserHasAgreementTest extends SelectAndInsertTest {
    protected function createTables() {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/../../../../../../../sql/common/create_table_userhasagreement.sql.php');
        $db->execute(include __DIR__ . '/sql/create_tables.sql.php');
    }
    protected function dropTables() {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/../../../../../../../sql/common/drop_table_userhasagreement.sql.php');
        $db->execute(include __DIR__ . '/sql/drop_tables.sql.php');
    }
    protected function insertRecords() {
        $db = self::$adapter->getDatabase();
        $db->execute(include __DIR__ . '/sql/insert_records.sql.php');
    }

    /**
     * This method should return a valid insert statement for the destination table. It
     * should match the data that will be read from the inserted records for the test.
     * @return string
     */
    protected function getInsertStatementTestValue()
    {
        return "INSERT INTO UserHasAgreement VALUES (NULL,1,1,'ip','','2013-05-01 00:00:00','2013-05-01 00:00:00');";
    }

    /**
     * Return an instantiated dao w/ its reader & writer adapters dependencies supplied
     * @return SelectAndInsertInterface
     */
    protected function getDao()
    {
        $dao = new UserHasAgreement();
        $dao->setLegacyReaderWriter(self::$adapter->getDatabase());
        $dao->setDelegatorReaderWriter(self::$adapter->getDatabase());
        return $dao;

    }
}

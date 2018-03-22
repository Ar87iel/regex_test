<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 5/16/13 12:07 PM
 */

namespace Console\Etl\Service\AgreementsImport\AgreementType\Dao;


use Console\Etl\Service\Dao\SelectAndInsertInterface;
use Console\Etl\Service\Dto\InsertStatementResult;
use Console\Etl\SqlConnector\Legacy\ReaderWriterDiInterface as LegacyDiInterface;
use Console\Etl\SqlConnector\Delegator\ReaderWriterDiInterface as DelegatorDiInterface;
use EMRCore\SqlConnector\Dto\CountResult;
use EMRCore\SqlConnector\SqlConnectorAbstract;

class AgreementType implements LegacyDiInterface, DelegatorDiInterface, SelectAndInsertInterface {
    /** @var  SqlConnectorAbstract */
    protected $delegatorReaderWriter;
    /** @var  SqlConnectorAbstract */
    protected $legacyReaderWriter;

    /**
     * Executes an sql statement against the target schema
     * @param $sql string
     */
    public function executeInsertStatement($sql)
    {
        $this->delegatorReaderWriter->execute($sql);
    }

    /**
     * Toggle foreign key checks in the destination schema
     * @param bool $on
     */
    public function foreignKeyChecks($on = true)
    {
        $sql = 'SET FOREIGN_KEY_CHECKS = ' . ($on ? '1':'0');
        $this->delegatorReaderWriter->execute($sql);
    }

    /**
     * Return the total record count from the destination table
     * @return int
     */
    public function getDestinationRecordCount()
    {
        $sql = "select count(*) as `count` from AgreementType";
        $result = $this->delegatorReaderWriter->query($sql, array(), new CountResult());
        return $result->getResults()->current()->getCount();
    }

    /**
     * Return the total record count from the source table
     * @return int
     */
    public function getSourceRecordCount()
    {
        $sql = "select count(*) as `count` from USR_AgreementType";
        $result = $this->legacyReaderWriter->query($sql, array(), new CountResult());
        return $result->getResults()->current()->getCount();
    }

    /**
     * Return a hydrated result set of InsertStatementResult dtos
     * @return \Zend\Db\ResultSet\HydratingResultSet
     */
    public function selectSourceRecordsAsInsertStatements()
    {
        $sql = "SELECT CONCAT('INSERT INTO AgreementType VALUES (',
                    AgreementTypeId,',',
                    QUOTE(`Type`),',',
                    QUOTE(Description),',',
                    `Order`,',',
                    'NOW()',',',
                    'NOW()',');') as `sql`
                FROM USR_AgreementType;";

        $results = $this->legacyReaderWriter->query($sql, array(), new InsertStatementResult());
        return $results->getResults();
    }

    /**
     * Empty the destination table
     */
    public function truncateDestinationTable()
    {
        $sql = "truncate table AgreementType";
        $this->delegatorReaderWriter->execute($sql);
    }

    public function setLegacyReaderWriter(SqlConnectorAbstract $database)
    {
        $this->legacyReaderWriter = $database;
    }

    public function setDelegatorReaderWriter(SqlConnectorAbstract $database)
    {
        $this->delegatorReaderWriter = $database;
    }
}
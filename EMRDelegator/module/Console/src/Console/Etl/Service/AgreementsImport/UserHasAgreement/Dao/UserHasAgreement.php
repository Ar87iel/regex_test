<?php

namespace Console\Etl\Service\AgreementsImport\UserHasAgreement\Dao;


use Console\Etl\Service\Dao\SelectAndInsertInterface;
use Console\Etl\Service\Dto\InsertStatementResult;
use Console\Etl\SqlConnector\Legacy\ReaderWriterDiInterface as LegacyDiInterface;
use Console\Etl\SqlConnector\Delegator\ReaderWriterDiInterface as DelegatorDiInterface;
use EMRCore\SqlConnector\Dto\CountResult;
use EMRCore\SqlConnector\SqlConnectorAbstract;

class UserHasAgreement implements LegacyDiInterface, DelegatorDiInterface, SelectAndInsertInterface {
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
        $sql = "select count(*) as `count` from UserHasAgreement";
        $result = $this->delegatorReaderWriter->query($sql, array(), new CountResult());
        return $result->getResults()->current()->getCount();
    }

    /**
     * Return the total record count from the source table
     * @return int
     */
    public function getSourceRecordCount()
    {
        $sql = "select count(*) as `count` from USR_UserHasAgreement";
        $result = $this->legacyReaderWriter->query($sql, array(), new CountResult());
        return $result->getResults()->current()->getCount();
    }

    /**
     * Return a hydrated result set of InsertStatementResult dtos
     * @return \Zend\Db\ResultSet\HydratingResultSet
     */
    public function selectSourceRecordsAsInsertStatements()
    {
        $sql = "SELECT CONCAT('INSERT INTO UserHasAgreement VALUES (',
                    'NULL',',',
                    Usrhagrd_UserID,',',
                    Usrhagrd_AgreementID,',',
                    QUOTE(Usrhagrd_RemoteAddress),',',
                    QUOTE(Usrhagrd_JobTitle),',',
                    QUOTE(Usrhagrd_DateTime),',',
                    QUOTE(Usrhagrd_DateTime),');') as `sql`
                FROM USR_UserHasAgreement;";

        $results = $this->legacyReaderWriter->query($sql, array(), new InsertStatementResult());
        return $results->getResults();

    }

    /**
     * Empty the destination table
     */
    public function truncateDestinationTable()
    {
        $sql = "truncate table UserHasAgreement";
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
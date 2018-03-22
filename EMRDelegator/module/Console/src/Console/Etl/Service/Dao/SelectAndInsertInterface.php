<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 5/9/13 3:56 PM
 */

namespace Console\Etl\Service\Dao;


/**
 * Class SelectAndInsertInterface
 * Use this interface for simple etl patterns that involve selecting records
 * from the source schema as insert statements and running those insert statements
 * against the target schema;
 * @package Console\Etl\Service\Dao
 */
interface SelectAndInsertInterface {
    /**
     * Executes an sql statement against the target schema
     * @param $sql string
     */
    public function executeInsertStatement($sql);

    /**
     * Toggle foreign key checks in the destination schema
     * @param bool $on
     */
    public function foreignKeyChecks($on=true);

    /**
     * Return the total record count from the destination table
     * @return int
     */
    public function getDestinationRecordCount();

    /**
     * Return the total record count from the source table
     * @return int
     */
    public function getSourceRecordCount();

    /**
     * Return a hydrated result set of InsertStatementResult dtos
     * @return \Zend\Db\ResultSet\HydratingResultSet
     */
    public function selectSourceRecordsAsInsertStatements();

    /**
     * Empty the destination table
     */
    public function truncateDestinationTable();
}
<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 5/9/13 4:07 PM
 */

namespace Console\Etl\Service;


use Console\Etl\Service\Dao\SelectAndInsertInterface;
use Console\Etl\Service\Dto\ImportFromLegacyResult;
use Console\Etl\Service\Dto\InsertStatementResult;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class SelectAndInsert implements ServiceLocatorAwareInterface {
    /** @var  SelectAndInsertInterface */
    protected $dao;
    /** @var  ServiceLocatorInterface */
    protected $serviceLocator;
    /** @var  ImportFromLegacyResult */
    protected $resultDto;

    /**
     * @return ImportFromLegacyResult
     */
    protected function getResultDto() {
        if(empty($this->resultDto)) {
            $this->resultDto = $this->serviceLocator->get('Console\Etl\Service\Dto\ImportFromLegacyResult');
        }
        return $this->resultDto;
    }


    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @return ImportFromLegacyResult
     */
    public function importFromLegacy() {
        $this->setExpectedResultCount();
        $this->setForeignKeyChecks(false);
        $this->truncateDestinationTable();
        $this->migrateRecords();
        $this->setForeignKeyChecks(true);
        $this->setActualResultCount();
        return $this->getResultDto();
    }

    /**
     * Set the expected result count on the result dto
     */
    protected function setExpectedResultCount()
    {
        $this->getResultDto()->setExpectedRecordCount($this->getDao()->getSourceRecordCount());
    }

    /**
     * Set the actual result count on the result dto
     */
    protected function setActualResultCount()
    {
        $this->getResultDto()->setActualRecordCount($this->getDao()->getDestinationRecordCount());
    }

    /**
     * Toggle foreign key checks
     * @param bool $on
     */
    protected function setForeignKeyChecks($on = false)
    {
        $this->getDao()->foreignKeyChecks($on);
    }

    /**
     * Copy records from source to destination
     */
    protected function migrateRecords()
    {
        $sourceRecords = $this->getDao()->selectSourceRecordsAsInsertStatements();
        /** @var $insertResult InsertStatementResult */
        foreach($sourceRecords as $insertResult) {
            $this->getDao()->executeInsertStatement($insertResult->getSql());
        }
    }

    /**
     * Empty destination table of current records
     */
    protected function truncateDestinationTable()
    {
        $this->getDao()->truncateDestinationTable();
    }

    /**
     * @return SelectAndInsertInterface
     */
    protected function getDao() {
        return $this->dao;
    }
}
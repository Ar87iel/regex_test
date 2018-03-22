<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/08/13 5:05 PM
 */
namespace Console\Etl\Service\CompaniesImport\Dao;

use Console\Etl\SqlConnector\Legacy\ReaderWriterDiInterface as LegacyDiInterface;
use Console\Etl\SqlConnector\Delegator\ReaderWriterDiInterface as DelegatorDiInterface;
use Console\Etl\Service\CompaniesImport\Dao\Dto\CompanyResult;
use Console\Etl\Service\CompaniesImport\Dao\Dto\DelegatorCompany;
use EMRCore\SqlConnector\SqlConnectorAbstract;

class Dao implements LegacyDiInterface, DelegatorDiInterface
{

    /** @var  SqlConnectorAbstract */
    protected $legacyReaderWriter;
    /** @var  SqlConnectorAbstract */
    protected $delegatorReaderWriter;

    /**
     * @param DelegatorCompany $company
     * @return \EMRCore\SqlConnector\Dto\ResultSet
     */
    public function createCompany(DelegatorCompany $company)
    {
        $result = $this->delegatorReaderWriter->query("
            insert into Company
              (CompanyId, Name, OnlineStatus, ClusterId, CreatedAt, LastModified)
            values (
                :companyId, :companyName, :onlineStatus, :clusterId, :created, :lastModified
            )",
            array(
                'companyId' => $company->getCompanyId(),
                'companyName' => $company->getName(),
                'onlineStatus' => $company->getOnlineStatus(),
                'clusterId' => $company->getClusterId(),
                'created' => $company->getCreated(),
                'lastModified' => $company->getLastModified()
            ));
        return $result;
    }


    /**
     * @param SqlConnectorAbstract $database
     */
    public function setLegacyReaderWriter(SqlConnectorAbstract $database)
    {
        $this->legacyReaderWriter = $database;
    }

    /**
     * @param SqlConnectorAbstract $database
     */
    public function setDelegatorReaderWriter(SqlConnectorAbstract $database)
    {
        $this->delegatorReaderWriter = $database;
    }

    /**
     * @return \Zend\Db\ResultSet\HydratingResultSet
     */
    public function getCompanies()
    {
        $result = $this->legacyReaderWriter->query("
            select
                Cmp_CompanyId as companyId,
                Cmp_Name as name
            from Company",
            array(), new CompanyResult());
        return $result->getResults();
    }

    /**
     * Remove all companies from the delegator company table.
     */
    public function truncateDelegatorCompanies() {
        $this->delegatorReaderWriter->execute("truncate table Company");
    }

    public function turnOffKeyConstraints()
    {
        $this->delegatorReaderWriter->execute("SET foreign_key_checks = 0;");
    }

    public function turnOnKeyConstraints()
    {
        $this->delegatorReaderWriter->execute("SET foreign_key_checks = 1;");
    }
}
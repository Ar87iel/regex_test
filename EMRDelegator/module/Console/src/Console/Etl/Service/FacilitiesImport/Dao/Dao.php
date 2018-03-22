<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/08/13 5:05 PM
 */
namespace Console\Etl\Service\FacilitiesImport\Dao;

use Console\Etl\Service\FacilitiesImport\Dao\Dto\DelegatorFacility;
use Console\Etl\Service\FacilitiesImport\Dao\Dto\FacilityResult;
use Console\Etl\SqlConnector\Legacy\ReaderWriterDiInterface as LegacyDiInterface;
use Console\Etl\SqlConnector\Delegator\ReaderWriterDiInterface as DelegatorDiInterface;
use EMRCore\SqlConnector\SqlConnectorAbstract;

class Dao implements LegacyDiInterface, DelegatorDiInterface
{

    /** @var  SqlConnectorAbstract */
    protected $legacyReaderWriter;
    /** @var  SqlConnectorAbstract */
    protected $delegatorReaderWriter;

    /**
     * @param DelegatorFacility $facility
     * @return \EMRCore\SqlConnector\Dto\ResultSet
     */
    public function createFacility(DelegatorFacility $facility)
    {
        $result = $this->delegatorReaderWriter->query("
            insert into Facility
              (FacilityId, Name, CompanyId, CreatedAt, LastModified)
            values (
                :facilityId, :facilityName, :companyId, :created, :lastModified
            )",
            array(
                'facilityId' => $facility->getFacilityId(),
                'facilityName' => $facility->getName(),
                'companyId' => $facility->getCompanyId(),
                'created' => $facility->getCreated(),
                'lastModified' => $facility->getLastModified()
            ));
        return $result;
    }

    /**
     * @return \Zend\Db\ResultSet\HydratingResultSet
     */
    public function getFacilities()
    {
        $result = $this->legacyReaderWriter->query("
            select
                Fclty_FacilityID as facilityId,
                Fclty_Name as name,
                Fclty_CompanyID as companyId
            from Facility",
            array(), new FacilityResult());
        return $result->getResults();
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
     * Remove all companies from the delegator company table.
     */
    public function truncateDelegatorFacilities()
    {
        $this->delegatorReaderWriter->execute("truncate table Facility");
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
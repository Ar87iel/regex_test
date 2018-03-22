<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/08/13 5:05 PM
 */
namespace Console\Etl\Service\UserHasFacilityImport\Dao;

use Console\Etl\Service\UserHasFacilityImport\Dao\Dto\FacilityHasUser;
use Console\Etl\SqlConnector\Legacy\ReaderWriterDiInterface as LegacyDiInterface;
use Console\Etl\SqlConnector\Delegator\ReaderWriterDiInterface as DelegatorDiInterface;
use Console\Etl\Service\UserHasFacilityImport\Dao\Dto\DelegatorUserHasFacility;
use EMRCore\SqlConnector\SqlConnectorAbstract;
use EMRDelegator\Model\Facility;

class Dao implements LegacyDiInterface, DelegatorDiInterface
{

    /** @var  SqlConnectorAbstract */
    protected $legacyReaderWriter;
    /** @var  SqlConnectorAbstract */
    protected $delegatorReaderWriter;

    /**
     * @param DelegatorUserHasFacility $uhf
     * @return \EMRCore\SqlConnector\Dto\ResultSet
     */
    public function createUserHasFacility(DelegatorUserHasFacility $uhf)
    {
        $result = $this->delegatorReaderWriter->query("
                insert into UserHasFacility
                  (IdentityId, IsDefault, FacilityId, CreatedAt, LastModified)
                values (
                    :identityId, :isDefault, :facilityId, :created, :lastModified
                )",
            array(
                'identityId' => $uhf->getIdentityId(),
                'isDefault' => $uhf->getIsDefault(),
                'facilityId' => $uhf->getFacilityId(),
                'created' => $uhf->getCreated(),
                'lastModified' => $uhf->getLastModified()
            ));
        return $result;
    }

    public function facilityExists($facilityId)
    {
        $result = $this->delegatorReaderWriter->query("SELECT FacilityId FROM Facility WHERE FacilityId = :facilityId",
            array( 'facilityId' => $facilityId), new Facility());
        return $result->getCount() > 0;
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
    public function getUserHasFacility()
    {
        $result = $this->legacyReaderWriter->query("
            select
                Fcltyhusr_FacilityId as facilityId,
                UserID as userId,
                DefaultClinic as defaultClinic
            from FacilityHasUser",
            array(), new FacilityHasUser());
        return $result->getResults();
    }

    /**
     * Remove all companies from the delegator company table.
     */
    public function truncateDelegatorUserHasFacility() {
        $this->delegatorReaderWriter->execute("truncate table UserHasFacility");
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
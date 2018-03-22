<?php
/**
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/08/13 5:05 PM
 */
namespace Console\Etl\Controller;

use Console\Etl\Service\UserHasFacilityImport\UserHasFacilityImport;
use EMRCore\Zend\Module\Console\Controller\ActionControllerAbstract;

class UserHasFacilityImportController extends ActionControllerAbstract
{

    public function executeAction() {
        /** @var UserHasFacilityImport $service */
        $service = $this->serviceLocator->get('Console\Etl\Service\UserHasFacilityImport\UserHasFacilityImport');

        $service->setVerbose(true);
        $result = $service->importFromLegacy();

        $msg = "Success!!! imported {$result->getWrittenCount()} out of {$result->getRecordCount()} facilities.\n";
        $msg.= $result->getFacilityMissing() . " Facility IDs did not exist in Delegator so a relationship was not inserted.\n";

        return $msg;
    }

}
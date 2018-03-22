<?php
/**
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/08/13 5:05 PM
 */
namespace Console\Etl\Controller;

use Console\Etl\Service\FacilitiesImport\FacilitiesImport;
use EMRCore\Zend\Module\Console\Controller\ActionControllerAbstract;

class FacilitiesImportController extends ActionControllerAbstract
{

    public function executeAction() {
        /** @var FacilitiesImport $service */
        $service = $this->serviceLocator->get('Console\Etl\Service\FacilitiesImport\FacilitiesImport');

        $service->setVerbose(true);
        $result = $service->importFromLegacy();

        return "Success!!! imported {$result->getFacilityCount()} out of {$result->getRecordCount()} facilities.\n";
    }

}
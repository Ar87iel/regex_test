<?php
/**
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/08/13 5:05 PM
 */
namespace Console\Etl\Controller;

use Console\Etl\Service\CompaniesImport\CompaniesImport;
use EMRCore\Zend\Module\Console\Controller\ActionControllerAbstract;

class CompaniesImportController extends ActionControllerAbstract
{

    public function executeAction()
    {
        /** @var CompaniesImport $service */
        $service = $this->serviceLocator->get('Console\Etl\Service\CompaniesImport\CompaniesImport');

        $service->setVerbose(true);
        $result = $service->importFromLegacy();

        return "Success!!! Imported {$result->getCompanyCount()} companies out of {$result->getRecordCount()} legacy company records.\n";
    }

}
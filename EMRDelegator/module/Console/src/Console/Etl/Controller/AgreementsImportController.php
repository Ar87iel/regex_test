<?php
/**
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 5/08/13 5:05 PM
 */
namespace Console\Etl\Controller;

use Console\Etl\Service\AgreementsImport\Agreements;
use EMRCore\Zend\Module\Console\Controller\ActionControllerAbstract;
use Console\Etl\Service\AgreementsImport\Agreements\Agreements as AgreementService;
use Console\Etl\Service\AgreementsImport\AgreementType\AgreementType as AgreementTypeService;
use Console\Etl\Service\AgreementsImport\UserHasAgreement\UserHasAgreement as UserHasAgreementService;

class AgreementsImportController extends ActionControllerAbstract
{
    protected function importAgreementTypes() {
        /** @var AgreementTypeService $service */
        $service = $this->serviceLocator->get('Console\Etl\Service\AgreementsImport\AgreementType\AgreementType');

        $result = $service->importFromLegacy();
        return "Imported {$result->getExpectedRecordCount()} records from Legacy and Created {$result->getActualRecordCount()} agreement types in Delegator\n";
    }

    protected function importAgreements() {
        /** @var AgreementService $service */
        $service = $this->serviceLocator->get('Console\Etl\Service\AgreementsImport\Agreements\Agreements');

        $result = $service->importFromLegacy();
        return "Imported {$result->getExpectedRecordCount()} records from Legacy and Created {$result->getActualRecordCount()} agreements in Delegator\n";
    }

    protected function importUserHasAgreements() {
        /** @var UserHasAgreementService $service */
        $service = $this->serviceLocator->get('Console\Etl\Service\AgreementsImport\UserHasAgreement\UserHasAgreement');

        $result = $service->importFromLegacy();
        return "Imported {$result->getExpectedRecordCount()} records from Legacy and Created {$result->getActualRecordCount()} user agreement associations in Delegator\n";

    }

    public function executeAction() {
        echo "Importing AgreementTypes ...\n";
        echo $this->importAgreementTypes();
        echo "Importing Agreements ...\n";
        echo $this->importAgreements();
        echo "Importing UserHasAgreement ...\n";
        echo $this->importUserHasAgreements();
    }

}
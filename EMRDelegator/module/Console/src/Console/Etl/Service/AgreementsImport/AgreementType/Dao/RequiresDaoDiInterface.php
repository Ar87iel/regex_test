<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 5/8/13 5:23 PM
 */

namespace Console\Etl\Service\AgreementsImport\AgreementType\Dao;
use EMRCore\Zend\ServiceManager\InterfaceInjection\InjectViaInitializerInterface;
use Console\Etl\Service\AgreementsImport\AgreementType\Dao\AgreementType as AgreementTypeDao;

interface RequiresDaoDiInterface extends InjectViaInitializerInterface {
    /**
     * @param AgreementTypeDao $dao
     * @return mixed
     */
    public function setAgreementTypeDao(AgreementTypeDao $dao);
}
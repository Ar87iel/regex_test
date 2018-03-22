<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 5/8/13 5:23 PM
 */

namespace Console\Etl\Service\AgreementsImport\UserHasAgreement\Dao;
use EMRCore\Zend\ServiceManager\InterfaceInjection\InjectViaInitializerInterface;
use Console\Etl\Service\AgreementsImport\UserHasAgreement\Dao\UserHasAgreement as UserHasAgreementDao;

interface RequiresDaoDiInterface extends InjectViaInitializerInterface {
    /**
     * @param UserHasAgreementDao $dao
     * @return mixed
     */
    public function setUserHasAgreementDao(UserHasAgreementDao $dao);
}
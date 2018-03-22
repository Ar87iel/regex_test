<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 5/8/13 5:23 PM
 */

namespace Console\Etl\Service\AgreementsImport\Agreement\Dao;
use EMRCore\Zend\ServiceManager\InterfaceInjection\InjectViaInitializerInterface;
use Console\Etl\Service\AgreementsImport\Agreements\Dao\Agreements as AgreementsDao;

interface RequiresDaoDiInterface extends InjectViaInitializerInterface {
    /**
     * @param AgreementsDao $dao
     * @return mixed
     */
    public function setAgreementsDao(AgreementsDao $dao);
}
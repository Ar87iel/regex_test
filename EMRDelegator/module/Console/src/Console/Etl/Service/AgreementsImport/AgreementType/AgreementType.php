<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 5/16/13 11:51 AM
 */

namespace Console\Etl\Service\AgreementsImport\AgreementType;


use Console\Etl\Service\AgreementsImport\AgreementType\Dao\RequiresDaoDiInterface;
use Console\Etl\Service\SelectAndInsert;
use Console\Etl\Service\AgreementsImport\AgreementType\Dao\AgreementType as AgreementTypeDao;

class AgreementType extends SelectAndInsert implements RequiresDaoDiInterface {
    /**
     * @param AgreementTypeDao $dao
     * @return mixed
     */
    public function setAgreementTypeDao(AgreementTypeDao $dao)
    {
        $this->dao = $dao;
    }

}
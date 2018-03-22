<?php

namespace Console\Etl\Service\AgreementsImport\UserHasAgreement;


use Console\Etl\Service\AgreementsImport\UserHasAgreement\Dao\RequiresDaoDiInterface;
use Console\Etl\Service\AgreementsImport\UserHasAgreement\Dao\UserHasAgreement as UserHasAgreementDao;
use Console\Etl\Service\SelectAndInsert;

class UserHasAgreement extends SelectAndInsert implements RequiresDaoDiInterface {

    /**
     * @param UserHasAgreementDao $dao
     * @return mixed
     */
    public function setUserHasAgreementDao(UserHasAgreementDao $dao)
    {
        $this->dao = $dao;
    }
}
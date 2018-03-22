<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 5/16/13 11:51 AM
 */

namespace Console\Etl\Service\AgreementsImport\Agreements;


use Console\Etl\Service\AgreementsImport\Agreement\Dao\RequiresDaoDiInterface;
use Console\Etl\Service\SelectAndInsert;
use Console\Etl\Service\AgreementsImport\Agreements\Dao\Agreements as AgreementsDao;

class Agreements extends SelectAndInsert implements RequiresDaoDiInterface {
    /**
     * @param AgreementsDao $dao
     * @return mixed
     */
    public function setAgreementsDao(AgreementsDao $dao)
    {
        $this->dao = $dao;
    }

}
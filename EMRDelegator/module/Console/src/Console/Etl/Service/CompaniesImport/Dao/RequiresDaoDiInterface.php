<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 4/29/13 5:05 PM
 */

namespace Console\Etl\Service\CompaniesImport\Dao;

use EMRCore\Zend\ServiceManager\InterfaceInjection\InjectViaInitializerInterface;

interface RequiresDaoDiInterface extends InjectViaInitializerInterface {
    /**
     * @param Dao $dao
     * @return mixed
     */
    public function setCompaniesImportDao(Dao $dao);
}
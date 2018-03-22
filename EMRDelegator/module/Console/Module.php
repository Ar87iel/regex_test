<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 4/29/13 8:31 AM
 */
namespace Console;

use EMRCore\Zend\Module\Console\ModuleEventAbstract;
use Zend\Config\Config as ZendConfig;
use Zend\Console\Adapter\AdapterInterface as ConsoleAdapter;

class Module extends ModuleEventAbstract
{
    /**
     * Each slice module should override this method to determine if controllers should expect
     * companyId being provided. Slices like SSO/Delegator don't need companyId specified as they have their own
     * schema that is not related to tenants.
     * @return bool
     */
    protected function checkForCompanyIdParam()
    {
        return false;
    }

    /**
     * Get the module configuration.
     * @return array
     */
    public function getConfig()
    {
        $coreModuleConfig = new ZendConfig( parent::getConfig() );
        $sliceModuleConfig = new ZendConfig( include __DIR__ . '/config/module.config.php' );
        $etlConfig = new ZendConfig(include __DIR__ . '/config/etl.config.php');

        $mergedConfig = $coreModuleConfig->merge( $sliceModuleConfig );
        $mergedConfig->merge($etlConfig);
        return $mergedConfig->toArray();
    }

    /**
     * Return usage information for all routes in the console module.
     * @param ConsoleAdapter $console
     * @return array
     */
    public function getConsoleUsage(ConsoleAdapter $console){
        return array_merge(parent::getConsoleUsage($console), array(
            'etl companiesimport' => 'Import Company records from the multi-tenant schema.',
            'etl facilitiesimport' => 'Import Facility records from the multi-tenant schema.',
            'etl userhasfacilityimport' => 'Import UserHasFacility records from the multi-tenant schema.',
            'etl agreements' => 'Import Agreement records from the multi-tenant schema.',
        ));
    }
}
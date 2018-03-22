<?php
/**
 * Service module.
 *
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2013 WebPT, INC
 */
namespace Service;

use EMRCore\Zend\Module\Service\ModuleEventAbstract;
use Zend\Config\Config as ZendConfig;

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
     * Get the module config.
     * @return array
     */
    public function getConfig()
    {
        $coreModuleConfig = new ZendConfig( parent::getConfig() );
        $sliceModuleConfig = new ZendConfig( include __DIR__ . '/config/module.config.php' );

        $mergedConfig = $coreModuleConfig->merge( $sliceModuleConfig );
        return $mergedConfig->toArray();
    }
}
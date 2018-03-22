<?php
namespace Console\Controller;

use Console\Database\Controller\ManagerController;
use Zend\Console\Adapter\AdapterInterface;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class DatabaseManagerController extends ManagerController
{
    /**
     * Release prefix must be a string that is unique to a project/repo.
     * For example, EMRAuth, EMRMaster, etc.
     *
     * @return string
     */
    protected function getReleasePrefix()
    {
        return 'EMRDelegator';
    }
}

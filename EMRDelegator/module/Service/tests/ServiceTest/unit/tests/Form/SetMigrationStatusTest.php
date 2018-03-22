<?php
/**
 * @category WebPT 
 * @package EMRAdmin
 * @author: kevinkucera
 * 5/31/13 11:41 AM
 */

use EMRCore\Service\Company\Migration;
use EMRDelegator\Model\Company;
use Service\Controller\Form\Company\SetMigrationStatus;

class SetMigrationStatusTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SetMigrationStatus
     */
    private $form;

    public function setUp()
    {
        $this->form = new SetMigrationStatus();
    }

    public function testValidData()
    {
        $this->form->setData(array('companyId' => 44, 'migrationStatus' => Migration::STATUS_READY));
        $result = $this->form->isValid();
        $this->assertTrue($result);
    }

    public function testCompanyIdRequired()
    {
        $this->form->setData(array( 'migrationStatus' => Migration::STATUS_EXPORTING_SCHEMA ));
        $result = $this->form->isValid();
        $this->assertFalse($result);
    }

    public function testStatusRequired()
    {
        $this->form->setData(array('companyId' => 1));
        $result = $this->form->isValid();
        $this->assertFalse($result);
    }

    public function testCompanyIdInvalid()
    {
        $this->form->setData(array('companyId' => 'asdf', 'migrationStatus' => Migration::STATUS_EXTRACTING_TENANT));
        $result = $this->form->isValid();
        $this->assertFalse($result);
    }

    public function testEmptyStatusInvalid()
    {
        $this->form->setData(array('companyId' => 21, 'migrationStatus' => ''));
        $result = $this->form->isValid();
        $this->assertFalse($result);
    }

    public function testStatusInvalid()
    {
        $this->form->setData(array('companyId' => 21, 'migrationStatus' => 'stuff'));
        $result = $this->form->isValid();
        $this->assertFalse($result);
    }

}
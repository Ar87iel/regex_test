<?php
/**
 * @category WebPT 
 * @package EMRAdmin
 * @author: kevinkucera
 * 5/31/13 11:41 AM
 */

use EMRCore\Service\Company\Migration;
use EMRDelegator\Model\Company;
use Service\Controller\Form\UserHasFacility\GetByToken;

class GetByTokenTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var GetByToken
     */
    private $form;

    public function setUp()
    {
        $this->form = new GetByToken();
    }

    public function testValidData()
    {
        $this->form->setData(array('wpt_sso_token' => '1234567890abcdefghijklmnopqrstuv'));
        $result = $this->form->isValid();
        $this->assertTrue($result);
    }

    public function testWptSsoTokenRequired()
    {
        $this->form->setData(array( 'junk' => 'stuff' ));
        $result = $this->form->isValid();
        $this->assertFalse($result);
    }

    public function testTokenTooShort()
    {
        $this->form->setData(array('wpt_sso_token' => '1234sdf'));
        $result = $this->form->isValid();
        $this->assertFalse($result);
    }

    public function testTokenTooLong()
    {
        $this->form->setData(array('wpt_sso_token' => '1234567890abcdefghijklmnopqrstuvw'));
        $result = $this->form->isValid();
        $this->assertFalse($result);
    }

}
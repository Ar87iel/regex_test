<?php
namespace EMRAdminTest\unit\tests\Zend\Form;

use EMRAdmin\Zend\Form\Form;
use PHPUnit_Framework_TestCase;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

class FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Form
     */
    private $form;

    public function setUp()
    {
        // This form is used to access the input-building helper functions.
        $this->form = new Form;
    }

    /**
     * Creates a new form with input filters passed in as parameters.
     * @return Form
     */
    private function getForm()
    {
        // Create a new form.
        $form = new Form;

        // Create a new input filter.
        $inputFilter = new InputFilter;

        /** @var $input Input */
        foreach (func_get_args() as $input) {

            // Register the input with the form.
            $form->add(array(
                'name' => $input->getName(),
            ));

            // Add the input to the filter.
            $inputFilter->add($input);
        }

        // Add the filter.
        $form->setInputFilter($inputFilter);

        return $form;
    }

    public function testValidRequiredString()
    {
        $inputName = 'asdf';
        $minLength = 1;
        $maxLength = 10;

        $inputValue = 'qwer';

        $input = $this->form->getRequiredString($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredStringDueToNotSet()
    {
        $inputName = 'asdf';
        $minLength = 1;
        $maxLength = 10;

        $input = $this->form->getRequiredString($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array());

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredStringDueToOverLength()
    {
        $inputName = 'asdf';
        $minLength = 1;
        $maxLength = 10;

        $inputValue = str_repeat('a', $maxLength + 1);

        $input = $this->form->getRequiredString($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredStringDueToUnderLength()
    {
        $inputName = 'asdf';
        $minLength = 1;
        $maxLength = 10;

        $inputValue = str_repeat('a', $minLength - 1);

        $input = $this->form->getRequiredString($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidOptionalString()
    {
        $inputName = 'asdf';
        $minLength = 1;
        $maxLength = 10;

        $inputValue = 'qwer';

        $input = $this->form->getOptionalString($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidOptionalStringDueToNotSet()
    {
        $inputName = 'asdf';
        $minLength = 1;
        $maxLength = 10;

        $input = $this->form->getOptionalString($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array());

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidOptionalStringDueToOverLength()
    {
        $inputName = 'asdf';
        $minLength = 1;
        $maxLength = 10;

        $inputValue = str_repeat('a', $maxLength + 1);

        $input = $this->form->getOptionalString($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidOptionalStringDueToEmptyAndNotRequired()
    {
        $inputName = 'asdf';
        $minLength = 1;
        $maxLength = 10;

        $inputValue = '';

        $input = $this->form->getOptionalString($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidRequiredInArray()
    {
        $inputName = 'asdf';
        $haystack = array('qwer');

        $inputValue = 'qwer';

        $input = $this->form->getRequiredInArray($inputName, $haystack);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredInArrayDueToNotSet()
    {
        $inputName = 'asdf';
        $haystack = array('qwer');

        $input = $this->form->getRequiredInArray($inputName, $haystack);

        $form = $this->getForm($input);
        $form->setData(array());

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredInArrayDueToNotInArray()
    {
        $inputName = 'asdf';
        $haystack = array('qwer');

        $inputValue = 'zxcv';

        $input = $this->form->getRequiredInArray($inputName, $haystack);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidOptionalDigits()
    {
        $inputName = 'asdf';
        $minLength = 2;
        $maxLength = 2;

        // Input from the user will always be a string.
        $inputValue = '12';

        $input = $this->form->getOptionalDigits($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidOptionalDigitsDueToNotSet()
    {
        $inputName = 'asdf';
        $minLength = 2;
        $maxLength = 2;

        $input = $this->form->getOptionalDigits($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array());

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidOptionalDigitsDueToOverLength()
    {
        $inputName = 'asdf';
        $minLength = 2;
        $maxLength = 2;

        // Input from the user will always be a string.
        $inputValue = '123';

        $input = $this->form->getOptionalDigits($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidOptionalDigitsDueToUnderLength()
    {
        $inputName = 'asdf';
        $minLength = 2;
        $maxLength = 2;

        // Input from the user will always be a string.
        $inputValue = '1';

        $input = $this->form->getOptionalDigits($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidOptionalEmail()
    {
        if (phpversion() >= '5.6') {
            self::markTestSkipped('Use of iconv.internal_encoding is deprecated');
        }
        
        $inputName = 'asdf';
        $minLength = 1;
        $maxLength = 10;

        $inputValue = 'as@df.com';

        $input = $this->form->getOptionalEmail($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidOptionalEmailDueToBadFormat()
    {
        $inputName = 'asdf';
        $minLength = 1;
        $maxLength = 10;

        $inputValue = '@df.com';

        $input = $this->form->getOptionalEmail($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidOptionalEmailDueToNotSet()
    {
        $inputName = 'asdf';
        $minLength = 1;
        $maxLength = 10;

        $input = $this->form->getOptionalEmail($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array());

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidOptionalEmailDueToOverLength()
    {
        if (phpversion() >= '5.6') {
            self::markTestSkipped('Use of iconv.internal_encoding is deprecated');
        }
        
        $inputName = 'asdf';
        $minLength = 1;
        $maxLength = 10;

        $inputValue = 'asdfqwer@zxcv.com';

        $input = $this->form->getOptionalEmail($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidOptionalEmailDueToUnderLength()
    {
        if (phpversion() >= '5.6') {
            self::markTestSkipped('Use of iconv.internal_encoding is deprecated');
        }
        
        $inputName = 'asdf';
        $minLength = 10;
        $maxLength = 20;

        $inputValue = 'as@df.com';

        $input = $this->form->getOptionalEmail($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidRequiredId()
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('intl extension is not loaded');
        }
        
        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = '1';

        $input = $this->form->getRequiredId($inputName);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredIdDueToNotSet()
    {
        $inputName = 'asdf';

        $input = $this->form->getRequiredId($inputName);

        $form = $this->getForm($input);
        $form->setData(array());

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredIdDueNotAnId()
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('intl extension is not loaded');
        }
        
        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = 'asdf';

        $input = $this->form->getRequiredId($inputName);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredIdDueToNegativeNumber()
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('intl extension is not loaded');
        }
        
        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = '-1';

        $input = $this->form->getRequiredId($inputName);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidOptionalId()
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('intl extension is not loaded');
        }

        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = '1';

        $input = $this->form->getOptionalId($inputName);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidOptionalIdDueToNotRequired()
    {
        $inputName = 'asdf';

        $input = $this->form->getOptionalId($inputName);

        $form = $this->getForm($input);
        $form->setData(array());

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidOptionalIdDueNotAnId()
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('intl extension is not loaded');
        }

        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = 'asdf';

        $input = $this->form->getOptionalId($inputName);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidOptionalIdDueToNegativeNumber()
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('intl extension is not loaded');
        }

        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = '-1';

        $input = $this->form->getOptionalId($inputName);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidOptionalIdDueToZero()
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('intl extension is not loaded');
        }

        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = '0';

        $input = $this->form->getOptionalId($inputName);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidOptionalIdWithEmptyStringDueToOptional()
    {
        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = '';

        $input = $this->form->getOptionalId($inputName);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidRequiredIpAddress()
    {
        $inputName = 'asdf';

        $inputValue = '1.1.1.1';

        $input = $this->form->getRequiredIpAddress($inputName);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredIpAddressDueToRequired()
    {
        $inputName = 'asdf';

        $input = $this->form->getRequiredIpAddress($inputName);

        $form = $this->getForm($input);
        $form->setData(array());

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredIpAddressDueToInvalidIpAddress()
    {
        $inputName = 'asdf';

        $inputValue = 'asdf';

        $input = $this->form->getRequiredIpAddress($inputName);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidOptionalGreaterThan()
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('intl extension is not loaded');
        }

        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = '1';

        $input = $this->form->getOptionalGreaterThan($inputName, 0);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidOptionalGreaterThanDueToNotRequired()
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('intl extension is not loaded');
        }

        $inputName = 'asdf';

        $input = $this->form->getOptionalGreaterThan($inputName, 0);

        $form = $this->getForm($input);
        $form->setData(array());

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidOptionalGreaterThanDueToLessThan()
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('intl extension is not loaded');
        }

        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = '0';

        $input = $this->form->getOptionalGreaterThan($inputName, 1);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidRequiredGreaterThan()
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('intl extension is not loaded');
        }

        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = '1';

        $input = $this->form->getRequiredGreaterThan($inputName, 0);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredGreaterThanDueToNotAnInteger()
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('intl extension is not loaded');
        }

        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = 'asdf';

        $input = $this->form->getRequiredGreaterThan($inputName, 0);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredGreaterThanDueToNotAnInteger2()
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('intl extension is not loaded');
        }

        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = '1asdf';

        $input = $this->form->getRequiredGreaterThan($inputName, 0);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredGreaterThanDueToRequired()
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('intl extension is not loaded');
        }

        $inputName = 'asdf';

        $input = $this->form->getRequiredGreaterThan($inputName, 1);

        $form = $this->getForm($input);
        $form->setData(array());

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredGreaterThanDueToLessThan()
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('intl extension is not loaded');
        }

        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = '0';

        $input = $this->form->getRequiredGreaterThan($inputName, 1);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidOptionalBoolean()
    {
        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = 'true';

        $input = $this->form->getOptionalBoolean($inputName);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidOptionalBooleanDueToNotRequired()
    {
        $inputName = 'asdf';

        $input = $this->form->getOptionalBoolean($inputName);

        $form = $this->getForm($input);
        $form->setData(array());

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidRequiredBoolean()
    {
        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = 'true';

        $input = $this->form->getRequiredBoolean($inputName);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidRequiredBooleanYes()
    {
        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = 'yes';

        $input = $this->form->getRequiredBoolean($inputName);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidRequiredBooleanOne()
    {
        $inputName = 'asdf';

        // Input from the user will always be a string.
        $inputValue = '1';

        $input = $this->form->getRequiredBoolean($inputName);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredBooleanDueToRequired()
    {
        $inputName = 'asdf';

        $input = $this->form->getRequiredBoolean($inputName);

        $form = $this->getForm($input);
        $form->setData(array());

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }
    
    public function testValidRequiredEmail()
    {
        if (phpversion() >= '5.6') {
            self::markTestSkipped('Use of iconv.internal_encoding is deprecated');
        }
        
        $inputName = 'asdf';
        $minLength = 1;
        $maxLength = 10;

        $inputValue = 'as@df.com';

        $input = $this->form->getRequiredEmail($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }
    
    public function testInvalidRequiredEmailDueToBadFormat()
    {
        $inputName = 'asdf';
        $minLength = 1;
        $maxLength = 10;

        $inputValue = '@df.com';

        $input = $this->form->getRequiredEmail($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredEmailDueToNotSet()
    {
        $inputName = 'asdf';
        $minLength = 1;
        $maxLength = 10;

        $input = $this->form->getRequiredEmail($inputName, $minLength, $maxLength);
 
        $form = $this->getForm($input);
        $form->setData(array());

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredEmailDueToOverLength()
    {
        if (phpversion() >= '5.6') {
            self::markTestSkipped('Use of iconv.internal_encoding is deprecated');
        }

        $inputName = 'asdf';
        $minLength = 1;
        $maxLength = 10;

        $inputValue = 'asdfqwer@zxcv.com';

        $input = $this->form->getRequiredEmail($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidRequiredEmailDueToUnderLength()
    {
        if (phpversion() >= '5.6') {
            self::markTestSkipped('Use of iconv.internal_encoding is deprecated');
        }

        $inputName = 'asdf';
        $minLength = 10;
        $maxLength = 20;

        $inputValue = 'as@df.com';

        $input = $this->form->getRequiredEmail($inputName, $minLength, $maxLength);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }
    
    public function testValidOptionalInArray()
    {
        $inputName = 'asdf';
        $haystack = array('qwer');

        $inputValue = 'qwer';

        $input = $this->form->getOptionalInArray($inputName, $haystack);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testValidOptionalInArrayDueToNotSet()
    {
        $inputName = 'asdf';
        $haystack = array('qwer');

        $input = $this->form->getOptionalInArray($inputName, $haystack);

        $form = $this->getForm($input);
        $form->setData(array());

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }

    public function testInvalidOptionalInArrayDueToNotInArray()
    {
        $inputName = 'asdf';
        $haystack = array('qwer');

        $inputValue = 'zxcv';

        $input = $this->form->getOptionalInArray($inputName, $haystack);

        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }
    
    public function testValidOptionalIsArray()
    {
        $inputName = 'asdf';
        $haystack = array(
            'qwer',
            'asdf',
            'zxcv'
            );

        $inputValue = array(
            'zxcv',
            'asdf',
            'qwer'
        );
        
        $input = $this->form->getOptionalIsArray($inputName, $haystack);
        
        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }
    
    public function testValidOptionalIsArrayEmpty()
    {
        $inputName = 'asdf';
        $haystack = array(
            'qwer',
            'asdf',
            'zxcv'
            );

        $inputValue = array();
        
        $input = $this->form->getOptionalIsArray($inputName, $haystack);
        
        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertTrue($isValid, print_r($form->getMessages($inputName), true));
    }
    
    public function testInValidOptionalIsArrayDueToNotArrayType()
    {
        $inputName = 'asdf';
        $haystack = array(
            'qwer',
            'asdf',
            'zxcv'
            );

        $inputValue = 'qwer';
        
        $input = $this->form->getOptionalIsArray($inputName, $haystack);
        
        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }
    
    public function testInValidOptionalIsArrayDueToInvalidDataInArray()
    {
        $inputName = 'asdf';
        $haystack = array(
            'qwer',
            'asdf',
            'zxcv'
            );

        $inputValue = array('reqw');
        
        $input = $this->form->getOptionalIsArray($inputName, $haystack);
        
        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }
    
    public function testInValidOptionalIsArrayDueToMixedValidAndInvalidDataInArray()
    {
        $inputName = 'asdf';
        $haystack = array(
            'qwer',
            'asdf',
            'zxcv'
            );

        $inputValue = array(
            'qwer',
            'fghj',
            'zxcv'
        );
        
        $input = $this->form->getOptionalIsArray($inputName, $haystack);
        
        $form = $this->getForm($input);
        $form->setData(array(
            $inputName => $inputValue,
        ));

        $isValid = $form->isValid();
        $this->assertFalse($isValid, print_r($form->getMessages($inputName), true));
    }
    
}
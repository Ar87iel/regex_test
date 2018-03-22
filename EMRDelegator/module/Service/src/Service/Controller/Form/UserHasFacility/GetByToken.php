<?php
/**
 * Defines a service form contract for sending getList requests to the
 * UserHasFacility endpoint.
 *
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2013 WebPT, INC
 */

namespace Service\Controller\Form\UserHasFacility;

use EMRCore\Zend\Form\Form;
use RuntimeException;
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class GetByToken extends Form
{
    public function __construct()
    {
        parent::__construct(__CLASS__);

        $this->add(array(
            'name' => 'wpt_sso_token',
        ));
    }

    public function setInputFilter(InputFilterInterface $filter)
    {
        throw new RuntimeException('This is not allowed.');
    }

    public function getInputFilter()
    {
        if ( $this->filter ) {
            return $this->filter;
        }

        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add($factory->createInput(array(
            'name' => 'wpt_sso_token',
            'required' => true,
            'filters'  => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 32,
                        'max' => 32,
                    ),
                ),
            ),
        )));

        $this->filter = $inputFilter;
        return $inputFilter;
    }
}
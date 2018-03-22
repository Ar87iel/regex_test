<?php
namespace Service\Controller\Form\Company;

use EMRCore\Zend\Form\Form;
use EMRDelegator\Model\Company;
use RuntimeException;
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\InArray;

class Create extends Form
{
    public function __construct()
    {
        parent::__construct(__CLASS__);

        $this->add(array(
            'name' => 'name',
        ));

        $this->add(array(
            'name' => 'clusterId',
        ));

        $this->add(array(
            'name' => 'onlineStatus',
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
            'name' => 'name',
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
                        'min' => 1,
                        'max' => 128,
                    ),
                ),
            ),
        )));

        $inputFilter->add($factory->createInput(array(
            'name' => 'clusterId',
            'allow_empty' => true,
            'filters' => array(
                array(
                    'name' => 'Int',
                ),
            ),
            'validators' => array(
                array(
                    'name' => 'GreaterThan',
                    'options' => array(
                        'min' => 0,
                    ),
                ),
            ),
        )));

        $onlineStatusValues = array(
            Company::STATUS_ALL,
            Company::STATUS_NONE,
            Company::STATUS_SYSTEM,
            Company::STATUS_SUPERUSER,
        );

        $inputFilter->add($factory->createInput(array(
            'name' => 'onlineStatus',
            'allow_empty' => true,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
            'validators' => array(
                array(
                    'name' => 'InArray',
                    'options' => array(
                        'haystack' => $onlineStatusValues,
                        'message' => 'Value must be one of: ' . implode(', ', $onlineStatusValues),
                    ),
                ),
            ),
        )));

        $this->filter = $inputFilter;
        return $inputFilter;
    }
}
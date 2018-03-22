<?php
/**
 * Form definition and validation for UserHasFacility create
 *
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2013 WebPT, INC
 */
namespace Service\Controller\Form\UserHasFacility;

use EMRCore\Zend\Form\Form;
use EMRCore\Zend\Form\Validator\IntArray;
use RuntimeException;
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\InArray;

class Create extends Form
{
    /**
     * Define the fields that are expected as input
     */
    public function __construct()
    {
        parent::__construct(__CLASS__);

        $this->add(array(
            'name' => 'identityId',
        ));

        $this->add(array(
            'name' => 'facilityIds',
        ));

        $this->add(array(
            'name' => 'defaultFacilityId',
        ));
    }

    /**
     * @param InputFilterInterface $filter
     * @return void|\Zend\Form\FormInterface
     * @throws \RuntimeException
     */
    public function setInputFilter(InputFilterInterface $filter)
    {
        throw new RuntimeException('This is not allowed.');
    }

    /**
     * Define validation for input fields
     * @return null|InputFilter|InputFilterInterface
     */
    public function getInputFilter()
    {
        if ( $this->filter ) {
            return $this->filter;
        }

        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add($factory->createInput(array(
            'name' => 'identityId',
            'required' => true,
            'filters'  => array(
                array(
                    'name' => 'int',
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

        $inputFilter->add($factory->createInput(array(
            'name' => 'facilityIds',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Callback',
                    'options' => array(
                        'callback' => array(
                            $this,
                            'validateJsonIsArray'
                        ),
                        'message' => '[facilityIds] json must be a valid array'
                    ),
                ),
                array(
                    'name' => 'Callback',
                    'options' => array(
                        'callback' => array(
                            $this,
                            'validateJson'
                        ),
                        'message' => '[facilityIds] invalid json'
                    ),
                ),
                array(
                    'name' => 'NotEmpty',
                ),
            ),
        )));

        $inputFilter->add($factory->createInput(array(
            'name' => 'defaultFacilityId',
            'required' => true,
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

        $this->filter = $inputFilter;
        return $inputFilter;
    }
    
    public function validateJson($data)
    {
        json_decode($data);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    
    public function validateJsonIsArray($data)
    {
        $decodedData = json_decode($data);
        return is_array($decodedData);
    }
}
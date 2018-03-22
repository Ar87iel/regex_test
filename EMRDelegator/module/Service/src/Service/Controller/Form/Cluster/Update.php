<?php
namespace Service\Controller\Form\Cluster;

use EMRCore\Zend\Form\Form;
use EMRDelegator\Model\Cluster;
use RuntimeException;
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\InArray;
use Zend\Filter\Boolean;

class Update extends Form
{
    public function __construct()
    {
        parent::__construct(__CLASS__);

        $this->add(array(
            'name' => 'clusterName',
        ));

        $this->add(array(
            'name' => 'facilityMax',
        ));

        $this->add(array(
            'name' => 'acceptingNewCompanies',
        ));

        $this->add(array(
            'name' => 'onlineStatus',
        ));

        $this->add(array(
            'name' => 'comment',
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
            'name' => 'clusterName',
            'allow_empty' => true,
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
            'name' => 'facilityMax',
            'allow_empty' => true,
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
            'name' => 'acceptingNewCompanies',
            'allow_empty' => true,
            'filters' => array(
                array(
                    'name' => 'Boolean',
                    'options' => array(
                        'type' => Boolean::TYPE_INTEGER + Boolean::TYPE_ZERO_STRING + Boolean::TYPE_FALSE_STRING
                    )
                ),
            ),
        )));

        $onlineStatusValues = array(
            Cluster::STATUS_ALL,
            Cluster::STATUS_NONE,
            Cluster::STATUS_SYSTEM,
            Cluster::STATUS_SUPERUSER,
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

        $inputFilter->add($factory->createInput(array(
            'name' => 'comment',
            'allow_empty' => true,
        )));

        $this->filter = $inputFilter;
        return $inputFilter;
    }
}
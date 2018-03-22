<?php
namespace Service\Controller\Form\Company;

use EMRCore\Service\Company\Migration;
use EMRCore\Zend\Form\Form;
use EMRDelegator\Model\Company;
use RuntimeException;
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\InArray;
use EMRCore\Service\Company\Migration as MigrationState;

class Update extends Form
{
    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    public function __construct()
    {
        parent::__construct(__CLASS__);

        $this->add(array(
            'name' => 'name',
        ));

        $this->add(array(
            'name' => 'onlineStatus',
        ));

        $this->add(array(
            'name' => 'migrationStatus',
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
            'required' => false,
            'allow_blank' => true,
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

        $onlineStatusValues = array(
            Company::STATUS_ALL,
            Company::STATUS_NONE,
            Company::STATUS_SYSTEM,
            Company::STATUS_SUPERUSER,
        );

        $inputFilter->add($factory->createInput(array(
            'name' => 'onlineStatus',
            'required' => false,
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

        /** @var \EMRCore\Service\Company\Migration $migrationState */
        $migrationState = $this->getMigrationState();
        $migrationStatusValues = $migrationState->getValidStatusArray();

        $inputFilter->add($factory->createInput(array(
            'name' => 'migrationStatus',
            'required' => false,
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
                        'haystack' => $migrationStatusValues,
                        'message' => 'Value must be one of: ' . implode(', ', $migrationStatusValues),
                    ),
                ),
            ),
        )));

        $this->filter = $inputFilter;
        return $inputFilter;
    }

    /**
     * @return Migration
     */
    public function getMigrationState()
    {
        return new MigrationState();
    }
}
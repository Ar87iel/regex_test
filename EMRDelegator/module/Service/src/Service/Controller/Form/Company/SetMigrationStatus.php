<?php
namespace Service\Controller\Form\Company;

use EMRCore\Service\Company\Migration;
use EMRCore\Zend\Form\Form;
use EMRCore\Zend\ServiceManager\ServiceManager;
use EMRDelegator\Model\Company;
use RuntimeException;
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\InArray;
use EMRCore\Service\Company\Migration as MigrationState;

class SetMigrationStatus extends Form
{
    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    public function __construct()
    {
        parent::__construct(__CLASS__);

        $this->add(array(
            'name' => 'companyId',
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
            'name' => 'companyId',
            'required' => true,
            'allow_blank' => false,
            'filters'  => array(
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

        /** @var MigrationState $migrationState */
        $migrationState = $this->getMigrationState();
        $migrationStatusValues = $migrationState->getValidStatusArray();
        $inputFilter->add($factory->createInput(array(
            'name' => 'migrationStatus',
            'required' => true,
            'allow_empty' => false,
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
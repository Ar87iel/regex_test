<?php
namespace Service\Controller\Marshaller;

use EMRCore\Marshaller\MarshallerInterface;
use EMRDelegator\Model\Company as CompanyModel;
use Service\Controller\Marshaller\FacilityToFacilityIdArray;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CompanyToCompanyIdFacilityIdArray implements MarshallerInterface, ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * @param CompanyModel[] $companyModels
     * @return array[]
     */
    public function marshall($companyModels)
    {
        return $this->marshalCompanyIds($companyModels);
    }

    /**
     * @param CompanyModel[] $companyModels
     * @return array[]
     */
    protected function marshalCompanyIds($companyModels)
    {
        $companies = array();

        if (empty( $companyModels ))
        {
            return $companies;
        }

        foreach ($companyModels as $companyModel)
        {
            $companies[] = $this->marshalCompanyId($companyModel);
        }

        return $companies;
    }

    /**
     * @param CompanyModel $companyModel
     * @return mixed[]
     */
    private function marshalCompanyId(CompanyModel $companyModel)
    {
        $marshalled = array(
            'id' => $companyModel->getCompanyId(),
        );

        /** @var FacilityToFacilityIdArray $marshaller */
        $marshaller = $this->serviceLocator->get('Service\Controller\Marshaller\FacilityToFacilityIdArray');
        $facilities = $marshaller->marshall($companyModel->getFacilities());

        if ( ! empty( $facilities ))
        {
            $marshalled['facilities'] = $facilities;
        }

        return $marshalled;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
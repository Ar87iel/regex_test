<?php
namespace Service\Controller\Marshaller;

use EMRCore\Marshaller\MarshallerInterface;
use EMRDelegator\Model\Cluster as ClusterModel;
use EMRDelegator\Model\Company as CompanyModel;
use Service\Controller\Marshaller\CompanyToCompanyIdFacilityIdArray;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ClusterToClusterIdCompanyIdFacilityIdArray implements MarshallerInterface, ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * @param ClusterModel[] $clusterModels
     * @return array[]
     */
    public function marshall($clusterModels)
    {
        return $this->marshalClusterIds($clusterModels);
    }


    /**
     * @param ClusterModel[] $clusterModels
     * @return array[]
     */
    protected function marshalClusterIds(array $clusterModels)
    {
        $clusters = array();

        foreach ($clusterModels as $clusterModel)
        {
            $clusters[] = $this->marshalClusterId($clusterModel);
        }

        return $clusters;
    }

    /**
     * @param ClusterModel $clusterModel
     * @return mixed[]
     */
    private function marshalClusterId(ClusterModel $clusterModel)
    {
        $marshalled = array(
            'id' => $clusterModel->getClusterId(),
        );

        /** @var CompanyToCompanyIdFacilityIdArray $marshaller */
        $marshaller = $this->serviceLocator->get('Service\Controller\Marshaller\CompanyToCompanyIdFacilityIdArray');
        $companies = $marshaller->marshall($clusterModel->getCompanies());

        if ( ! empty( $companies ))
        {
            $marshalled['companies'] = $companies;
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
<?php
/**
 * Services to manage clusters.
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2012 WebPT, INC
 */
namespace Service\Controller;

use EMRCore\Zend\Grant\IpGrantDiInterface;
use EMRCore\Zend\Mvc\Controller\RestfulControllerAbstract;
use EMRCore\Zend\ServiceManager\Factory as ServiceManagerFactory;
use EMRDelegator\Cluster\Marshaller\ClusterModelToArray;
use EMRDelegator\Model\Cluster as ClusterModel;
use EMRDelegator\Model\Company as CompanyModel;
use EMRDelegator\Service\Cluster\Cluster;
use EMRDelegator\Service\Cluster\Exception\ClusterNotFound as ClusterNotFoundException;
use EMRCore\Zend\Module\Service\Response\Content;
use Service\Controller\Form\Cluster\Create;
use Service\Controller\Form\Cluster\Update;
use Service\Controller\Marshaller\ClusterToClusterIdCompanyIdArray;
use Service\Controller\Marshaller\ClusterToClusterIdCompanyIdFacilityIdArray;

class ClusterController extends RestfulControllerAbstract implements IpGrantDiInterface
{
    /**
     * Get a list of available clusters
     * @return \EMRCore\Zend\Module\Service\Response\ResponseInterface|void
     */
    public function getList()
    {
        /** @var $service \EMRDelegator\Service\Cluster\Cluster */
        $service = $this->getServiceLocator()->get('EMRDelegator\Service\Cluster\Cluster');
        $clusterModels = $service->getList();

        /** @var $marshaller ClusterModelToArray */
        $marshaller = $this->getServiceLocator()->get('EMRDelegator\Cluster\Marshaller\ClusterModelToArray');

        $clusterArrays = array();
        foreach ($clusterModels as $clusterModel) {
            $clusterArrays[] = $marshaller->marshall($clusterModel);
        }

        $contentResponse = $this->getContentResponse();
        $contentResponse->setContent(array('clusters' => $clusterArrays));
        return $contentResponse;
    }

    /**
     * @param int $id
     * @return Content
     * @throws ClusterNotFoundException
     */
    public function get($id)
    {
        return $this->getContentResponse(array(
            'cluster' => $this->getMarshalledClusterById($id),
        ));
    }

    /**
     * @param int $clusterId
     * @return ClusterModel
     */
    public function getClusterById($clusterId)
    {
        return $this->getClusterService()->load($clusterId);
    }

    /**
     * @param ClusterModel $cluster
     * @return mixed[]
     */
    public function getMarshalledCluster(ClusterModel $cluster)
    {
        /** @var $marshaller ClusterModelToArray */
        $marshaller = $this->getServiceLocator()->get('EMRDelegator\Cluster\Marshaller\ClusterModelToArray');

        return $marshaller->marshall($cluster);
    }

    /**
     * @param int $clusterId
     * @return mixed[]
     */
    public function getMarshalledClusterById($clusterId)
    {
        $cluster = $this->getClusterById($clusterId);
        return $this->getMarshalledCluster($cluster);
    }

    /**
     * @param mixed $rawData
     * @return Content
     */
    public function create($rawData)
    {
        $data = $this->validateCreateData($rawData);

        $clusterModel = new ClusterModel();
        $clusterModel->setName($data['clusterName']);
        $clusterModel->setMaxFacilityCount($data['facilityMax']);

        if ($data['comment'] !== null) {
            $clusterModel->setComment($data['comment']);
        }
        if ($data['acceptingNewCompanies'] !== null) {
            $clusterModel->setAcceptingNewCompanies($data['acceptingNewCompanies']);
        }

        /** @var $service \EMRDelegator\Service\Cluster\Cluster */
        $service = $this->getServiceLocator()->get('EMRDelegator\Service\Cluster\Cluster');
        $createdClusterModel = $service->create($clusterModel);

        return $this->getContentResponse(array(
            'cluster' => $this->getMarshalledCluster($createdClusterModel),
            'success' => true,
        ));
    }

    /**
     * @param $data
     * @return array
     * @throws \InvalidArgumentException
     */
    public function validateCreateData($data)
    {
        return $this->getFormHelper()->validateFormData(new Create(), $data);
    }

    /**
     * @param int $id
     * @param mixed $rawData
     * @return Content
     * @throws ClusterNotFoundException
     */
    public function update($id, $rawData)
    {
        $data = $this->validateUpdateData($rawData);

        /** @var $service \EMRDelegator\Service\Cluster\Cluster */
        $service = $this->getServiceLocator()->get('EMRDelegator\Service\Cluster\Cluster');
        $clusterModel = $service->load($id);

        if(empty($clusterModel)){
            throw new ClusterNotFoundException('Cluster with id: '.$id.' does not exist');
        }

        $clusterModel = $this->updatePrepareModel($clusterModel, $data);

        $updatedClusterModel = $service->update($clusterModel);

        return $this->getContentResponse(array(
            'cluster' => $this->getMarshalledCluster($updatedClusterModel),
            'success' => true,
        ));
    }

    /**
     * @param $data
     * @return array
     */
    public function validateUpdateData($data)
    {
        return $this->getFormHelper()->validateFormData(new Update(), $data);
    }

    /**
     * @param ClusterModel $clusterModel
     * @param $data
     * @return ClusterModel
     */
    public function updatePrepareModel(ClusterModel $clusterModel, $data)
    {
        if (!empty($data['clusterName'])) {
            $clusterModel->setName($data['clusterName']);
        }
        if (!empty($data['facilityMax'])) {
            $clusterModel->setMaxFacilityCount($data['facilityMax']);
        }
        if ($data['acceptingNewCompanies'] !== null) {
            $clusterModel->setAcceptingNewCompanies($data['acceptingNewCompanies']);
        }
        if (!empty($data['onlineStatus'])) {
            $clusterModel->setOnlineStatus($data['onlineStatus']);
        }
        if (isset($data['comment']) && $data['comment'] !== null) {
            $clusterModel->setComment($data['comment']);
        }

        return $clusterModel;
    }

    /**
     * @return Cluster
     */
    private function getClusterService()
    {
        return $this->getServiceLocator()->get('EMRDelegator\Service\Cluster\Cluster');
    }

    /**
     * @param int $id
     * @return Content
     */
    public function delete($id)
    {
        $this->getClusterService()->delete($id);
        return $this->getContentResponse(array('success' => true));
    }

    /**
     * @return Content
     */
    public function getClusterIdsWithCompanyIdsAndFacilityIdsAction()
    {
        $clusterModels = $this->getClusterService()->getListClusterCompanyFacility();

        /** @var ClusterToClusterIdCompanyIdFacilityIdArray $marshaller */
        $marshaller = $this->serviceLocator->get('Service\Controller\Marshaller\ClusterToClusterIdCompanyIdFacilityIdArray');

        return $this->getContentResponse(array(
            'clusters' => $marshaller->marshall($clusterModels),
        ));
    }

    /**
     * @return Content
     */
    public function getClusterIdsWithCompanyIdsAction()
    {
        $clusterModels = $this->getClusterService()->getListClusterCompany();

        /** @var ClusterToClusterIdCompanyIdArray $marshaller */
        $marshaller = $this->serviceLocator->get('Service\Controller\Marshaller\ClusterToClusterIdCompanyIdArray');

        return $this->getContentResponse(array(
            'clusters' => $marshaller->marshall($clusterModels),
        ));
    }
}

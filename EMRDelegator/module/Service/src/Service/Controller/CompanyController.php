<?php
namespace Service\Controller;

use EMRCore\Http\HeaderService;
use EMRCore\PrototypeFactory;
use EMRCore\Zend\Grant\IpGrantDiInterface;
use EMRCore\Zend\Module\Service\Response\Content;
use EMRCore\Zend\Mvc\Controller\RestfulControllerAbstract;
use EMRDelegator\Company\Marshaller\CompanyModelToArray;
use EMRDelegator\Service\Company\Company;
use EMRDelegator\Service\Company\CompanyDiInterface;
use EMRDelegator\Service\Company\Exception\CannotLoad;
use EMRDelegator\Service\Company\Marshaller\SearchCompanyResultsToArray;
use EMRDelegator\Model\Company as CompanyModel;
use EMRDelegator\Service\Cluster\Cluster as ClusterService;
use EMRDelegator\Service\Company\Company as CompanyService;
use InvalidArgumentException;
use Service\Controller\Form\Company\Create;
use Service\Controller\Form\Company\SetMigrationStatus;
use Service\Controller\Form\Company\Update;
use Service\Controller\Marshaller\ParametersToSearchCompanyRequest;
use Service\Controller\Marshaller\ParametersToSearchCompanyRequestDiInterface;
use Zend\Form\Element;
use Zend\Http\Request;

// sigh
set_time_limit(-1);

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class CompanyController extends RestfulControllerAbstract
    implements CompanyDiInterface, ParametersToSearchCompanyRequestDiInterface, IpGrantDiInterface
{
    /**
     * @var CompanyService
     */
    protected $companyService;

    /**
     * @var ParametersToSearchCompanyRequest
     */
    protected $parametersToSearchCompanyRequestMarshaller;

    /**
     * @return ClusterService
     */
    private function getClusterService()
    {
        return $this->serviceLocator->get('EMRDelegator\Service\Cluster\Cluster');
    }

    /**
     * \Zend\Mvc\Controller\AbstractRestfulController->onDispatch() routes to this method
     * when this endpoint receives a GET command with an id. Exceptions are caught and packaged
     * up into a JSON response.
     *
     * @param int $companyId
     * @return Content
     */
    public function get($companyId)
    {
        return $this->getContentResponse($this->getMarshalledCompanyById($companyId));
    }

    /**
     * @return CompanyModelToArray
     */
    protected function getCompanyModelToArrayMarshaller()
    {
        return $this->getServiceLocator()->get('EMRDelegator\Company\Marshaller\CompanyModelToArray');
    }

    /**
     * Get a list of available companies
     * @return Content
     */
    public function getList()
    {
        $companyModels = $this->companyService->getList();

        $marshaller = $this->getCompanyModelToArrayMarshaller();

        $companyArrays = array();
        foreach ($companyModels as $companyModel) {
            $companyArrays[] = $marshaller->marshall($companyModel);
        }

        return $this->getContentResponse(array('companies' => $companyArrays));
    }


    /**
     * @param CompanyModel $company
     * @return mixed[]
     */
    public function getMarshalledCompany(CompanyModel $company)
    {
        return array(
            'company' => $this->getCompanyModelToArrayMarshaller()->marshall($company)
        );
    }

    /**
     * @param int $companyId
     * @return mixed[]
     */
    public function getMarshalledCompanyById($companyId)
    {
        $company = $this->getCompany($companyId);
        return $this->getMarshalledCompany($company);
    }

    /**
     * @param int $companyId
     * @throws CannotLoad
     * @throws InvalidArgumentException
     * @return CompanyModel
     */
    public function getCompany($companyId)
    {
        if (1 > $companyId) {
            throw new InvalidArgumentException( "companyId [$companyId] is invalid." );
        }

        $companyModel = $this->companyService->load($companyId);

        return $companyModel;
    }

    /**
     * \Zend\Mvc\Controller\AbstractRestfulController->onDispatch() routes to this method
     * when this endpoint receives a DELETE command. Exceptions are caught and packaged
     * up into a JSON response.
     *
     * @param int $companyId
     * @return Content
     */
    public function delete($companyId)
    {
        $this->deleteCompany($companyId);

        return $this->getContentResponse(array(
            'success' => true,
        ));
    }

    /**
     * Sends a prepared company model to the company business service to be deleted.
     *
     * @param int $companyId
     * @return mixed
     */
    public function deleteCompany($companyId)
    {
        $companyModel = $this->prepareDeleteCompany($companyId);

        return $this->companyService->delete($companyModel);
    }

    /**
     * Transforms delete input into a company model.
     *
     * @param int $companyId
     * @throws CannotLoad
     * @throws InvalidArgumentException
     * @return CompanyModel
     */
    public function prepareDeleteCompany($companyId)
    {
        if (1 > $companyId) {
            throw new InvalidArgumentException( "companyId [$companyId] is invalid." );
        }

        $companyModel = $this->companyService->load($companyId);

        return $companyModel;
    }

    /**
     * \Zend\Mvc\Controller\AbstractRestfulController->onDispatch() routes to this method
     * when this endpoint receives a POST command. Exceptions are caught and packaged
     * up into a JSON response.
     *
     * @param mixed $data
     * @return Content
     */
    public function create($data)
    {
        $companyModel = $this->createCompany($data);

        $response = $this->getMarshalledCompany($companyModel);
        $response['success'] = true;

        return $this->getContentResponse($response);
    }

    /**
     * @return CompanyModel
     */
    protected function getCompanyModel()
    {
        return new CompanyModel;
    }

    /**
     * Transforms create data into a company model.
     *
     * @param $rawData
     * @throws \InvalidArgumentException
     * @return CompanyModel
     */
    public function prepareCreateCompany($rawData)
    {
        $data = $this->validateCreateData($rawData);

        $companyModel = $this->getCompanyModel();
        $companyModel->setName($data['name']);

        if (! empty( $data['clusterId'] )) {
            $cluster = $this->getClusterService()->load($data['clusterId']);
            $companyModel->setCluster($cluster);
        }

        if (! empty( $data['onlineStatus'] )) {
            $companyModel->setOnlineStatus($data['onlineStatus']);
        }

        return $companyModel;
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
     * Sends a prepared company model to the company business service to be created.
     *
     * @param mixed[] $data
     * @return CompanyModel
     * @throws InvalidArgumentException
     */
    public function createCompany($data)
    {
        $companyModel = $this->prepareCreateCompany($data);

        return $this->companyService->create($companyModel);
    }

    /**
     * \Zend\Mvc\Controller\AbstractRestfulController->onDispatch() routes to this method
     * when this endpoint receives a PUT command. Exceptions are caught and packaged
     * up into a JSON response.
     *
     * @param int $companyId
     * @param mixed $rawData
     * @return Content
     * @throws InvalidArgumentException
     */
    public function update($companyId, $rawData)
    {
        $data = $this->validateUpdateData($rawData);

        $updatedCompany = $this->updateCompany($companyId, $data);

        $response = $this->getMarshalledCompany($updatedCompany);
        $response['success'] = true;

        return $this->getContentResponse($response);
    }

    /**
     * Transforms update data into a company model.
     *
     * @param int $companyId
     * @param mixed[] $data
     * @throws CannotLoad
     * @return CompanyModel
     */
    public function prepareUpdateCompany($companyId, $data)
    {
        $companyModel = $this->companyService->load($companyId);

        if (! empty( $data['name'] )) {
            $companyModel->setName($data['name']);
        }

        if (! empty( $data['onlineStatus'] )) {
            $companyModel->setOnlineStatus($data['onlineStatus']);
        }

        if(! empty( $data['migrationStatus'])){
            $companyModel->setMigrationStatus($data['migrationStatus']);
        }

        return $companyModel;
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
     * Sends a prepared company model to the company service to be updated.
     *
     * @param int $companyId
     * @param mixed[] $data
     * @return CompanyModel
     */
    public function updateCompany($companyId, $data)
    {
        $companyModel = $this->prepareUpdateCompany($companyId, $data);

        return $this->companyService->update($companyModel);
    }

    /**
     * @return SearchCompanyResultsToArray
     */
    protected function getSearchCompanyResultsMarshaller()
    {
        return $this->serviceLocator->get('EMRDelegator\Service\Company\Marshaller\SearchCompanyResultsToArray');
    }

    /**
     * Searches for a list of companies based on search criteria
     * @return Content
     */
    public function searchAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();

        $searchCompanyRequest = $this->parametersToSearchCompanyRequestMarshaller->marshall($request->getQuery());

        $result = $this->companyService->searchCompany($searchCompanyRequest);

        return $this->getContentResponse(array(
            'companies' => $this->getSearchCompanyResultsMarshaller()->marshall($result)
        ));
    }

    /**
     * @return Content
     */
    public function setMigrationStatusAction()
    {
        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();
        $query = $request->getQuery();

        $rawData = array(
            'companyId' => $query->get('companyId', null),
            'migrationStatus' => $query->get('migrationStatus', null)
        );

        $data = $this->validateSetStatusData($rawData);

        $updatedCompany = $this->updateCompany($data['companyId'], $data);

        $response = $this->getMarshalledCompany($updatedCompany);
        $response['success'] = true;

        return $this->getContentResponse($response);
    }

    /**
     * Change the cluster a company is assigned to
     * @return Content
     */
    public function updateClusterAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $query = $request->getQuery();

        $companyId = $query->get('companyId', null);
        $clusterId = $query->get('clusterId', null);

        $clusterService = $this->getClusterService();

        $cluster = $clusterService->load($clusterId);
        $companyModel = $this->companyService->load($companyId);
        $companyModel->setCluster($cluster);

        $this->companyService->update($companyModel);

        $response['success'] = true;

        return $this->getContentResponse($response);
    }

    /**
     * @param $data
     * @return array
     */
    protected function validateSetStatusData($data)
    {
        return $this->getFormHelper()->validateFormData(new SetMigrationStatus(), $data);
    }

    /**
     * @param ParametersToSearchCompanyRequest $marshaller
     * @return void
     * @setter
     */
    public function setParametersToSearchCompanyRequestMarshaller(ParametersToSearchCompanyRequest $marshaller)
    {
        $this->parametersToSearchCompanyRequestMarshaller = $marshaller;
    }

    /**
     * @return ParametersToSearchCompanyRequest
     */
    public function getParametersToSearchCompanyRequestMarshaller()
    {
        return $this->parametersToSearchCompanyRequestMarshaller;
    }

    /**
     * @param Company $service
     * @return Company
     */
    public function setCompanyService(Company $service)
    {
        $this->companyService = $service;
    }

    /**
     * @return Company
     */
    public function getCompanyService()
    {
        return $this->companyService;
    }
}
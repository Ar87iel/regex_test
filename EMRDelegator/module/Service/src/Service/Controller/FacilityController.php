<?php
/**
 * Services to manage clusters.
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2012 WebPT, INC
 */
namespace Service\Controller;

use DateTime;
use DateTimeZone;
use EMRCore\Zend\Grant\IpGrantDiInterface;
use EMRCore\Zend\Module\Service\Response\Content;
use EMRCore\Zend\Mvc\Controller\RestfulControllerAbstract;
use EMRCore\Zend\ServiceManager\Factory as ServiceManagerFactory;
use EMRDelegator\Facility\Marshaller\FacilityModelToArray;
use EMRDelegator\Model\Facility as FacilityModel;
use EMRDelegator\Service\Facility\Facility as FacilityService;
use InvalidArgumentException;
use Service\Controller\Form\Facility\Create;
use Service\Controller\Form\Facility\Update;

class FacilityController extends RestfulControllerAbstract implements IpGrantDiInterface
{

    /**
     * @param mixed $id
     * @return Content
     */
    public function get($id)
    {
        $content = $this->getFacility($id);
        return $this->getContentResponse($content);
    }

    /**
     * @param int $id
     * @return int
     */
    public function getFacility($id)
    {
        $facilityModel = $this->getFacilityService()->load($id);

        return $this->getMarshalledFacility($facilityModel);


    }

    /**
     * @param FacilityModel $facilityModel
     * @return mixed[]
     */
    private function getMarshalledFacility(FacilityModel $facilityModel)
    {
        /** @var $marshaller FacilityModelToArray */
        $marshaller = $this->getServiceLocator()->get('EMRDelegator\Facility\Marshaller\FacilityModelToArray');

        return array(
            'facility' => $marshaller->marshall($facilityModel)
        );
    }

    /**
     * @param mixed $data
     * @return Content
     * @throws \InvalidArgumentException
     */
    public function create($data)
    {
        $content = $this->createFacility($data);
        return $this->getContentResponse($content);
    }

    /**
     * @param $data
     * @return FacilityModel
     */
    public function prepareCreateFacility($data)
    {
        /** @var $service \EMRDelegator\Service\Company\Company */
        $service = $this->getServiceLocator()->get('EMRDelegator\Service\Company\Company');
        $companyModel = $service->load($data['companyId']);

        $facilityModel = new FacilityModel();
        $facilityModel->setName($data['name']);
        $facilityModel->setCompany($companyModel);
        $facilityModel->setCreatedAt(new DateTime('now',new DateTimeZone('UTC')));

        return $facilityModel;
    }

    /**
     * @param mixed[] $rawData
     * @return mixed[]
     * @throws \InvalidArgumentException
     */
    public function createFacility($rawData)
    {
        $data = $this->validateCreateData($rawData);

        $facilityModel = $this->prepareCreateFacility($data);

        $facilityModel = $this->getFacilityService()->create($facilityModel);

        $response = $this->getFacility($facilityModel->getFacilityId());
        // The read may fail from the read only server(s) if replication from the read write database is delayed
        if(empty($response['facility'])){
            sleep(5);
            $response = $this->getFacility($facilityModel->getFacilityId());
        }

        // Verify creation and set success flag
        if(empty($response['facility'])){
            $response['success'] = false;
        }else{
            $response['success'] = true;
        }
        return $response;
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
     * @param mixed $data
     * @return Content
     */
    public function update($id, $data)
    {
        $content = $this->updateFacility($id, $data);
        return $this->getContentResponse($content);
    }

    /**
     * @param $facilityModel FacilityModel
     * @param $rawData
     * @return FacilityModel
     */
    public function prepareUpdateFacilityModel(FacilityModel $facilityModel,$rawData)
    {
        $data = $this->validateUpdateData($rawData);

        if (!empty($data['name'])) {
            $facilityModel->setName($data['name']);
        }
        return $facilityModel;
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
     * @param int $id
     * @param mixed[] $data
     * @return mixed[]
     */
    public function updateFacility($id, $data)
    {
        $service = $this->getFacilityService();
        $facilityModel = $service->load($id);

        $facilityModel = $this->prepareUpdateFacilityModel($facilityModel,$data);
        $updatedFacilityModel = $service->update($facilityModel);

        $response = $this->getMarshalledFacility($updatedFacilityModel);
        $response['success'] = true;

        return $response;
    }

    /**
     * @param int $id
     * @return Content
     */
    public function delete($id)
    {
        $content = $this->deleteFacility($id);
        return $this->getContentResponse($content);
    }

    /**
     * @param $id
     * @return array
     */
    public function deleteFacility($id)
    {
        $this->getFacilityService()->delete($id);
        return array('success' => true);
    }

    /**
     * @return FacilityService
     */
    private function getFacilityService()
    {
        return $this->serviceLocator->get('EMRDelegator\Service\Facility\Facility');
    }
}

<?php
/**
 *
 * @category WebPT
 * @package EMRDelegator
 * @copyright Copyright (c) 2013 WebPT, INC
 */
namespace Service\Controller;

use EMRCore\Zend\Grant\IpGrantDiInterface;
use EMRCore\Zend\Module\Service\Response\Content;
use EMRCore\Zend\Mvc\Controller\RestfulControllerAbstract;
use EMRCore\Zend\ServiceManager\Factory as ServiceManagerFactory;
use EMRDelegator\Company\Marshaller\CompanyModelToArray;
use EMRDelegator\Facility\Marshaller\FacilityModelToArray;
use EMRDelegator\Model\Company;
use EMRDelegator\Model\Facility;
use EMRDelegator\Service\Facility\Facility as FacilityService;

class FacilityWithCompanyController extends RestfulControllerAbstract implements IpGrantDiInterface
{
    /**
     * @return FacilityService
     */
    private function getFacilityService()
    {
        return $this->serviceLocator->get('EMRDelegator\Service\Facility\Facility');
    }

    /**
     * @param int $facilityId
     * @return Content
     */
    public function get($facilityId)
    {
        $facility = $this->getFacilityById($facilityId);

        return $this->getContentResponse(array(
            'facility' => $this->marshalFacilityToArray($facility),
            'company' => $this->marshalCompanyToArray($facility->getCompany()),
        ));
    }

    /**
     * @param Facility $facility
     * @return mixed[]
     */
    private function marshalFacilityToArray(Facility $facility)
    {
        /** @var $marshaller FacilityModelToArray */
        $marshaller = $this->getServiceLocator()->get('EMRDelegator\Facility\Marshaller\FacilityModelToArray');

        return $marshaller->marshall($facility);
    }

    /**
     * @param Company $company
     * @return mixed[]
     */
    private function marshalCompanyToArray(Company $company)
    {
        /** @var $marshaller CompanyModelToArray */
        $marshaller = $this->getServiceLocator()->get('EMRDelegator\Company\Marshaller\CompanyModelToArray');

        return $marshaller->marshall($company);
    }

    /**
     * @param int $facilityId
     * @return Facility
     */
    private function getFacilityById($facilityId)
    {
        return $this->getFacilityService()->load($facilityId);
    }
}
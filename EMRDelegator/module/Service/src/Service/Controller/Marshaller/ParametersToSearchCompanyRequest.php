<?php
namespace Service\Controller\Marshaller;

use EMRCore\Marshaller\AbstractObjectMarshaller;
use EMRCore\Zend\Http\Request\Pagination\Pagination;
use EMRCore\Zend\Http\Request\Pagination\PaginationDiInterface;
use EMRDelegator\Service\Company\Dto\SearchCompanyRequest;
use Zend\Stdlib\Parameters;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class ParametersToSearchCompanyRequest extends AbstractObjectMarshaller implements PaginationDiInterface
{
    /**
     * @var Pagination
     */
    protected $paginationService;

    /**
     * @return string
     */
    public function getExpectedClassName()
    {
        return '\Zend\Stdlib\Parameters';
    }

    /**
     * @param Parameters $parameters
     * @return SearchCompanyRequest
     */
    protected function marshallValidatedItem($parameters)
    {
        $request = new SearchCompanyRequest;
        $request->setCompanyId($parameters->get('companyId', null));
        $request->setCompanyName($parameters->get('name', null));
        $request->setFacilityId($parameters->get('facilityId', null));
        $request->setFacilityName($parameters->get('facilityName', null));
        $request->setPagination($this->paginationService->getPagination());
        return $request;
    }

    /**
     * @param Pagination $service
     * @return void
     * @setter
     */
    public function setPaginationService(Pagination $service)
    {
        $this->paginationService = $service;
    }

    /**
     * @return Pagination
     */
    public function getPaginationService()
    {
        return $this->paginationService;
    }
}
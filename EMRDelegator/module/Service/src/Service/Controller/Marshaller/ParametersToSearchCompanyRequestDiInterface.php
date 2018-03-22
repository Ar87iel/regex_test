<?php
namespace Service\Controller\Marshaller;

use EMRCore\Zend\ServiceManager\AnnotationAwareInterface;
use Service\Controller\Marshaller\ParametersToSearchCompanyRequest;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
interface ParametersToSearchCompanyRequestDiInterface extends AnnotationAwareInterface
{
    /**
     * @param ParametersToSearchCompanyRequest $marshaller
     * @return void
     * @setter
     */
    public function setParametersToSearchCompanyRequestMarshaller(ParametersToSearchCompanyRequest $marshaller);

    /**
     * @return ParametersToSearchCompanyRequest
     */
    public function getParametersToSearchCompanyRequestMarshaller();
}
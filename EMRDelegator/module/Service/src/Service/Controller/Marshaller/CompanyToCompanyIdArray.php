<?php
namespace Service\Controller\Marshaller;

use EMRCore\Marshaller\MarshallerInterface;
use EMRDelegator\Model\Company as CompanyModel;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class CompanyToCompanyIdArray implements MarshallerInterface
{
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
        return array(
            'id' => $companyModel->getCompanyId(),
        );
    }
}
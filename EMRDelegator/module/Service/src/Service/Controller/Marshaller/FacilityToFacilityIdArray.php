<?php
namespace Service\Controller\Marshaller;

use EMRCore\Marshaller\MarshallerInterface;
use EMRDelegator\Model\Facility as FacilityModel;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class FacilityToFacilityIdArray implements MarshallerInterface
{
    /**
     * @param FacilityModel[] $facilityModels
     * @return array[]
     */
    public function marshall($facilityModels)
    {
        return $this->marshalFacilityIds($facilityModels);
    }

    /**
     * @param FacilityModel[] $facilityModels
     * @return array[]
     */
    protected function marshalFacilityIds($facilityModels)
    {
        $facilities = array();

        if (empty( $facilityModels ))
        {
            return $facilities;
        }

        foreach ($facilityModels as $facilityModel)
        {
            $facilities[] = $this->marshalFacilityId($facilityModel);
        }

        return $facilities;
    }

    /**
     * @param FacilityModel $facilityModel
     * @return int[]
     */
    private function marshalFacilityId(FacilityModel $facilityModel)
    {
        return array(
            'id' => $facilityModel->getFacilityId(),
        );
    }
}
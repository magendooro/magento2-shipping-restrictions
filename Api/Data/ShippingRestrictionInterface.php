<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magendoo\ShippingRestrictions\Api\Data;

interface ShippingRestrictionInterface
{

    const CARRIER_CODE = 'carrier_code';
    const ZIP_CODE = 'zip_code';
    const SHIPPINGRESTRICTION_ID = 'shippingrestriction_id';

    /**
     * Get shippingrestriction_id
     * @return string|null
     */
    public function getShippingrestrictionId();

    /**
     * Set shippingrestriction_id
     * @param string $shippingrestrictionId
     * @return \Magendoo\ShippingRestrictions\ShippingRestriction\Api\Data\ShippingRestrictionInterface
     */
    public function setShippingrestrictionId($shippingrestrictionId);

    /**
     * Get zip_code
     * @return string|null
     */
    public function getZipCode();

    /**
     * Set zip_code
     * @param string $zipCode
     * @return \Magendoo\ShippingRestrictions\ShippingRestriction\Api\Data\ShippingRestrictionInterface
     */
    public function setZipCode($zipCode);

    /**
     * Get carrier_code
     * @return string|null
     */
    public function getCarrierCode();

    /**
     * Set carrier_code
     * @param string $carrierCode
     * @return \Magendoo\ShippingRestrictions\ShippingRestriction\Api\Data\ShippingRestrictionInterface
     */
    public function setCarrierCode($carrierCode);
}


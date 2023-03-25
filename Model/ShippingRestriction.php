<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magendoo\ShippingRestrictions\Model;

use Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionInterface;
use Magento\Framework\Model\AbstractModel;

class ShippingRestriction extends AbstractModel implements ShippingRestrictionInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Magendoo\ShippingRestrictions\Model\ResourceModel\ShippingRestriction::class);
    }

    /**
     * @inheritDoc
     */
    public function getShippingrestrictionId()
    {
        return $this->getData(self::SHIPPINGRESTRICTION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setShippingrestrictionId($shippingrestrictionId)
    {
        return $this->setData(self::SHIPPINGRESTRICTION_ID, $shippingrestrictionId);
    }

    /**
     * @inheritDoc
     */
    public function getZipCode()
    {
        return $this->getData(self::ZIP_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setZipCode($zipCode)
    {
        return $this->setData(self::ZIP_CODE, $zipCode);
    }

    /**
     * @inheritDoc
     */
    public function getCarrierCode()
    {
        return $this->getData(self::CARRIER_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setCarrierCode($carrierCode)
    {
        return $this->setData(self::CARRIER_CODE, $carrierCode);
    }
}


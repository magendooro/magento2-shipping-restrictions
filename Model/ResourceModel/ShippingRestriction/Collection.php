<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magendoo\ShippingRestrictions\Model\ResourceModel\ShippingRestriction;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'shippingrestriction_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Magendoo\ShippingRestrictions\Model\ShippingRestriction::class,
            \Magendoo\ShippingRestrictions\Model\ResourceModel\ShippingRestriction::class
        );
    }
}


<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magendoo\ShippingRestrictions\Api\Data;

interface ShippingRestrictionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get ShippingRestriction list.
     * @return \Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionInterface[]
     */
    public function getItems();

    /**
     * Set zip_code list.
     * @param \Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}


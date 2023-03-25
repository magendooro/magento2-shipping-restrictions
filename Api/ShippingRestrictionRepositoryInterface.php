<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magendoo\ShippingRestrictions\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ShippingRestrictionRepositoryInterface
{

    /**
     * Save ShippingRestriction
     * @param \Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionInterface $shippingRestriction
     * @return \Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionInterface $shippingRestriction
    );

    /**
     * Retrieve ShippingRestriction
     * @param string $shippingrestrictionId
     * @return \Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($shippingrestrictionId);

    /**
     * Retrieve ShippingRestriction matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete ShippingRestriction
     * @param \Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionInterface $shippingRestriction
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionInterface $shippingRestriction
    );

    /**
     * Delete ShippingRestriction by ID
     * @param string $shippingrestrictionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($shippingrestrictionId);
}


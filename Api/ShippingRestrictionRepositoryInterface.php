<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magendoo\ShippingRestrictions\Api;

use Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionInterface;
use Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Shipping Restriction Repository Interface
 */
interface ShippingRestrictionRepositoryInterface
{
    /**
     * Save Shipping Restriction
     *
     * @param ShippingRestrictionInterface $shippingRestriction
     * @return ShippingRestrictionInterface
     * @throws CouldNotSaveException
     */
    public function save(ShippingRestrictionInterface $shippingRestriction): ShippingRestrictionInterface;

    /**
     * Retrieve Shipping Restriction by ID
     *
     * @param int $shippingrestrictionId
     * @return ShippingRestrictionInterface
     * @throws NoSuchEntityException
     */
    public function get(int $shippingrestrictionId): ShippingRestrictionInterface;

    /**
     * Retrieve Shipping Restrictions matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ShippingRestrictionSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ShippingRestrictionSearchResultsInterface;

    /**
     * Delete Shipping Restriction
     *
     * @param ShippingRestrictionInterface $shippingRestriction
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(ShippingRestrictionInterface $shippingRestriction): bool;

    /**
     * Delete Shipping Restriction by ID
     *
     * @param int $shippingrestrictionId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $shippingrestrictionId): bool;
}


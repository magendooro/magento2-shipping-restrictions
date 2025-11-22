<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magendoo\ShippingRestrictions\Model;

use Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionInterface;
use Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionInterfaceFactory;
use Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionSearchResultsInterface;
use Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionSearchResultsInterfaceFactory;
use Magendoo\ShippingRestrictions\Api\ShippingRestrictionRepositoryInterface;
use Magendoo\ShippingRestrictions\Model\ResourceModel\ShippingRestriction as ResourceShippingRestriction;
use Magendoo\ShippingRestrictions\Model\ResourceModel\ShippingRestriction\CollectionFactory as ShippingRestrictionCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Shipping Restriction Repository
 */
class ShippingRestrictionRepository implements ShippingRestrictionRepositoryInterface
{
    /**
     * @var ResourceShippingRestriction
     */
    private ResourceShippingRestriction $resource;

    /**
     * @var ShippingRestrictionInterfaceFactory
     */
    private ShippingRestrictionInterfaceFactory $shippingRestrictionFactory;

    /**
     * @var ShippingRestrictionCollectionFactory
     */
    private ShippingRestrictionCollectionFactory $shippingRestrictionCollectionFactory;

    /**
     * @var ShippingRestrictionSearchResultsInterfaceFactory
     */
    private ShippingRestrictionSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param ResourceShippingRestriction $resource
     * @param ShippingRestrictionInterfaceFactory $shippingRestrictionFactory
     * @param ShippingRestrictionCollectionFactory $shippingRestrictionCollectionFactory
     * @param ShippingRestrictionSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceShippingRestriction $resource,
        ShippingRestrictionInterfaceFactory $shippingRestrictionFactory,
        ShippingRestrictionCollectionFactory $shippingRestrictionCollectionFactory,
        ShippingRestrictionSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->shippingRestrictionFactory = $shippingRestrictionFactory;
        $this->shippingRestrictionCollectionFactory = $shippingRestrictionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function save(ShippingRestrictionInterface $shippingRestriction): ShippingRestrictionInterface
    {
        try {
            $this->resource->save($shippingRestriction);
            $this->logger->info('Shipping restriction saved via repository', [
                'id' => $shippingRestriction->getShippingrestrictionId(),
                'zip_code' => $shippingRestriction->getZipCode(),
                'carrier_code' => $shippingRestriction->getCarrierCode()
            ]);
        } catch (\Exception $exception) {
            $this->logger->error('Failed to save shipping restriction', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);
            throw new CouldNotSaveException(__(
                'Could not save the shipping restriction: %1',
                $exception->getMessage()
            ));
        }
        return $shippingRestriction;
    }

    /**
     * @inheritDoc
     */
    public function get(int $shippingRestrictionId): ShippingRestrictionInterface
    {
        $shippingRestriction = $this->shippingRestrictionFactory->create();
        $this->resource->load($shippingRestriction, $shippingRestrictionId);

        if (!$shippingRestriction->getShippingrestrictionId()) {
            throw new NoSuchEntityException(__(
                'Shipping Restriction with id "%1" does not exist.',
                $shippingRestrictionId
            ));
        }

        return $shippingRestriction;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $criteria): ShippingRestrictionSearchResultsInterface
    {
        $collection = $this->shippingRestrictionCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(ShippingRestrictionInterface $shippingRestriction): bool
    {
        try {
            $shippingRestrictionModel = $this->shippingRestrictionFactory->create();
            $this->resource->load(
                $shippingRestrictionModel,
                $shippingRestriction->getShippingrestrictionId()
            );
            $this->resource->delete($shippingRestrictionModel);

            $this->logger->info('Shipping restriction deleted via repository', [
                'id' => $shippingRestriction->getShippingrestrictionId()
            ]);
        } catch (\Exception $exception) {
            $this->logger->error('Failed to delete shipping restriction', [
                'id' => $shippingRestriction->getShippingrestrictionId(),
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);
            throw new CouldNotDeleteException(__(
                'Could not delete the Shipping Restriction: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $shippingRestrictionId): bool
    {
        return $this->delete($this->get($shippingRestrictionId));
    }
}


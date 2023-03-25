<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magendoo\ShippingRestrictions\Model;

use Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionInterface;
use Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionInterfaceFactory;
use Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionSearchResultsInterfaceFactory;
use Magendoo\ShippingRestrictions\Api\ShippingRestrictionRepositoryInterface;
use Magendoo\ShippingRestrictions\Model\ResourceModel\ShippingRestriction as ResourceShippingRestriction;
use Magendoo\ShippingRestrictions\Model\ResourceModel\ShippingRestriction\CollectionFactory as ShippingRestrictionCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ShippingRestrictionRepository implements ShippingRestrictionRepositoryInterface
{

    /**
     * @var ShippingRestrictionCollectionFactory
     */
    protected $shippingRestrictionCollectionFactory;

    /**
     * @var ShippingRestrictionInterfaceFactory
     */
    protected $shippingRestrictionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var ShippingRestriction
     */
    protected $searchResultsFactory;

    /**
     * @var ResourceShippingRestriction
     */
    protected $resource;


    /**
     * @param ResourceShippingRestriction $resource
     * @param ShippingRestrictionInterfaceFactory $shippingRestrictionFactory
     * @param ShippingRestrictionCollectionFactory $shippingRestrictionCollectionFactory
     * @param ShippingRestrictionSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceShippingRestriction $resource,
        ShippingRestrictionInterfaceFactory $shippingRestrictionFactory,
        ShippingRestrictionCollectionFactory $shippingRestrictionCollectionFactory,
        ShippingRestrictionSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->shippingRestrictionFactory = $shippingRestrictionFactory;
        $this->shippingRestrictionCollectionFactory = $shippingRestrictionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(
        ShippingRestrictionInterface $shippingRestriction
    ) {
        try {
            $this->resource->save($shippingRestriction);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the shippingRestriction: %1',
                $exception->getMessage()
            ));
        }
        return $shippingRestriction;
    }

    /**
     * @inheritDoc
     */
    public function get($shippingRestrictionId)
    {
        $shippingRestriction = $this->shippingRestrictionFactory->create();
        $this->resource->load($shippingRestriction, $shippingRestrictionId);
        if (!$shippingRestriction->getId()) {
            throw new NoSuchEntityException(__('ShippingRestriction with id "%1" does not exist.', $shippingRestrictionId));
        }
        return $shippingRestriction;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
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
    public function delete(
        ShippingRestrictionInterface $shippingRestriction
    ) {
        try {
            $shippingRestrictionModel = $this->shippingRestrictionFactory->create();
            $this->resource->load($shippingRestrictionModel, $shippingRestriction->getShippingrestrictionId());
            $this->resource->delete($shippingRestrictionModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the ShippingRestriction: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($shippingRestrictionId)
    {
        return $this->delete($this->get($shippingRestrictionId));
    }
}


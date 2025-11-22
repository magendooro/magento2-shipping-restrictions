<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magendoo\ShippingRestrictions\Controller\Adminhtml\ShippingRestriction;

use Magendoo\ShippingRestrictions\Api\ShippingRestrictionRepositoryInterface;
use Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionInterfaceFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

/**
 * Edit shipping restriction controller
 */
class Edit extends \Magendoo\ShippingRestrictions\Controller\Adminhtml\ShippingRestriction
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'Magendoo_ShippingRestrictions::shippingrestriction';

    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * @var ShippingRestrictionRepositoryInterface
     */
    private ShippingRestrictionRepositoryInterface $repository;

    /**
     * @var ShippingRestrictionInterfaceFactory
     */
    private ShippingRestrictionInterfaceFactory $restrictionFactory;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param ShippingRestrictionRepositoryInterface $repository
     * @param ShippingRestrictionInterfaceFactory $restrictionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        ShippingRestrictionRepositoryInterface $repository,
        ShippingRestrictionInterfaceFactory $restrictionFactory,
        LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->repository = $repository;
        $this->restrictionFactory = $restrictionFactory;
        $this->logger = $logger;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('shippingrestriction_id');

        // Load existing or create new model
        if ($id) {
            try {
                $model = $this->repository->get((int) $id);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This Shipping Restriction no longer exists.'));
                $this->logger->warning('Attempted to edit non-existent shipping restriction', ['id' => $id]);
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        } else {
            $model = $this->restrictionFactory->create();
        }

        // Register model for use in form
        $this->_coreRegistry->register('magendoo_shippingrestrictions_shippingrestriction', $model);

        // Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Shipping Restriction') : __('New Shipping Restriction'),
            $id ? __('Edit Shipping Restriction') : __('New Shipping Restriction')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Shipping Restrictions'));
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getShippingrestrictionId()
                ? __('Edit Shipping Restriction %1', $model->getShippingrestrictionId())
                : __('New Shipping Restriction')
        );

        return $resultPage;
    }

    /**
     * Check if user has permission to access this resource
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}


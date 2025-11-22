<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magendoo\ShippingRestrictions\Controller\Adminhtml\ShippingRestriction;

use Magendoo\ShippingRestrictions\Api\ShippingRestrictionRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

/**
 * Delete shipping restriction controller
 */
class Delete extends \Magendoo\ShippingRestrictions\Controller\Adminhtml\ShippingRestriction
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'Magendoo_ShippingRestrictions::shippingrestriction';

    /**
     * @var ShippingRestrictionRepositoryInterface
     */
    private ShippingRestrictionRepositoryInterface $repository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param ShippingRestrictionRepositoryInterface $repository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ShippingRestrictionRepositoryInterface $repository,
        LoggerInterface $logger
    ) {
        $this->repository = $repository;
        $this->logger = $logger;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('shippingrestriction_id');

        if ($id) {
            try {
                // Delete using repository
                $this->repository->deleteById($id);

                $this->messageManager->addSuccessMessage(__('You deleted the Shipping Restriction.'));
                $this->logger->info('Shipping restriction deleted successfully', ['id' => $id]);

                return $resultRedirect->setPath('*/*/');
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This Shipping Restriction no longer exists.'));
                $this->logger->warning('Attempted to delete non-existent shipping restriction', [
                    'id' => $id
                ]);
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->error('Error deleting shipping restriction', [
                    'id' => $id,
                    'error' => $e->getMessage()
                ]);
                return $resultRedirect->setPath('*/*/edit', ['shippingrestriction_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('We can\'t delete this Shipping Restriction right now.'));
                $this->logger->critical('Exception while deleting shipping restriction', [
                    'id' => $id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return $resultRedirect->setPath('*/*/edit', ['shippingrestriction_id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a Shipping Restriction to delete.'));
        return $resultRedirect->setPath('*/*/');
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


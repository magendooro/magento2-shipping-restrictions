<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magendoo\ShippingRestrictions\Controller\Adminhtml\ShippingRestriction;

use Magendoo\ShippingRestrictions\Api\ShippingRestrictionRepositoryInterface;
use Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionInterfaceFactory;
use Magendoo\ShippingRestrictions\Model\Validator\InputValidator;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Save shipping restriction controller
 */
class Save extends Action
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'Magendoo_ShippingRestrictions::shippingrestriction';

    /**
     * @var DataPersistorInterface
     */
    private DataPersistorInterface $dataPersistor;

    /**
     * @var ShippingRestrictionRepositoryInterface
     */
    private ShippingRestrictionRepositoryInterface $repository;

    /**
     * @var ShippingRestrictionInterfaceFactory
     */
    private ShippingRestrictionInterfaceFactory $restrictionFactory;

    /**
     * @var InputValidator
     */
    private InputValidator $inputValidator;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param ShippingRestrictionRepositoryInterface $repository
     * @param ShippingRestrictionInterfaceFactory $restrictionFactory
     * @param InputValidator $inputValidator
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        ShippingRestrictionRepositoryInterface $repository,
        ShippingRestrictionInterfaceFactory $restrictionFactory,
        InputValidator $inputValidator,
        LoggerInterface $logger
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->repository = $repository;
        $this->restrictionFactory = $restrictionFactory;
        $this->inputValidator = $inputValidator;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            try {
                // Sanitize and validate input
                $data = $this->inputValidator->sanitize($data);
                $this->inputValidator->validate($data);

                $id = $this->getRequest()->getParam('shippingrestriction_id');

                // Load existing or create new
                if ($id) {
                    try {
                        $model = $this->repository->get($id);
                    } catch (NoSuchEntityException $e) {
                        $this->messageManager->addErrorMessage(__('This Shipping Restriction no longer exists.'));
                        $this->logger->warning('Attempted to edit non-existent shipping restriction', [
                            'id' => $id
                        ]);
                        return $resultRedirect->setPath('*/*/');
                    }
                } else {
                    $model = $this->restrictionFactory->create();
                }

                // Set data only for allowed fields
                $model->setZipCode($data['zip_code']);
                $model->setCarrierCode($data['carrier_code']);

                // Save
                $this->repository->save($model);

                $this->messageManager->addSuccessMessage(__('You saved the Shipping Restriction.'));
                $this->dataPersistor->clear('magendoo_shippingrestrictions_shippingrestriction');

                $this->logger->info('Shipping restriction saved successfully', [
                    'id' => $model->getShippingrestrictionId(),
                    'zip_code' => $model->getZipCode(),
                    'carrier_code' => $model->getCarrierCode()
                ]);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', [
                        'shippingrestriction_id' => $model->getShippingrestrictionId()
                    ]);
                }
                return $resultRedirect->setPath('*/*/');

            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->error('Validation error while saving shipping restriction', [
                    'error' => $e->getMessage(),
                    'data' => $data
                ]);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the Shipping Restriction.')
                );
                $this->logger->critical('Exception while saving shipping restriction', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            $this->dataPersistor->set('magendoo_shippingrestrictions_shippingrestriction', $data);
            return $resultRedirect->setPath('*/*/edit', [
                'shippingrestriction_id' => $this->getRequest()->getParam('shippingrestriction_id')
            ]);
        }
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


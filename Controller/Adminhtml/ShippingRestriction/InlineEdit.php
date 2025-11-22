<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magendoo\ShippingRestrictions\Controller\Adminhtml\ShippingRestriction;

use Magendoo\ShippingRestrictions\Api\ShippingRestrictionRepositoryInterface;
use Magendoo\ShippingRestrictions\Model\Validator\InputValidator;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Inline edit shipping restriction controller
 */
class InlineEdit extends Action
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'Magendoo_ShippingRestrictions::shippingrestriction';

    /**
     * @var JsonFactory
     */
    private JsonFactory $jsonFactory;

    /**
     * @var ShippingRestrictionRepositoryInterface
     */
    private ShippingRestrictionRepositoryInterface $repository;

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
     * @param JsonFactory $jsonFactory
     * @param ShippingRestrictionRepositoryInterface $repository
     * @param InputValidator $inputValidator
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        ShippingRestrictionRepositoryInterface $repository,
        InputValidator $inputValidator,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->repository = $repository;
        $this->inputValidator = $inputValidator;
        $this->logger = $logger;
    }

    /**
     * Inline edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach ($postItems as $modelId => $itemData) {
                    try {
                        // Load the model
                        $model = $this->repository->get((int) $modelId);

                        // Merge with existing data
                        $data = array_merge($model->getData(), $itemData);

                        // Sanitize and validate
                        $data = $this->inputValidator->sanitize($data);
                        $this->inputValidator->validate($data);

                        // Update only allowed fields
                        $model->setZipCode($data['zip_code']);
                        $model->setCarrierCode($data['carrier_code']);

                        // Save
                        $this->repository->save($model);

                        $this->logger->info('Inline edit successful for shipping restriction', [
                            'id' => $modelId,
                            'zip_code' => $model->getZipCode(),
                            'carrier_code' => $model->getCarrierCode()
                        ]);

                    } catch (NoSuchEntityException $e) {
                        $messages[] = __('[Shipping Restriction ID: %1] This restriction no longer exists.', $modelId);
                        $error = true;
                        $this->logger->warning('Inline edit failed - restriction not found', ['id' => $modelId]);
                    } catch (LocalizedException $e) {
                        $messages[] = __('[Shipping Restriction ID: %1] %2', $modelId, $e->getMessage());
                        $error = true;
                        $this->logger->error('Inline edit validation error', [
                            'id' => $modelId,
                            'error' => $e->getMessage()
                        ]);
                    } catch (\Exception $e) {
                        $messages[] = __('[Shipping Restriction ID: %1] %2', $modelId, $e->getMessage());
                        $error = true;
                        $this->logger->critical('Inline edit exception', [
                            'id' => $modelId,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
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


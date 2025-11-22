<?php
/**
 * Copyright © All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magendoo\ShippingRestrictions\Plugin;

use Magendoo\ShippingRestrictions\Helper\Config;
use Magendoo\ShippingRestrictions\Model\ResourceModel\ShippingRestriction\CollectionFactory as RestrictionsCollectionFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Psr\Log\LoggerInterface;

/**
 * Plugin to restrict shipping methods based on zip code
 */
class ZipCodeRestrictionPlugin
{
    /**
     * @var RestrictionsCollectionFactory
     */
    private RestrictionsCollectionFactory $restrictionsCollectionFactory;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param RestrictionsCollectionFactory $restrictionsCollectionFactory
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        RestrictionsCollectionFactory $restrictionsCollectionFactory,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->restrictionsCollectionFactory = $restrictionsCollectionFactory;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Check if the shipping method is available for the given address
     *
     * @param CarrierInterface $subject
     * @param Result|bool $result
     * @param RateRequest $request
     * @return Result|bool
     */
    public function afterCollectRates(
        CarrierInterface $subject,
        $result,
        RateRequest $request
    ) {
        // Check if module is enabled
        if (!$this->config->isEnabled()) {
            return $result;
        }

        // If result is false or not a Result object, return as is
        if (!$result instanceof Result) {
            return $result;
        }

        $destPostcode = $request->getDestPostcode();
        $carrierCode = $subject->getCarrierCode();

        // Query restrictions for this zip code and carrier
        $restrictions = $this->restrictionsCollectionFactory->create()
            ->addFieldToFilter('zip_code', $destPostcode)
            ->addFieldToFilter('carrier_code', $carrierCode);

        // If restriction found, the shipping method IS allowed for this zip code
        if ($restrictions->count() > 0) {
            $this->logger->debug('Shipping method allowed by restriction', [
                'carrier' => $carrierCode,
                'zip_code' => $destPostcode
            ]);
            return $result;
        }

        // No restriction found - remove this carrier's rates
        $this->logger->debug('Shipping method restricted - no matching restriction found', [
            'carrier' => $carrierCode,
            'zip_code' => $destPostcode
        ]);

        $filteredRates = [];
        foreach ($result->getAllRates() as $rate) {
            /** @var Method $rate */
            if ($rate->getCarrier() !== $carrierCode) {
                $filteredRates[] = $rate;
            }
        }

        $result->reset();
        foreach ($filteredRates as $rate) {
            $result->append($rate);
        }

        return $result;
    }
}

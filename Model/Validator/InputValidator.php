<?php
/**
 * Copyright © All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magendoo\ShippingRestrictions\Model\Validator;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Input validator for shipping restriction data
 */
class InputValidator extends AbstractValidator
{
    /**
     * Required fields for shipping restriction
     */
    private const REQUIRED_FIELDS = ['zip_code', 'carrier_code'];

    /**
     * Maximum field lengths
     */
    private const MAX_LENGTHS = [
        'zip_code' => 10,
        'carrier_code' => 255
    ];

    /**
     * Validate shipping restriction data
     *
     * @param array $data
     * @return bool
     * @throws LocalizedException
     */
    public function validate(array $data): bool
    {
        $this->_clearMessages();

        // Check required fields
        foreach (self::REQUIRED_FIELDS as $field) {
            if (empty($data[$field])) {
                throw new LocalizedException(
                    __('Required field "%1" is missing or empty.', $field)
                );
            }
        }

        // Validate zip code format
        if (!$this->validateZipCode($data['zip_code'])) {
            throw new LocalizedException(
                __('Invalid zip code format. Only alphanumeric characters and hyphens are allowed.')
            );
        }

        // Validate carrier code format
        if (!$this->validateCarrierCode($data['carrier_code'])) {
            throw new LocalizedException(
                __('Invalid carrier code format. Only alphanumeric characters and underscores are allowed.')
            );
        }

        // Check field lengths
        foreach (self::MAX_LENGTHS as $field => $maxLength) {
            if (isset($data[$field]) && strlen($data[$field]) > $maxLength) {
                throw new LocalizedException(
                    __('Field "%1" exceeds maximum length of %2 characters.', $field, $maxLength)
                );
            }
        }

        return true;
    }

    /**
     * Validate zip code format
     *
     * @param string $zipCode
     * @return bool
     */
    private function validateZipCode(string $zipCode): bool
    {
        // Allow alphanumeric characters and hyphens
        return (bool) preg_match('/^[a-zA-Z0-9\-]+$/', $zipCode);
    }

    /**
     * Validate carrier code format
     *
     * @param string $carrierCode
     * @return bool
     */
    private function validateCarrierCode(string $carrierCode): bool
    {
        // Allow alphanumeric characters and underscores (typical Magento carrier code format)
        return (bool) preg_match('/^[a-zA-Z0-9_]+$/', $carrierCode);
    }

    /**
     * Sanitize input data
     *
     * @param array $data
     * @return array
     */
    public function sanitize(array $data): array
    {
        $sanitized = [];

        // Only allow whitelisted fields
        $allowedFields = ['shippingrestriction_id', 'zip_code', 'carrier_code'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                // Trim whitespace
                $sanitized[$field] = is_string($data[$field])
                    ? trim($data[$field])
                    : $data[$field];
            }
        }

        return $sanitized;
    }
}

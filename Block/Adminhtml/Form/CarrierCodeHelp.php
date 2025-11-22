<?php
declare(strict_types=1);

namespace Magendoo\ShippingRestrictions\Block\Adminhtml\Form;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Shipping\Model\Config as ShippingConfig;

/**
 * Block to display available carrier codes in the admin form
 */
class CarrierCodeHelp extends Template
{
    /**
     * @var ShippingConfig
     */
    private ShippingConfig $shippingConfig;

    /**
     * @param Context $context
     * @param ShippingConfig $shippingConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        ShippingConfig $shippingConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->shippingConfig = $shippingConfig;
    }

    /**
     * Get all active shipping carriers
     *
     * @return array
     */
    public function getActiveCarriers(): array
    {
        return $this->shippingConfig->getActiveCarriers();
    }

    /**
     * Get carrier title from configuration
     *
     * @param string $carrierCode
     * @return string
     */
    public function getCarrierTitle(string $carrierCode): string
    {
        $title = $this->_scopeConfig->getValue(
            'carriers/' . $carrierCode . '/title',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $title ?: 'Shipping Method';
    }

    /**
     * Render the help text HTML
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        $carriers = $this->getActiveCarriers();

        if (empty($carriers)) {
            return '<div class="admin__field-note" style="margin-top: -15px; margin-bottom: 20px;">
                <span style="color: #f00;">No shipping methods are currently configured.</span>
            </div>';
        }

        $html = '<div class="admin__field-note" style="margin-top: -15px; margin-bottom: 20px;">';
        $html .= '<strong>Available shipping method codes (copy and paste):</strong><br/>';
        $html .= '<ul style="margin-top: 8px; padding-left: 20px; list-style-type: disc;">';

        foreach ($carriers as $carrierCode => $carrier) {
            $carrierTitle = $this->getCarrierTitle($carrierCode);
            $html .= sprintf(
                '<li><code style="background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-family: monospace; color: #007bdb;">%s</code> - %s</li>',
                $this->escapeHtml($carrierCode),
                $this->escapeHtml($carrierTitle)
            );
        }

        $html .= '</ul>';
        $html .= '<div style="margin-top: 10px; padding: 8px; background: #e3f2fd; border-left: 3px solid #2196F3; font-size: 12px;">';
        $html .= '<strong>Note:</strong> Only shipping methods explicitly whitelisted for a ZIP code will be available to customers. ';
        $html .= 'If no restriction exists for a ZIP code + carrier combination, that carrier will be blocked.';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}

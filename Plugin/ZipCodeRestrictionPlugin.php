<?php
namespace Magendoo\ShippingRestrictions\Plugin;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magendoo\ShippingRestrictions\Model\ResourceModel\ShippingRestriction\CollectionFactory as RestrictionsCollectionFactory;
use Magento\Shipping\Model\Carrier\CarrierInterface;

class ZipCodeRestrictionPlugin
{
    protected $restrictionsCollectionFactory;
    
    public function __construct(        
        RestrictionsCollectionFactory $restrictionsCollectionFactory
    ) {        
        $this->restrictionsCollectionFactory = $restrictionsCollectionFactory;
    }

    /**
     * Check if the shipping method is available for the given address
     *
     * @param \Magento\Shipping\Model\Carrier\AbstractCarrier $subject
     * @param array $result
     * @return array
     */
    public function afterCollectRates(
        CarrierInterface $subject,
        $result,
        RateRequest $request
    ) {
        /*
        if (!$this->getConfigFlag('active')) {
            return $result;
        }*/
        $restrictions = $this->restrictionsCollectionFactory->create()
            ->addFieldToFilter('zip_code', $request->getDestPostcode())
            ->addFieldToFilter('carrier_code', $subject->getCarrierCode());
            \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Psr\Log\LoggerInterface::class)->debug($restrictions->getSelect());

        //if I found the zip code, it is fine, the method should be shown
        if ($restrictions->count()) {
            return $result;
        }

        $rates = [];
        foreach ($result->getAllRates() as $rate) {
            if ($rate->getCarrier() != $subject->getCarrierCode()) {
                $rates[] = $rate;            
            }
        }
        $result->reset();
        foreach ($rates as $rate) {
            $result->append($rate);
        }
        return $result;        
    }
}

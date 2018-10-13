<?php

class Interactone_Fixedshipping_Helper_Data extends Interactone_Common_Helper_Data
{
    const XML_PATH_ENABLED       = 'interactone_fixedshipping/settings/enabled';
    const XML_PATH_FLATRATE      = 'interactone_fixedshipping/settings/flatrate';
    const XML_PATH_FEDEX         = 'interactone_fixedshipping/settings/fedex';
    const XML_PATH_UPS           = 'interactone_fixedshipping/settings/ups';
    const XML_PATH_USPS          = 'interactone_fixedshipping/settings/usps';
    const XML_PATH_PRODUCTMATRIX = 'interactone_fixedshipping/settings/productmatrix';
    const XML_PATH_PREMIUMRATE = 'interactone_fixedshipping/settings/premiumrate';

    /**
     * @var array
     */
    protected $result;

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }

    /**
     * @return bool
     */
    public function isFlatRateEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfig(self::XML_PATH_FLATRATE);
    }

    /**
     * @return bool
     */
    public function isFedexEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfig(self::XML_PATH_FEDEX);
    }

    /**
     * @return bool
     */
    public function isUpsEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfig(self::XML_PATH_UPS);
    }

    /**
     * @return bool
     */
    public function isUspsEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfig(self::XML_PATH_USPS);
    }

    /**
     * @return bool
     */
    public function isProductmatrixEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfig(self::XML_PATH_PRODUCTMATRIX);
    }
    /**
     * @return bool
     */
    public function isPremiumrateEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfig(self::XML_PATH_PREMIUMRATE); 
    }

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return array
     */
    public function getFixedShippingAmount(Mage_Shipping_Model_Rate_Request $request)
    {
        if (empty($this->result)) {
            $result = array(
                'packages' => 0,
                'amount'   => 0,
            );

            if ($request->getAllItems()) {
                foreach ($request->getAllItems() as $item ){///* @var $item Mage_Sales_Model_Quote_Item */) {
                    if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                        continue;
                    }

                    $itemPackageQty = $item->isShipSeparately() ? $item->getQty() : 1;

                    if ($item->getHasChildren()) {
                        foreach ($item->getChildren() as $child) {
                            if ($child->getProduct()->isVirtual()) {
                                continue;
                            } else {
                                if ($child->getFreeShipping()) {
                                    $childPackageQty = ($child->isShipSeparately() ? $child->getQty() : 1) * $itemPackageQty;
                                    $result['packages'] += $childPackageQty;
                                    $result['amount']   += $item->getFixedShippingAmount() * $childPackageQty;
                                }
                            }
                        }
                    } else {
                        if ($item->getFreeShipping()) {
                            $result['packages'] += $itemPackageQty;
                            $result['amount']   += $item->getFixedShippingAmount() * $itemPackageQty;
                        }
                    }
                }
            }

            // If packages == 0, check order level fixed shipping amount
            if ($result['packages'] === 0 && $request->getFreeShipping()) {
                $result['packages'] = 1;
                $result['amount']   = $request->getFixedShippingAmount();
            }

            $this->result = $result;
        }

        return $this->result;
    }
}
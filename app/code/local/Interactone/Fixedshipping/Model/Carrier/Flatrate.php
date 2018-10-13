<?php

class Interactone_Fixedshipping_Model_Carrier_Flatrate
    extends Mage_Shipping_Model_Carrier_Flatrate
{
    /**
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!Mage::helper('interactone_fixedshipping')->isFlatRateEnabled()) {
            return parent::collectRates($request);
        }

        // Pull fixed shipping amount
        $fixed = Mage::helper('interactone_fixedshipping')->getFixedShippingAmount($request);
        $fixedPackages = $fixed['packages'];
        $fixedAmount   = $fixed['amount'];

        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $this->setFreeBoxes($fixedPackages);

        $result = Mage::getModel('shipping/rate_result');
        $price  = $this->getConfigData('price');
        $packageValue = $request->getPackageValue();

        switch ($this->getConfigData('type')) {
            case 'O':
                $shippingPrice = $price;
                break;
            case 'I':
                $shippingPrice = ($request->getPackageQty() * $price) - ($fixedPackages * $price);
                break;
            default:
                $shippingPrice = false;
                break;
        }

        if ($request->getFreeShipping() || $request->getPackageQty() == $fixedPackages) {
            $shippingPrice = $fixedAmount;
        } elseif ($fixedPackages > 0) {
            $shippingPrice += $fixedAmount;
        }

        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);

        // Check for fixed shipping or minimum order amount
        $allow = ($fixedPackages > 0) || ($packageValue >= $this->getConfigData('flatrate_shipping_subtotal'));

        if ($shippingPrice !== false && $allow) {
            $method = Mage::getModel('shipping/rate_result_method');

            $method->setCarrier('flatrate');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('flatrate');
            $method->setMethodTitle($this->getConfigData('name'));

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);

            $result->append($method);
        }

        return $result;
    }
}
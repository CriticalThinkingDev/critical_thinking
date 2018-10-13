<?php

class Interactone_Fixedshipping_Model_Carrier_Fedex
    extends Mage_Usa_Model_Shipping_Carrier_Fedex
{
    /**
     * @var float
     */
    protected $fixedAmount;

    /**
     * Retrieve information from carrier configuration
     *
     * @param   string $field
     * @return  mixed
     */
    public function getConfigData($field)
    {
        if (!Mage::helper('interactone_fixedshipping')->isFedexEnabled()) {
            if ($field == 'free_method' && $this->fixedAmount !== 0) {
                return false;
            }
        }

        return parent::getConfigData($field);
    }

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return null
     */
    protected function _updateFreeMethodQuote($request)
    {
        // Pull fixed shipping amount
        $fixed = Mage::helper('interactone_fixedshipping')->getFixedShippingAmount($request);
        $fixedAmount = $this->fixedAmount = $fixed['amount'];

        if (!Mage::helper('interactone_fixedshipping')->isFedexEnabled() || $fixedAmount === 0) {
            return parent::_updateFreeMethodQuote($request);
        }

        $freeMethod = $this->getConfigData('free_method');
        if (!$freeMethod) {
            return;
        }
        $freeRateId = false;

        if (is_object($this->_result)) {
            foreach ($this->_result->getAllRates() as $i=>$item) {
                if ($item->getMethod() == $freeMethod) {
                    $freeRateId = $i;
                    break;
                }
            }
        }

        if ($freeRateId === false) {
            return;
        }

        $price = null;
        if ($request->getFreeMethodWeight() > 0) {
            $this->_setFreeMethodRequest($freeMethod);

            $result = $this->_getQuotes();
            if ($result && ($rates = $result->getAllRates()) && count($rates)>0) {
                if ((count($rates) == 1) && ($rates[0] instanceof Mage_Shipping_Model_Rate_Result_Method)) {
                    $price = $rates[0]->getPrice() + $fixedAmount;
                }
                if (count($rates) > 1) {
                    foreach ($rates as $rate) {
                        if ($rate instanceof Mage_Shipping_Model_Rate_Result_Method
                            && $rate->getMethod() == $freeMethod
                        ) {
                            $price = $rate->getPrice() + $fixedAmount;
                        }
                    }
                }
            }
        } else {
            /**
             * if we can apply free shipping for all order we should force price
             * to $0.00 for shipping with out sending second request to carrier
             */
            $price = $fixedAmount;
        }

        /**
         * if we did not get our free shipping method in response we must use its old price
         */
        if (!is_null($price)) {
            $this->_result->getRateById($freeRateId)->setPrice($price);
        }
    }
}
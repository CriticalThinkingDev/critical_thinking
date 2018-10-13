<?php
class Krishinc_Ajaxtocart_Block_List extends Mage_Catalog_Block_Product_List
{
	
	/******************** Functions for downloadable product in listing page******/

    /**
     * Enter description here...
     *
     * @return boolean
     */
    public function getLinksPurchasedSeparately($_product)
    {
        return $_product->getLinksPurchasedSeparately();
    }
    
    
     /**
     * Enter description here...
     *
     * @return array
     */
    public function getLinks($_product)
    {
        return $_product->getTypeInstance(true)
            ->getLinks($_product);
    }
	 
    public function hasLinks($_product)
    {
    	 return $_product->getTypeInstance(true)
            ->hasLinks($_product);
    }
    /**
     * Enter description here...
     *
     * @param Mage_Downloadable_Model_Link $link
     * @return string
     */
    public function getFormattedLinkPrice($link,$_product)
    {
        $price = $link->getPrice();
        $store = $_product->getStore();

        if (0 == $price) {
            return '';
        }

        $taxCalculation = Mage::getSingleton('tax/calculation');
        if (!$taxCalculation->getCustomer() && Mage::registry('current_customer')) {
            $taxCalculation->setCustomer(Mage::registry('current_customer'));
        }

        $taxHelper = Mage::helper('tax');
        $coreHelper = $this->helper('core');
        $_priceInclTax = $taxHelper->getPrice($link->getProduct(), $price, true);
        $_priceExclTax = $taxHelper->getPrice($link->getProduct(), $price);

        $priceStr = '<span class="price-notice">+';
        if ($taxHelper->displayPriceIncludingTax()) {
            $priceStr .= $coreHelper->currencyByStore($_priceInclTax, $store);
        } elseif ($taxHelper->displayPriceExcludingTax()) {
            $priceStr .= $coreHelper->currencyByStore($_priceExclTax, $store);
        } elseif ($taxHelper->displayBothPrices()) {
            $priceStr .= $coreHelper->currencyByStore($_priceExclTax, $store);
            if ($_priceInclTax != $_priceExclTax) {
                $priceStr .= ' (+'.$coreHelper
                    ->currencyByStore($_priceInclTax, $store).' '.$this->__('Incl. Tax').')';
            }
        }
        $priceStr .= '</span>';

        return $priceStr;
    }

    /**
     * Returns price converted to current currency rate
     *
     * @param float $price
     * @return float
     */
    public function getCurrencyPrice($price,$_product)
    {
        $store = $_product->getStore();
        return $this->helper('core')->currencyByStore($price, $store, false);
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getJsonConfig($_product)
    {
        $config = array();
        $coreHelper = Mage::helper('core');

        foreach ($this->getLinks($_product) as $link) {
            $config[$link->getId()] = $coreHelper->currency($link->getPrice(), false, false);
        }

        return $coreHelper->jsonEncode($config);
    }

    public function getLinkSamlpeUrl($link)
    {
        return $this->getUrl('downloadable/download/linkSample', array('link_id' => $link->getId()));
    }

    /**
     * Return title of links section
     *
     * @return string
     */
    public function getLinksTitle($_product)
    {
        if ($_product->getLinksTitle()) {
            return $_product->getLinksTitle();
        }
        return Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }

    /**
     * Return true if target of link new window
     *
     * @return bool
     */
    public function getIsOpenInNewWindow()
    {
        return Mage::getStoreConfigFlag(Mage_Downloadable_Model_Link::XML_PATH_TARGET_NEW_WINDOW);
    }

    /**
     * Returns whether link checked by default or not
     *
     * @param Mage_Downloadable_Model_Link $link
     * @return bool
     */
    public function getIsLinkChecked($link,$_product)
    {
        $configValue = $_product->getPreconfiguredValues()->getLinks();
        if (!$configValue || !is_array($configValue)) {
            return false;
        }

        return $configValue && (in_array($link->getId(), $configValue));
    }

    /**
     * Returns value for link's input checkbox - either 'checked' or ''
     *
     * @param Mage_Downloadable_Model_Link $link
     * @return string
     */
    public function getLinkCheckedValue($link,$_product)
    {
        return $this->getIsLinkChecked($link, $_product) ? 'checked' : '';
    }
}
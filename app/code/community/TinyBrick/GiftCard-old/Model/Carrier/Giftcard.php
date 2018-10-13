<?php
class TinyBrick_GiftCard_Model_Carrier_Giftcard extends Mage_Shipping_Model_Carrier_Abstract
{
	protected $_code = 'giftcard';
	
	public function collectRates(Mage_Shipping_Model_Rate_Request $request) 
	{
		$isGiftcardOnly = true;
		foreach($this->_getQuote()->getAllItems() as $item) {
			if(!$item->getIsGiftcard()) {
				$isGiftcardOnly = false;
			}
		}
		if(strpos(Mage::getStoreConfig('carriers/'.$this->_code.'/specificcountry'), $this->_getQuote()->getShippingAddress()->getCountryId()) === false) {
			return false;
		}
		if(!$isGiftcardOnly || !Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
			return false;
		}
		
		$method = Mage::getModel('shipping/rate_result_method');
	 	
		$method->setCarrier($this->_code);
	    $method->setCarrierTitle($this->getConfigData('title'));
	    
	    $method->setMethod('giftcard');
	    $method->setMethodTitle(Mage::getStoreConfig('carriers/'.$this->_code.'/title'));
	    
	    if(!Mage::getStoreConfig('carriers/'.$this->_code.'/percent')) {
	    	$method->setPrice(Mage::getStoreConfig('carriers/'.$this->_code.'/price'));
	    } else {
	    	$price = $this->_getQuote()->getShippingAddress()->getGrandTotal() * (Mage::getStoreConfig('carriers/'.$this->_code.'/percent') / 100);
	    	$method->setPrice($price);
	    }
	    	    
	    $result = Mage::getModel('shipping/rate_result');
	    $result->append($method);
	    return $result;
	}
	
	protected function _getQuote()
	{
		return Mage::getSingleton('checkout/session')->getQuote();
	}
}
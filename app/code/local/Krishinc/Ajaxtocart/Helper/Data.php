<?php 
class Krishinc_Ajaxtocart_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getAddUrl()
	{
		if (Mage::app()->getStore()->isCurrentlySecure()) {

            return Mage::getUrl('ajaxtocart/ajax/add', array('_secure'=>true));
        }
        else{
            return Mage::getUrl('ajaxtocart/ajax/add');
        }
		//return Mage::getUrl('ajaxtocart/ajax/add');
	}
	public function getInfoUrl()
	{
		if (Mage::app()->getStore()->isCurrentlySecure()) {

            return Mage::getUrl('ajaxtocart/ajax/getInfo', array('_secure'=>true));
        }
        else{
            return Mage::getUrl('ajaxtocart/ajax/getInfo');
        }
		//return Mage::getUrl('ajaxtocart/ajax/getInfo');
	}
}
<?php
class Unirgy_StoreLocator_Block_Stores extends  Mage_Core_Block_Template
{
 
	public function getAllCountries()
	{
		$countries = Mage::getModel('ustorelocator/location')->getCollection()
						->addFieldToSelect('country')->setOrder('country','asc');
		$countries->getSelect()->distinct(true);
		$countryArray = array();
		foreach ($countries as $country){
			$countryArray []= $country->getData('country');
		}
	 $countryList = Mage::getResourceModel('directory/country_collection')
	 ->addFieldToFilter('country_id', $countryArray)
                    ->loadData()
                     ->toOptionArray(false); 

		return $countryList;
	}
	
 
	
}
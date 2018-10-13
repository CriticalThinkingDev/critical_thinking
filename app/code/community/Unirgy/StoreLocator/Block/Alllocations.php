<?php
class Unirgy_StoreLocator_Block_Alllocations extends  Mage_Core_Block_Template
{
	
	
	public function getFilteredLocations()
	{
		$country = ($this->getRequest()->getParam('country'))?$this->getRequest()->getParam('country'):'';
        $region =  $this->getRequest()->getParam('region_id');
		$postcode =  $this->getRequest()->getParam('postcode');
		$locations = Mage::getModel('ustorelocator/location')->getCollection()->addFieldToFilter('status',1);
		
		if($postcode=='')
		{
			if($region)
			{
				$locations->addFieldToFilter(array('region', 'region_id'),array(array('eq' => $region),array('eq' => $region)));
			}
			
			if($country)
			{ 
				$locations->addFieldToFilter('country',$country);
			} 
			$locations->addFieldToFilter('status','1');
			$locations->setOrder('city','ASC');
			$locations->setOrder('title','ASC');
		}
		else
		{
			$latlong = Mage::getModel('ustorelocator/location')->geocode($postcode,$country);
			$radius = Mage::getStoreConfig('ustorelocator/general/default_radius');
			
			$locations = Mage::getResourceModel('ustorelocator/location_collection')->addAreaFilter($latlong['lat'],$latlong['lng'],$radius);
			
			if($region)
			{
				$locations->addFieldToFilter(array('region', 'region_id'),array(array('eq' => $region),array('eq' => $region)));
			}
			
			if($country)
			{ 
				$locations->addFieldToFilter('country',$country);
			} 
			$locations->addFieldToFilter('status','1');
			$locations->setOrder('distance','ASC');
		}
		
		return $locations; 
	}
}
<?php

class Krishinc_Catalogue_Helper_Data extends Mage_Core_Helper_Abstract
{
	 
	/**
	 * Function to create virtual product array 
	 *
	 * @return array
	 */
	public function getProductsArray()
	{
		$collection = Mage::getModel('catalog/product')
					->getCollection()
					->addFieldToFilter('status', array('eq'=>'1'))
					//->addFieldToFilter('type_id', array('eq'=>'simple'))
					->addFieldToFilter('sku', array('like'=>'%ITO%'))
					->addAttributeToSelect('sku')
					->addAttributeToSelect('name'); 
		
		$data = array();
		
		if($collection->count()>1)
		{
			foreach($collection as $col)
			{  
				$data[] = array('value' =>$col->getSku(), 'label'=>$col->getSku().' - '.addslashes($col->getName())); 		
			}
		} 
		return $data; 
	}
	
	/**
	 * Function to return month array
	 *
	 * @return array
	 */
	public function getMonthArray()
	{
		$data = array();
		$data = array(
					array('value'=>'01','label'=>'January'),
					array('value'=>'02','label'=>'February'),
					array('value'=>'03','label'=>'March'),
					array('value'=>'04','label'=>'April'),
					array('value'=>'05','label'=>'May'),
					array('value'=>'06','label'=>'June'),
					array('value'=>'07','label'=>'July'),
					array('value'=>'08','label'=>'August'),
					array('value'=>'09','label'=>'September'),
					array('value'=>'10','label'=>'October'),
					array('value'=>'11','label'=>'November'),
					array('value'=>'12','label'=>'December'),
		
		);
	 
		return $data;
	}
    /**
	 * Get config setting <catalogue_choose>
	 *
	 * @return array|FALSE
	 */
	public function getCatalogueProduct($storeId)
	{  
		 return unserialize($this->config('catalogue_choose', $storeId));  
	}
	/**
	 * Function to retrieve current month sku from admin system >> configuration >> Catalog >> Attach Catalogue to each order
	 *
	 * @return booleon|string
	 */
	public function getCatalogueProductFromCurrentMonth()
	{
	  	$currentMonth = date("m", Mage::getModel('core/date')->timestamp(time()));
		$allMonthProduct = $this->getCatalogueProduct(Mage::app()->getStore()->getId());
		if(!$allMonthProduct){
			return false;
		} 
		foreach($allMonthProduct as $fld){
			$month = $fld['month'];
			$sku  = $fld['product']; 
			
			if($month[0] == $currentMonth)
			{ 
				return $sku[0];
			}			  
		}  
		return false; 
	}
	/**
	 * Get module configuration value
	 *
	 * @param string $value
	 * @param string $store
	 * @return mixed Configuration setting
	 */
	public function config($value, $store = null)
	{
		$store = is_null($store) ? Mage::app()->getStore() : $store;

		$configscope = Mage::app()->getRequest()->getParam('store');
		if( $configscope && ($configscope !== 'undefined') ){
			$store = $configscope;
		}
 
		return Mage::getStoreConfig("catalog/catalogue/$value", $store); 
	}
}
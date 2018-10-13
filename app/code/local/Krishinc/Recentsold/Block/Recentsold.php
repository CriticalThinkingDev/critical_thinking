<?php
class Krishinc_Recentsold_Block_Recentsold extends Mage_Core_Block_Template 
{
 
	public function getRecentlySoldItems()
	{
		$storeID = Mage::app()->getStore()->getId();
		$itemsCollection = Mage::getResourceModel('sales/order_item_collection') 
							//->addFieldToFilter('main_table.created_at', array('from'=>$dateStart,'to'=>$dateEnd)) 
							->join('order', 'order_id=entity_id') 
							->addFieldToFilter('main_table.store_id', array('eq'=>$storeID))
							->setOrder('main_table.created_at','desc') 
							->setPageSize(12);
		$itemsCollection->getSelect()->group(`main_table`.'product_id');
		$products = array();		
		if(sizeof($itemsCollection)>0)
		{
			foreach ($itemsCollection as $item) {
				$product = Mage::getModel('catalog/product')->load($item->getProductId());
				$products[] = $product; 
			}	
		} 
		
		return $products;	 
			
	} 
	
}




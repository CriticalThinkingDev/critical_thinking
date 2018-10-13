<?php
class Krishinc_Overridepo_Model_Overridepo extends Mage_Core_Model_Abstract
{
	public function salesOrderUpdateSearchPriority()
	{
                 Mage::log('start',null,'priotity_search.log');
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$writeConnection = $resource->getConnection('core_write');
		$table = $resource->getTableName('sales/order');
		$itemTable = $resource->getTableName('sales/order_item');
		$query = 'SELECT entity_id FROM ' .$table . '
		where  '.$table.'.status = "complete" and  '.$table.'.is_updated_for_search = "1" limit 0,100';
				
		$order_ids = array();
		$results = $readConnection->fetchAll($query);
		if(count($results)>0)
		{ 
			foreach ($results as $res) {
				$order_ids[] = 	$res['entity_id'];		
				$itemquery = 'SELECT item_id,sku,product_id,qty_ordered FROM ' .$itemTable . '
		where  '.$itemTable.'.order_id = "'.$res['entity_id'].'" and  '.$itemTable.'.is_updated_for_search = "1"';
				$itemResults = $readConnection->fetchAll($itemquery);		
				if(count($itemResults)>0)
				{ 
					foreach ($itemResults as $item) {
						$product = Mage::getModel('catalog/product')->load($item['product_id']);
						// if($product->getVisibility()!= 1) {
							if($product->getOverride() == 0)
							{   
								$priority = 0;
								//echo 'priority  : '. (int)$product->getPriority().'<br>';
								$priority = (int)$product->getPriority()+1;
								$product->setPriority($priority)->save();
								$updatesql = '';
							    $updatesql =  'UPDATE ' .$itemTable . ' SET  '.$itemTable.'.is_updated_for_search = "2"
			where  '.$itemTable.'.item_id = "'.$item['item_id'].'";';
								 $writeConnection->query($updatesql);
							}	 
						/*} else {
							$grouped_product_model = Mage::getModel('catalog/product_type_grouped');
							$groupedParentId = $grouped_product_model->getParentIdsByChild($product->getId()); 
							if(sizeof($groupedParentId) >0){
								$groupProduct = Mage::getModel('catalog/product')->load($groupedParentId[0]);	
								if($groupProduct->getOverride() == 0)
								{   
									$priority = 0;
									///echo 'priority grouped: '.(int)$groupProduct->getPriority().'<br>';
									$priority = (int)$groupProduct->getPriority()+1;
									$groupProduct->setPriority($priority)->save(); 
									$updatesql = '';
								    $updatesql =  'UPDATE ' .$itemTable . ' SET  '.$itemTable.'.is_updated_for_search = "2" where  '.$itemTable.'.item_id = "'.$item['item_id'].'";';
									 $writeConnection->query($updatesql); 
								}
							}
						}*/
					}
				}
				 
				$updatesql = '';
			    $updatesql =  'UPDATE ' .$table . ' SET  '.$table.'.is_updated_for_search = "2"
		where  '.$table.'.entity_id = "'.$res['entity_id'].'";';
				 
				 $writeConnection->query($updatesql);  
			} 	 
		}			
		return $order_ids;  
	}
}

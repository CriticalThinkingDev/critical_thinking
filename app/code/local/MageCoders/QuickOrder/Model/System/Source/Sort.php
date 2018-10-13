<?php 
class MageCoders_QuickOrder_Model_System_Source_Sort extends Mage_Core_Model_Abstract{

	 public function toOptionArray()
     {
	 	$sort = array(
					array(
						'label'=>'Name',
						'value'=>'name'
					),
					array(
						'label'=>'Price',
						'value'=>'price'
					),					
				);
				
		return $sort;		
		
	 }

}
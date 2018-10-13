<?php 
class MageCoders_QuickOrder_Model_System_Source_Filter extends Mage_Core_Model_Abstract{

	 public function toOptionArray()
     {
	 	$filter = Mage::getModel('catalog/product_visibility')->getOptionArray();
		if(!empty($filter)){
			$options = array();
			foreach($filter as $k=>$v){
				$options[] = array('label'=>$v,'value'=>$k);
			}
			return $options;
		}
		
	 }

}
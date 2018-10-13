<?php
class MageCoders_QuickOrder_Helper_Protected extends Mage_Core_Helper_Abstract{

	const VERSION = 'community';//changed version by krish developer

	
	public function loadProduct($product){
			
			$data = array();
			
			$data['product']['name'] = $product->getName();
			$data['product']['sku'] = $product->getSku();
			$data['product']['id'] = $product->getId();
			
			if($product->getTypeId()=='configurable'){
				$options = $this->_getConfigurableOptions($product);
				
				if($options){
					
					$options->row_id = 0;
				
					$data['product']['options'] = $options;
					$data['product']['has_options'] = true;
					$data['product']['is_configurable'] = true;
				}
				
			}
			$data['success'] =  true;
			return $data;

	}
	
	public function _getConfigurableOptions($product){
	
		$configBlock = Mage::app()->getLayout()->createBlock('catalog/product_view_type_configurable');
		
		$configBlock->setProduct($product);
		
		$jsonConfig = json_decode($configBlock->getJsonConfig());
		
		if(!empty($jsonConfig)){
			return $jsonConfig;
		}
	
	}
	
	public function getProductTypes(){
		if(self::VERSION=='community'){
			return array('simple','downloadable','virtual');
		}else{
			return array('simple','downloadable','virtual','configurable');
		}
	}
	
	
	public function isProductAllowed($product){
	 
		$allowed = true;
		$product_types = $this->getProductTypes();
		 
		if(!in_array($product->getTypeId(),$product_types)){
			$allowed = false;
		}elseif($product->getHasOptions() && $product->getTypeId()!='configurable' && $product->getTypeId()!='downloadable'){// added condition by krish developer to add downloadable product in autosuggestion
			$allowed = false;
		}
		
		if(!$product->getIsSalable()){
			$allowed = false;
		}
		 
		return $allowed;
	}
	
	public function getConfig($key){
		if($key!=''){
			return Mage::getStoreConfig('quickorder/settings/'.$key);
		}
	}
	
	
}
<?php

class Krishinc_Standards_Model_Standards extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('standards/standards');
    }
    
     public function CheckUrlKey($urlKey){
      
    	$collection = $this->getCollection();
    	$collection->addFieldToFilter('pagelink',$urlKey);
    	
    	if($collection->count() > 0){
    		$brandObj =  $collection->getFirstItem();
    		return $brandObj->getId();
    	}
    	return null;
    }
	public function getProductSkuOptionArray()
	{
		$model = Mage::getModel('catalog/product');
		$_product = $model->getCollection();
		$data_array = array();
		$data_array[] = array('value'=>'', 'label'=>Mage::helper('standards')->__('--Please Select Product--'));
		$data = $_product->getData();
		
		
		foreach($data as $product)
		{
			$model->load($product['entity_id']);
			$pname = $model->getName();  
			$data_array[] = array('value'=>$product['sku'], 'label'=>$product['sku']);
		}
		return $data_array;
	}
	public function getProductIdOptionArray()
	{
		$model = Mage::getModel('catalog/product');
		$_product = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('type_id', array('eq','simple'))->setOrder('name','asc');
		$data_array = array();
		$data_array[] = array('value'=>'', 'label'=>Mage::helper('standards')->__('--Please Select Product--'));
		$data = $_product->getData();	
		
		foreach($data as $product)
		{
			$model->load($product['entity_id']);
			$pname = $model->getName().' - [ '.$model->getSku().' ] ';  
			$data_array[] = array('value'=>$product['entity_id'], 'label'=>$pname);
		}
		return $data_array;
	}
	public function getProductIdOptionArrayByFilter($productids)
	{
		$model = Mage::getModel('catalog/product');
		$_product = Mage::getModel('catalog/product')->getCollection()->addFieldToFilter('entity_id', array('in' => $productids))->setOrder('name','asc');
		
		
		$data_array = array();
		$data_array[] = array('value'=>'', 'label'=>Mage::helper('standards')->__('Choose a product'));
		$data_array[] = array('value'=>'', 'label'=>Mage::helper('standards')->__('-----------------'));
		$data = $_product->getData();	
		
		foreach($data as $product)
		{
			$model->load($product['entity_id']);
			$pname = $model->getName();  
			$data_array[] = array('value'=>$product['entity_id'], 'label'=>$pname);
		}
		return $data_array;
	}
	public function getStates($country = 0)
	{
		$statearray = array();
		$statearray[] = array('value'=>'', 'label'=>Mage::helper('standards')->__('Choose a state'));	 
		$states = Mage::getModel('directory/country')->load($country)->getRegions();
	 	foreach ($states as $state)	 
		{      
			$statearray[] = array('value'=>$state->getId(), 'label'=>$state->getName());	 
		}
		return $statearray;
	}
	public function getProductSku($id)
	{
		$model = Mage::getModel('catalog/product');
		$_product = Mage::getModel('catalog/product')->getCollection()->addFieldToFilter('entity_id',$id);
		
		$data = $_product->getData();
		
		return $data[0]['sku'];
	}
	
}
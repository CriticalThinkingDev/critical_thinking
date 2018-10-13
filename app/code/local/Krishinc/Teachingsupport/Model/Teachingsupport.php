<?php

class Krishinc_Teachingsupport_Model_Teachingsupport extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('teachingsupport/teachingsupport');
    }
    
    public function getProductSkuOptionArray()
	{
		$_products = Mage::getResourceModel('catalog/product_collection')
					->addAttributeToSelect(array('entity_id','name','series','status'));
  		$_products->addAttributeToFilter('type_id', array('in' => array(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE)));
		$_products->setOrder('name','asc');
		 
		$data_array = array();
		$data_array[] = array('value'=>'', 'label'=>Mage::helper('standards')->__('--Please Select Product--'));
		 
		foreach($_products as $_product)
		{   
			$data_array[] = array('value'=>$_product->getSku(), 'label'=>$_product->getName() . ' - '. $_product->getSku().' ['.(($_product->getStatus()==1)?'Active':'In Active').']');
		}
		return $data_array;
	} 

	public function getProductNameBySku($sku)
	{
		$_product = Mage::getResourceModel('catalog/product_collection')
					->addAttributeToSelect(array('entity_id','name'))
					->addAttributeToFilter('sku',$sku)
					->getFirstItem();
		 
		if($_product)
		{
			return $_product->getName();
		}
		return $sku;
	}
}
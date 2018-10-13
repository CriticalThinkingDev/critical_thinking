<?php

class Krishinc_Free_Block_Donate extends Mage_Core_Block_Template
{
	
	public function getAllDonateProducts()
    {
    	$storeId = Mage::app()->getStore()->getId();
		$products = Mage::getModel('catalog/product')->getCollection()
					->addAttributeToSelect(array('name', 'small_image', 'is_donate'))
					->addAttributeToFilter('is_donate',array('eq' => 1))
					->setStoreId($storeId)
					->addStoreFilter($storeId);
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
		return $products;	
    }
}
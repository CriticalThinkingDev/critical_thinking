<?php

class Krishinc_Grouped_Block_Product_View_Seriesproducts extends Mage_Core_Block_Template
{
    public function getProduct()
    {
        $product = $this->_getData('product');
        if (!$product) {
            $product = Mage::registry('product');
        }
        return $product;
    }

    public function getSeriesProducts($product) {
	if ($product->getTypeId() == 'grouped'){
	    $associated_products = $product->getTypeInstance(true)->getAssociatedProducts($product);
	    $final_arr = array();
	    foreach($associated_products as $product) {
		$final_arr[] = $product->getId();
	    }
	    if(count($final_arr) > 0) {
		$visibility = array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG);
		$collection = Mage::getModel('catalog/product')->getCollection();
		$collection->addAttributeToSelect('*');
		$collection->addAttributeToFilter('entity_id' , array('in' => $final_arr));
		$collection->addAttributeToFilter('visibility', $visibility);
		$collection->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED); 
	    }
		
		/* Set the array as the position given to associated products in admin */
		$new_final_array = array();
		foreach($final_arr as $key => $val) {
			foreach($collection as $product) {
				if($product->getId() == $val) {
					$new_final_array[] = $product;
					continue;
				}
			}
		}
	    return $new_final_array;
	}
	return array();
    }
	
	
	public function getMasterGroupSeriesProducts($product) {
		if ($product->getTypeId() == 'grouped'){
			$associated_products = $product->getTypeInstance(true)->getAssociatedProducts($product);
			$final_arr = array();
			foreach($associated_products as $product) {
			$final_arr[] = $product->getId();
			}
			if(count($final_arr) > 0) {
			//$visibility = array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG);
			/* Krish dev(12 Nov) : to change the series product listing for master grouped product */
			$visibility = array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
			$collection = Mage::getModel('catalog/product')->getCollection();
			$collection->addAttributeToSelect('*');
			$collection->addAttributeToFilter('entity_id' , array('in' => $final_arr));
			$collection->addAttributeToFilter('visibility', $visibility);
			$collection->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED); 
			}
			
			
			/* Set the array as the position given to associated products in admin */
			$new_final_array = array();
			foreach($final_arr as $key => $val) {
				foreach($collection as $product) {
					if($product->getId() == $val) {
						$new_final_array[] = $product;
						continue;
					}
				}
			}
			return $new_final_array;
			
		}
		return array();
    }
	
	
	
	public function getParentProduct()
    {
        $product = Mage::registry('parent_product');
        return $product;
    }
}
<?php
/**
 * created : 11/20/12
 * 
 * @category Krishinc
 * @package Krishinc_Customlinks
 * @author Bijal Bhavsar
 * @copyright Krishinc - 2012 - http://www.krishinc.com
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Override of product Link model
 * Used to add Component Sold link in product 
 * @package Krishinc_Customlinks
 */
class Krishinc_Customlinks_Model_Product_Link extends Mage_Catalog_Model_Product_Link
{
	const LINK_TYPE_COMPONENTSOLD   = 6;
	const LINK_TYPE_BUNDLECONTENT   = 7;
	const LINK_TYPE_OTHERFORMAT   = 8;
	    
    public function useComponentsoldLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_COMPONENTSOLD);
        return $this;
    }
    
     public function useBundlecontentLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_BUNDLECONTENT);
        return $this;
    }
    
     public function useOtherformatLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_OTHERFORMAT); 
        return $this;
    }
    

     /**
     * Save data for product relations
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Catalog_Model_Product_Link
     */
    public function saveProductRelations($product)
    {
    	$data = $product->getComponentsoldLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_COMPONENTSOLD);
        }  
        $data1 = $product->getBundlecontentLinkData();
        if (!is_null($data1)) {
            $this->_getResource()->saveProductLinks($product, $data1, self::LINK_TYPE_BUNDLECONTENT);
        }  

        $data2 = $product->getOtherformatLinkData();
        if (!is_null($data2)) {
            $this->_getResource()->saveProductLinks($product, $data2, self::LINK_TYPE_OTHERFORMAT);
        }  
    	return parent::saveProductRelations($product);
    }
    
 
}
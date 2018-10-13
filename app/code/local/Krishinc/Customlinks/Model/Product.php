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
 * Override of product Model
 * Used to add Component Sold products saving to product 
 * @package Krishinc_Customlinks
 */


class Krishinc_Customlinks_Model_Product extends Mage_Catalog_Model_Product  
//class Krishinc_Customlinks_Model_Product extends TBT_RewardsOnly_Model_Catalog_Product
{
	
    /**
     * Set type instance for external
     *
     * @param Mage_Catalog_Model_Product_Type_Abstract $instance  Product type instance
     * @param bool                                     $singleton Whether instance is singleton
     * @return Mage_Catalog_Model_Product
     */
    public function setTypeInstance($instance, $singleton = false)
    {
        if ($singleton === true) {
            $this->_typeInstanceSingleton = $instance;
        } else {
            $this->_typeInstance = $instance;
        }
        return $this;
    }

    /**
     * Retrieve link instance
     *
     * @return  Mage_Catalog_Model_Product_Link
     */
    public function getLinkInstance()
    {
        if (!$this->_linkInstance) {
            $this->_linkInstance = Mage::getSingleton('catalog/product_link');
        }
        return $this->_linkInstance;
    }
	
	 /**
     * Retrieve array of Componentsold products
     *
     * @return array
     */
    public function getComponentsoldProducts()
    {
    	
        if (!$this->hasComponentsoldProducts()) { 
            $products = array();
            foreach ($this->getComponentsoldProductCollection() as $product) {
                $products[] = $product;
            }
            $this->setComponentsoldProducts($products);
        } 
        return $this->getData('componentsold_products');
    }

    /**
     * Retrieve Componentsold products identifiers
     *
     * @return array
     */
    public function getComponentsoldProductIds()
    {
        if (!$this->hasComponentsoldProductIds()) {
            $ids = array();
            foreach ($this->getComponentsoldProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setComponentsoldProductIds($ids);
        }
        return $this->getData('componentsold_product_ids');
    }

    /**
     * Retrieve collection Componentsold product
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function getComponentsoldProductCollection()
    {  
        $collection = $this->getLinkInstance()->useComponentsoldLinks()
			            ->getProductCollection()
			            ->setIsStrongMode();
        $collection->setProduct($this);
        return $collection; 
    }

    /**
     * Retrieve collection Componentsold link
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function getComponentsoldLinkCollection()
    {
        $collection = $this->getLinkInstance()->useComponentsoldLinks()
            ->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }
    
     
     
	 
	 /**
     * Retrieve array of Bundlecontent products
     *
     * @return array
     */
    public function getBundlecontentProducts()
    {
    	
        if (!$this->hasBundlecontentProducts()) { 
            $products = array();
            foreach ($this->getBundlecontentProductCollection() as $product) {
                $products[] = $product;
            }
            $this->setBundlecontentProducts($products);
        } 
        return $this->getData('bundlecontent_products');
    }

    /**
     * Retrieve Bundlecontent products identifiers
     *
     * @return array
     */
    public function getBundlecontentProductIds()
    {
        if (!$this->hasBundlecontentProductIds()) {
            $ids = array();
            foreach ($this->getBundlecontentProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setBundlecontentProductIds($ids);
        }
        return $this->getData('bundlecontent_product_ids');
    }

    /**
     * Retrieve collection Bundlecontent product
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function getBundlecontentProductCollection()
    {  
        $collection = $this->getLinkInstance()->useBundlecontentLinks()
			            ->getProductCollection()
			            ->setIsStrongMode();
        $collection->setProduct($this);
        return $collection; 
    }

    /**
     * Retrieve collection Bundlecontent link
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function getBundlecontentLinkCollection()
    {
        $collection = $this->getLinkInstance()->useBundlecontentLinks()
            ->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }
    
     
     
	 
	 /**
     * Retrieve array of Otherformat products
     *
     * @return array
     */
    public function getOtherformatProducts()
    {
    	
        if (!$this->hasOtherformatProducts()) { 
            $products = array();
            foreach ($this->getOtherformatProductCollection() as $product) {
                $products[] = $product;
            }
            $this->setOtherformatProducts($products);
        } 
        return $this->getData('otherformat_products');
    }

    /**
     * Retrieve Otherformat products identifiers
     *
     * @return array
     */
    public function getOtherformatProductIds()
    {
        if (!$this->hasOtherformatProductIds()) {
            $ids = array();
            foreach ($this->getOtherformatProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setOtherformatProductIds($ids);
        }
        return $this->getData('otherformat_product_ids');
    }

    /**
     * Retrieve collection Otherformat product
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function getOtherformatProductCollection()
    {  
        $collection = $this->getLinkInstance()->useOtherformatLinks()
			            ->getProductCollection()
			            ->setIsStrongMode();
        $collection->setProduct($this);
        return $collection; 
    }

    /**
     * Retrieve collection Otherformat link
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function getOtherformatLinkCollection()
    {
        $collection = $this->getLinkInstance()->useOtherformatLinks()
            ->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }
    
	
}
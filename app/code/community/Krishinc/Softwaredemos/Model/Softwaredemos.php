<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog category
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Krishinc_Softwaredemos_Model_Softwaredemos extends Mage_Core_Model_Abstract
{
	 public function _construct()
    {
        parent::_construct();
        $this->_init('softwaredemos/softwaredemos');
    }
    
     
 
  /**
     * Retrieve array of Bundlecontent products
     *
     * @return array
     */
    public function getSoftwaredemosProducts()
    {
        if (!$this->hasSoftwaredemosProducts()) { 
            $products = array();
            $abc = $this->getSoftwaredemosProductCollection();
         	$productsArr = $abc->getData();
            foreach ($productsArr as $key => $product) { 
                $products[$product['product_id']] = $product['position']; 
            }   
            
            $this->setSoftwaredemosProducts($products); 
        }  
        return $this->getData('softwaredemos_products');
    }

    /**
     * Retrieve Bundlecontent products identifiers
     *
     * @return array
     */
    public function getSoftwaredemosProductIds()
    {
        if (!$this->hasSoftwaredemosProductIds()) {
        	
            $ids = array();
            foreach ($this->getSoftwaredemosProducts() as $product) {  
            	
                $ids[] = $product->getId(); 
            }
            $this->setSoftwaredemosProductIds($ids);
        }
        return $this->getData('softwaredemos_product_ids'); 
    }

    /**
     * Retrieve collection Bundlecontent product
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function getSoftwaredemosProductCollection()
    {  
    	 
        $collection = Mage::getModel('softwaredemos/softwaredemos_product')->getCollection()->addFieldToFilter('softwaredemos_id',$this->getId());   
        
        return $collection;  
    }

}
  
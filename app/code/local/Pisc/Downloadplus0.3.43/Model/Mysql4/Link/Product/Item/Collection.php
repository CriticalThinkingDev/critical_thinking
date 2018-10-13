<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable links product items resource collection
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.1
 */

class Pisc_Downloadplus_Model_Mysql4_Link_Product_Item_Collection extends Mage_Downloadable_Model_Mysql4_Sample_Collection
{

	protected $_attributeFilter = null;
	
    protected function _construct()
    {
    	$this->_init('downloadplus/link_product_item');
    }

    /*
     * Returns Array of Model Downloadplus_Link_Purchased_Item
     */
    public function getByProductId($productId, $store=null)
    {
    	$result = Array();

        $sql = $this->getSelect()
	            	->where('product_id=?', $productId)
	            	->order('sort_order ASC');

        $collection = $this->getResource()->getReadConnection()->fetchAll($sql);
        foreach ($collection as $item) {
        	$productItem = Mage::getModel('downloadplus/link_product_item')->load($item['link_id']);
        	if ($store) {
        		if ($store instanceof Mage_Core_Model_Store) {
        			$productItem->setStoreId($store->getId());
        		} else {
        			$productItem->setStoreId($store);
        		}
        	}
        	$result[$item['link_id']] = $productItem;
        }

        $this->_items = $result;
        $this->_afterLoad();
        
        return $this->_items;
    }

    /*
     * Adds Attribute to Filter
     */
    public function addAttributeFilter($attribute, $value)
    {
    	$this->_attributeFilter = Array($attribute => $value);
    	return $this;
    }
    
    public function clearAttributeFilter()
    {
    	$this->_attributeFilter = null;
    	return $this;
    }
    
    /*
     * Apply attribute filter on prepared collection
     */
    protected function _afterLoad()
    {
    	//parent::_afterLoad();
    	
    	if ($this->_attributeFilter) {
    		$items = $this->_items;
    		if (!$items) {
    			$items = $this->getItems();
    		}
    		foreach ($items as $id=>$item) {
    			$valid = true;
    			foreach ($this->_attributeFilter as $key=>$value) {
    				$valid = $valid && ($item->getAttribute($key)==$value);
    			}
    			if (!$valid) {
    				unset($items[$id]);
    			}
    		}
    		$this->_items = $items;
    	}
    	return $this;
    }
    
}
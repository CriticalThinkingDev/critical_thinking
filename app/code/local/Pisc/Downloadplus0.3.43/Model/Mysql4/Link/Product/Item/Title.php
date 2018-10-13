<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable additional product link title resource
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.0
 */

class Pisc_Downloadplus_Model_Mysql4_Link_Product_Item_Title extends Mage_Core_Model_Mysql4_Abstract
{

    protected function  _construct()
    {
        $this->_init('downloadplus/link_product_item_title', 'title_id');
    }

    public function saveTitle($linkObject)
    {
	    $sql = $this->_getReadAdapter()->select()
	    			->from($this->getTable('downloadplus/link_product_item_title'))
			    	->where('link_id = ?', $linkObject->getId())
			    	->where('store_id = ?', $linkObject->getStoreId());
	    
	    if ($this->_getReadAdapter()->fetchOne($sql)) {
	    	$where = $this->_getReadAdapter()->quoteInto('link_id = ?', $linkObject->getId()) .
	                    ' AND ' . $this->_getReadAdapter()->quoteInto('store_id = ?', $linkObject->getStoreId());
	    	
	    	if ($linkObject->getUseDefaultTitle()) {
	    		$this->_getWriteAdapter()->delete(
	    					$this->getTable('downloadplus/link_product_item_title'), $where);
	    	} else {
	    		$this->_getWriteAdapter()->update(
	    					$this->getTable('downloadplus/link_product_item_title'),
	    					array('title' => $linkObject->getData('link_title')), $where);
	    	}
	    } else {
	    	if (!$linkObject->getUseDefaultTitle()) {
	    		$this->_getWriteAdapter()->insert(
		    		$this->getTable('downloadplus/link_product_item_title'),
		    				array(
	                      		'link_id' => $linkObject->getId(),
	                            'store_id' => $linkObject->getStoreId(),
	                            'title' => $linkObject->getData('link_title'),
		    				)
	    			);
	    	}
	    }
    }

    /**
     * Delete data by item(s)
     *
     * @param Mage_Downloadable_Model_Link|array|int $items
     * @return Mage_Downloadable_Model_Mysql4_Link
     */
    public function deleteItems($items)
    {
    	$where = '';
    	if ($items instanceof Pisc_Downloadplus_Model_Link_Product_Item) {
    		$where = $this->_getReadAdapter()->quoteInto('link_id = ?', $items->getId());
    	}
    	elseif (is_array($items)) {
    		$where = $this->_getReadAdapter()->quoteInto('link_id in (?)', $items);
    	}
    	else {
    		$where = $this->_getReadAdapter()->quoteInto('link_id = ?', $items);
    	}
    	if ($where) {
    		$this->_getWriteAdapter()->delete(
    				$this->getTable('downloadplus/link_product_item'), $where);
    		$this->_getWriteAdapter()->delete(
    				$this->getTable('downloadplus/link_product_item_title'), $where);
    	}
    	return $this;
    }
    
    /**
     * Retrieve links searchable data
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getSearchableData($productId, $storeId)
    {
    	$select = $this->_getReadAdapter()->select()
    	->from(array('link' => $this->getMainTable()), null)
    	->join(
    			array('link_title_default' => $this->getTable('downloadplus/link_product_item_title')),
    			'link_title_default.link_id=link.link_id AND link_title_default.store_id=0',
    			array())
    			->joinLeft(
    					array('link_title_store' => $this->getTable('downloadplus/link_product_item_title')),
    					'link_title_store.link_id=link.link_id AND link_title_store.store_id=' . intval($storeId),
    					array('title' => 'IFNULL(link_title_store.title, link_title_default.title)'))
    					->where('link.product_id=?', $productId);
    	if (!$searchData = $this->_getReadAdapter()->fetchCol($select)) {
    		$searchData = array();
    	}
    	return $searchData;
    }
    
}

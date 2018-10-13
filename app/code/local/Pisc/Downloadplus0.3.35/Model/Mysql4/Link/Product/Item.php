<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable link product item resource
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.2
 */

class Pisc_Downloadplus_Model_Mysql4_Link_Product_Item extends Mage_Core_Model_Mysql4_Abstract
{

    protected function  _construct()
    {
        $this->_init('downloadplus/link_product_item', 'link_id');
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
    	if ($where) {
    		$this->_getWriteAdapter()->delete(
    				$this->getTable('downloadplus/link_product_item'), $where);
    		$this->_getWriteAdapter()->delete(
    				$this->getTable('downloadplus/link_product_item_title'), $where);
    	}
    	return $this;
    }
    
}
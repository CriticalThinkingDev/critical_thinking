<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable links purchased items serial number resource collection
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.1
 */

class Pisc_Downloadplus_Model_Mysql4_Link_Purchased_Item_Serialnumber_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

	protected $_filter = Array();
	
    protected function _construct()
    {
    	$this->_init('downloadplus/link_purchased_item_serialnumber');
    }

    public function addSqlFilter($filter)
    {
    	if (is_null($filter)) {
    		$this->_filter = Array();
    	} else {
    		$this->_filter[] = $filter;
    	}
	    
	    return $this;
    }
    
    /*
     * Returns Array of Model Downloadplus_Link_Purchased_Item
     */
    public function getByOrderItemId($orderItemId)
    {
    	$result = Array();

    	$filter = '';
    	if (count($this->_filter)>0) {
    		$filter = ' AND '.implode(' AND ', $this->_filter);
    	}
    	
        $sql = $this->getSelect()
	            	->where("order_item_id='".$orderItemId."'".$filter);

        $collection = $this->getResource()->getReadConnection()->fetchAll($sql);
        foreach ($collection as $item) {
        	$result[] = Mage::getModel('downloadplus/link_purchased_item_serialnumber')->load($item['serial_id']);
        }

        return $result;
    }

    public function addPurchasedLinksToResult()
    {
    	$this->getSelect()
            ->joinLeft(Array('purchased_links'=>$this->getTable('downloadable/link_purchased')),
                	'`purchased_links`.purchased_id=`main_table`.purchased_id',
            		Array(
            			'purchased_links.order_increment_id'=>'order_increment_id',
            			'purchased_links.order_id'=>'order_id',
            			'purchased_links.customer_id'=>'customer_id',
            			'purchased_links.product_sku'=>'product_sku',
            			'purchased_links.product_name'=>'product_name',
            			'purchased_links.link_section_title'=>'link_section_title'
            		));
        return $this;
    }

    public function addOrderItemToResult($fields=null)
    {
    	if (is_array($fields)) {
    		$this->getSelect()
	            ->joinLeft(Array('order_item'=>$this->getTable('sales/order_item')),
	                	'`order_item`.item_id=`main_table`.order_item_id',
	            		$fields);
    	} else {
    		$this->getSelect()
	            ->joinLeft(Array('order_item'=>$this->getTable('sales/order_item')),
	                	'`order_item`.item_id=`main_table`.order_item_id');
    	}

        return $this;
    }

    public function addOrderToResult($fields=null)
    {
    	if (is_array($fields)) {
	   		$this->getSelect()
	            ->joinLeft(Array('order'=>$this->getTable('sales/order')),
	                	'`order`.entity_id=order_id',
	            		$fields);
    	} else {
	   		$this->getSelect()
	            ->joinLeft(Array('order'=>$this->getTable('sales/order')),
	                	'`order`.entity_id=order_id');
    	}

        return $this;
    }

}
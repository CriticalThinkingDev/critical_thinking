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
 * @version		0.1.3
 */

class Pisc_Downloadplus_Model_Mysql4_Link_Purchased_Item_Serialnumber_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
    	$this->_init('downloadplus/link_purchased_item_serialnumber');
    }

    /*
     * Returns Array of Model Downloadplus_Link_Purchased_Item
     */
    public function getByOrderItemId($id)
    {
    	$result = Array();

        $sql = $this->getSelect()
	            	->where("order_item_id='".$id."'");

        $collection = $this->getResource()->getReadConnection()->fetchAll($sql);
        foreach ($collection as $item) {
        	$result[] = Mage::getModel('downloadplus/link_purchased_item_serialnumber')->load($item['serial_id']);
        }

        return $result;
    }

    /*
     * Returns Array of Model Downloadplus_Link_Purchased_Item
     */
    public function getByPurchasedId($id)
    {
    	$result = Array();
    
    	$sql = $this->getSelect()
			    	->where("purchased_id='".$id."'");

    	$collection = $this->getResource()->getReadConnection()->fetchAll($sql);
    	foreach ($collection as $item) {
    		$result[] = Mage::getModel('downloadplus/link_purchased_item_serialnumber')->load($item['serial_id']);
    	}
    
    	return $result;
    }

    /*
     * Returns Array of Model Downloadplus_Link_Purchased_Item
     */
    public function getByPurchasedItemId($id)
    {
    	$result = Array();
    
    	$sql = $this->getSelect()
    				->where("purchased_item_id='".$id."'");
    
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

    	/* Add Customer Name and Email */
    	$firstNameAttr = Mage::getSingleton('customer/customer')->getResource()->getAttribute('firstname');
    	$lastNameAttr = Mage::getSingleton('customer/customer')->getResource()->getAttribute('lastname');
    	
    	$this->getSelect()
        	->joinLeft(
        	    Array('customerFirstnameTb' => $firstNameAttr->getBackend()->getTable()),
        	    'order.customer_id = customerFirstnameTb.entity_id AND customerFirstnameTb.attribute_id = '.$firstNameAttr->getId(). ' AND customerFirstnameTb.entity_type_id = '.Mage::getSingleton('customer/customer')->getResource()->getTypeId(),
        	    array('customerFirstnameTb.value')
        	);
    	 
    	$this->getSelect()
        	->joinLeft(
        	    Array('customerLastnameTb' => $lastNameAttr->getBackend()->getTable()),
        	    'order.customer_id = customerLastnameTb.entity_id AND customerLastnameTb.attribute_id = '.$lastNameAttr->getId(). ' AND customerLastnameTb.entity_type_id = '.Mage::getSingleton('customer/customer')->getResource()->getTypeId(),
        	    array('customer_name' => "CONCAT(customerFirstnameTb.value, ' ', customerLastnameTb.value)")
        	);

    	$emailAttr = Mage::getSingleton('customer/customer')->getResource()->getAttribute('email');
    	$this->getSelect()
        	->joinLeft(
        	    Array('customerEmailTb' => $emailAttr->getBackend()->getTable()),
        	    'order.customer_id = customerEmailTb.entity_id',
        	    array('customer_email'=>'email')
        	);
    	 
        return $this;
    }
    
}
<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable links purchased items resource collection
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.0
 */

class Pisc_Downloadplus_Model_Mysql4_Link_Customer_Item_Collection extends Mage_Downloadable_Model_Mysql4_Link_Purchased_Item_Collection
{

    protected function _construct()
    {
    	$this->_init('downloadplus/link_customer_item');
    }

    /*
     * Returns Array of Model Downloadplus_Link_Purchased_Item
     */
    public function getByOrderItemId($orderItemId)
    {
    	$result = Array();

        $sql = $this->getSelect()
	            	->where("order_item_id='".$orderItemId."'");

        $collection = $this->getResource()->getReadConnection()->fetchAll($sql);
        foreach ($collection as $item) {
        	$result[] = Mage::getModel('downloadplus/link_customer_item')->load($item['item_id']);
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

}
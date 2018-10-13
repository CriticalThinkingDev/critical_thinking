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
 * @version		0.1.0
 */

class Pisc_Downloadplus_Model_Mysql4_Link_Product_Item_Collection extends Mage_Downloadable_Model_Mysql4_Sample_Collection
{

    protected function _construct()
    {
    	$this->_init('downloadplus/link_product_item');
    }

    /*
     * Returns Array of Model Downloadplus_Link_Purchased_Item
     */
    public function getByProductId($productId)
    {
    	$result = Array();

        $sql = $this->getSelect()
	            	->where('product_id=?', $productId)
	            	->order('sort_order ASC');

        $collection = $this->getResource()->getReadConnection()->fetchAll($sql);
        foreach ($collection as $item) {
        	$result[] = Mage::getModel('downloadplus/link_product_item')->load($item['link_id']);
        }

        return $result;
    }

}
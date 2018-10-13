<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadplus Additional Product Downloadable Link Title model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 * @version		0.1.0
 */

class Pisc_Downloadplus_Model_Link_Product_Item_Title extends Mage_Core_Model_Abstract
{
	
	protected $_eventPrefix = 'downloadplus_link_product_item_title';
	
	protected $_filter = Array();
	
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('downloadplus/link_product_item_title', 'title_id');
        $this->_setResourceModel('downloadplus/link_product_item_title', 'downloadplus/link_product_item_title_collection');
    }

}

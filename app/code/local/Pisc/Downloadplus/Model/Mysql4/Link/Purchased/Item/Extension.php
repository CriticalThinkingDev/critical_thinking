<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable link extension resource
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.0
 */

class Pisc_Downloadplus_Model_Mysql4_Link_Purchased_Item_Extension extends Mage_Core_Model_Mysql4_Abstract
{

	protected $_filter = Array();
	
    protected function  _construct()
    {
        $this->_init('downloadplus/link_purchased_item_extension', 'id');
    }

    /*
     * Returns the resource ID by LinkId
     */
    public function getIdByItemId($id)
    {
    	$result = null;
    	if ($id) {
	    	$where = $this->_filter;
	    	$where[] = "item_id=".$id;

	        $sql = $this->_getReadAdapter()->select()
	        			->from($this->getTable('downloadplus/link_purchased_item_extension'))
	        			->where(implode(' AND ', $where));

	        $result = $this->_getReadAdapter()->fetchOne($sql);
    	}

        return $result;
    }

}

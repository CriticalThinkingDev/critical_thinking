<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable link purchased item resource
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.1
 */

class Pisc_Downloadplus_Model_Mysql4_Link_Purchased_Item extends Mage_Downloadable_Model_Mysql4_Link_Purchased_Item
{
	protected $_FK_CHECK_DISABLED = false;

	/*
    protected function  _construct()
    {
        $this->_init('downloadable/link_purchased_item', 'item_id');
    }
    */

    public function disableForeignKeyCheck()
    {
    	$this->_FK_CHECK_DISABLED = true;
    }
    
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
    	if ($this->_FK_CHECK_DISABLED) {
    		$adapter = $this->_getWriteAdapter();
    		$adapter->raw_query('SET foreign_key_checks = 0');
    	}
    }
    
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
    	if ($this->_FK_CHECK_DISABLED) {
    		$adapter = $this->_getWriteAdapter();
    		$adapter->raw_query('SET foreign_key_checks = 1');
    	}
    	$this->_FK_CHECK_DISABLED = false;
    }
    
}
